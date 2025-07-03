<?php

namespace App\Http\Controllers;

use App\Models\{Orden, DetalleOrden, Producto, Usuario, Restaurante};
use App\Services\{StripeService, NotificacionService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Auth};
use Illuminate\Support\Str;
use App\Models\productos;
use App\Models\restaurantes;
use App\Models\usuarios;
use App\Models\ordenes;
use App\Models\detalles;


class StripeController extends Controller
{
    protected $stripeService;
    protected $notificacionService;

    public function __construct(StripeService $stripeService, NotificacionService $notificacionService)
    {
        $this->stripeService = $stripeService;
        $this->notificacionService = $notificacionService;
    }

    public function checkout(Request $request)
    {
        DB::beginTransaction();
        try {
            $usuario = Auth::user();
            if (!$usuario) return response()->json(['error' => 'Usuario no autenticado'], 401);

            $detalles = $request->input('cart', []);
            if (empty($detalles)) return response()->json(['error' => 'El carrito está vacío'], 400);

            foreach ($detalles as $detalle) {
                $producto = productos::findOrFail($detalle['producto_id']);
                $cantidad = (int) $detalle['cantidad'];
                if ($cantidad <= 0 || $producto->cantidad < $cantidad) {
                    return response()->json([
                        'error' => "Stock insuficiente para {$producto->nombre}"
                    ], 400);
                }
            }

            $total = collect($detalles)->sum(fn($d) => $d['precio'] * $d['cantidad']);

            $orden = ordenes::create([
                'fechacreacion' => now(),
                'numero' => $this->generarNumeroOrden(),
                'usuario_id' => $usuario->id,
                'total' => $total,
                'estado' => 'PENDIENTE',
                'restaurante_id' => productos::find($detalles[0]['producto_id'])->restaurante_id,
                'estadoEntrega' => 'PENDIENTE',
                
            ]);

            foreach ($detalles as $data) {
                detalles::create([
                    'orden_id' => $orden->id,
                    'producto_id' => $data['producto_id'],
                    'cantidad' => $data['cantidad'],
                    'precio' => $data['precio'],
                ]);
            }

            $metadata = [
                'usuario_id' => $usuario->id,
                'orden_id' => $orden->id,
                'total' => number_format($total, 2),
                'productos' => collect($detalles)->map(fn($d) =>
                    "{$d['producto_id']}:{$d['cantidad']}:{$d['precio']}")->implode(';')
            ];

            $sessionData = $this->stripeService->createCheckoutSession(
                'Compra en MiTienda',
                count($detalles) . ' productos',
                $total,
                $metadata
            );

            $orden->session_id = $sessionData['session_id'];
            $orden->save();

            DB::commit();
            return response()->json($sessionData);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en checkout: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);

            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;
                $metadata = $session->metadata;

                $usuario = usuarios::findOrFail($metadata->usuario_id);

                $detalles = collect(explode(';', $metadata->productos))->map(function ($item) {
                    [$id, $cantidad, $precio] = explode(':', $item);
                    return [
                        'producto' => productos::findOrFail($id),
                        'cantidad' => $cantidad,
                        'precio' => $precio
                    ];
                });

                $orden = ordenes::create([
                    'fechacreacion' => now(),
                    'numero' => $this->generarNumeroOrden(),
                    'usuario_id' => $usuario->id,
                    'total' => $session->amount_total / 100,
                    'estado' => 'PAGADA',
                    'session_id' => $session->id,
                    'restaurante_id' => $detalles->first()['producto']->restaurante_id
                ]);

                foreach ($detalles as $d) {
                    detalles::create([
                        'orden_id' => $orden->id,
                        'producto_id' => $d['producto']->id,
                        'cantidad' => $d['cantidad'],
                        'precio' => $d['precio'],
                    ]);
                    $d['producto']->decrement('cantidad', $d['cantidad']);
                }

                $admin = restaurantes::find($orden->restaurante_id)?->administrador;
                if ($admin) {
                    $this->notificacionService->enviarNotificacionOrdenPagada(
                        $admin->id,
                        $orden->id,
                        $orden->numero,
                        $orden->restaurante_id
                    );
                }

                return response()->json(['message' => 'Orden pagada y notificada']);
            }

            return response()->json(['message' => 'Evento no manejado']);
        } catch (\Exception $e) {
            Log::error('Error en webhook: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generarNumeroOrden(){
        $ultimo = ordenes::max('id') ?? 0;
        $siguiente = $ultimo + 1;
        return 'ORD-' . str_pad($siguiente, 13, '0', STR_PAD_LEFT);
    }

    public function pagoExitoso(Request $request)
    {
        $sessionId = $request->query('session_id');
        Log::info("🔗 Acceso a /stripe/exitoso con session_id: {$sessionId}");

        try {
            if (!$sessionId) {
                Log::warning("⚠️ session_id no proporcionado en la URL");
                return response()->json([
                    'status' => 'error',
                    'message' => "No se pudo identificar la sesión de pago"
                ], 400);
            }

            $usuario = Auth::user();
            if (!$usuario) {
                Log::warning("🔒 Usuario no autenticado accediendo a /stripe/exitoso");
                return response()->json([
                    'status' => 'error',
                    'message' => "Usuario no autenticado"
                ], 401);
            }

            $orden = ordenes::where('session_id', $sessionId)->first();
            if (!$orden) {
                Log::warning("🔍 Orden no encontrada con session_id: {$sessionId}");
                return response()->json([
                    'status' => 'error',
                    'message' => "Orden no encontrada"
                ], 404);
            }

            if ($orden->usuario_id !== $usuario->id) {
                Log::warning("🚫 Usuario no autorizado para esta orden. Usuario actual: {$usuario->id} | Dueño: {$orden->usuario_id}");
                return response()->json([
                    'status' => 'error',
                    'message' => "Esta orden no pertenece al usuario actual"
                ], 403);
            }

            if ($orden->estado !== 'PAGADA') {
                $orden->estado = 'PAGADA';
                $orden->save();
                Log::info("💳 Orden #{$orden->numero} marcada como PAGADA");
            }

            $detalles = detalles::where('orden_id', $orden->id)->get();
            foreach ($detalles as $detalle) {
                $producto = productos::find($detalle->producto_id);
                $cantidad = intval($detalle->cantidad);

                if ($producto->cantidad < $cantidad) {
                    Log::warning("📦 Stock insuficiente para producto {$producto->id}");
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stock insuficiente para {$producto->nombre}"
                    ], 400);
                }

                $producto->cantidad -= $cantidad;
                $producto->save();
            }

            session()->forget('cart');

            return response()->json([
                'status' => 'success',
                'message' => 'Pago procesado correctamente.',
                'orden' => $orden,
                'detalles' => $detalles
            ]);
        } catch (\Exception $e) {
            Log::error("❌ Error en pagoExitoso: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => "Error al procesar el pago: " . $e->getMessage()
            ], 500);
        }
    }

    public function pagoCancelado(Request $request)
    {
        $sessionId = $request->query('session_id');
        Log::info("🔗 Acceso a /stripe/cancelado con session_id: {$sessionId}");

        try {
            if (!$sessionId) {
                return response()->json([
                    'status' => 'error',
                    'message' => "No se pudo identificar la sesión de pago"
                ], 400);
            }

            $usuario = Auth::user();
            if (!$usuario) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Usuario no autenticado"
                ], 401);
            }

            $orden = ordenes::where('session_id', $sessionId)->first();
            if ($orden && $orden->usuario_id === $usuario->id && $orden->estado !== 'CANCELADA') {
                $orden->estado = 'CANCELADA';
                $orden->save();
                Log::info("🛑 Orden #{$orden->numero} marcada como CANCELADA");
            }

            return response()->json([
                'status' => 'cancelled',
                'message' => 'Pago cancelado correctamente.',
                'sessionId' => $sessionId
            ]);
        } catch (\Exception $e) {
            Log::error("❌ Error en pagoCancelado: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => "Error al procesar cancelación: " . $e->getMessage()
            ], 500);
        }
    }

    public function error(Request $request)
    {
        $mensaje = $request->query('error', 'Ocurrió un error desconocido');
        return response()->json([
            'status' => 'error',
            'message' => $mensaje
        ]);
    }
}





