<?php

namespace App\Http\Controllers;

use App\Models\{ordenes, detalles, productos, restaurantes, usuarios};
use App\Services\{StripeService, NotificacionService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Auth};

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

            $agrupado = collect($detalles)->groupBy(function ($item) {
                // Asegúrate de que el producto exista antes de intentar acceder a su restaurante_id
                $producto = productos::find($item['producto_id']);
                return $producto ? $producto->restaurante_id : null;
            })->filter()->all(); // Filtra cualquier grupo nulo si un producto no se encuentra

            $totalGeneral = 0;
            $ordenesCreadas = [];

            foreach ($agrupado as $restauranteId => $items) {
                $totalOrden = collect($items)->sum(fn($d) => $d['precio'] * $d['cantidad']);
                $totalGeneral += $totalOrden;

                $orden = ordenes::create([
                    'fechacreacion' => now(),
                    'numero' => $this->generarNumeroOrden(),
                    'usuario_id' => $usuario->id,
                    'total' => $totalOrden,
                    'estado' => 'PENDIENTE',
                    'estadoEntrega' => 'PENDIENTE',
                    'id_restaurante' => $restauranteId, // ¡CAMBIO AQUÍ! Usar 'id_restaurante'
                ]);

                foreach ($items as $item) {
                    detalles::create([
                        'orden_id' => $orden->id,
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $item['cantidad'],
                        'precio' => $item['precio'],
                    ]);
                }

                $ordenesCreadas[] = $orden;
            }

            $metadata = [
                'usuario_id' => $usuario->id,
                'ordenes_ids' => collect($ordenesCreadas)->pluck('id')->implode(','),
                'total' => number_format($totalGeneral, 2),
            ];

            if (!empty($ordenesCreadas)) {
                $metadata['orden_id'] = $ordenesCreadas[0]->id;
            }

            $sessionData = $this->stripeService->createCheckoutSession(
                'Compra en MiTienda',
                count($detalles) . ' productos',
                $totalGeneral,
                $metadata
            );

            foreach ($ordenesCreadas as $orden) {
                $orden->session_id = $sessionData['session_id'];
                $orden->save();
            }

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
                $metadata = $session->metadata ?? [];

                $usuarioId = $metadata->usuario_id ?? null;
                $ordenesIdsStr = $metadata->ordenes_ids ?? '';

                if (!$usuarioId || !$ordenesIdsStr) {
                    return response()->json(['error' => 'Metadata incompleta'], 400);
                }

                $ordenesIds = explode(',', $ordenesIdsStr);

                foreach ($ordenesIds as $ordenId) {
                    $orden = ordenes::find($ordenId);
                    if (!$orden || $orden->estado === 'PAGADA') continue;

                    $orden->estado = 'PAGADA';
                    $orden->save();

                    $detalles = detalles::where('orden_id', $orden->id)->get();

                    foreach ($detalles as $detalle) {
                        $producto = productos::find($detalle->producto_id);
                        if ($producto) {
                            $producto->decrement('cantidad', $detalle->cantidad);
                        }
                    }

                    $admin = restaurantes::find($orden->id_restaurante)?->administrador; // Usar id_restaurante
                    if ($admin) {
                        $this->notificacionService->enviarNotificacionOrdenPagada(
                            $admin->id,
                            $orden->id,
                            $orden->numero,
                            $orden->id_restaurante // Usar id_restaurante
                        );
                    }
                }

                return response()->json(['message' => 'Ordenes pagadas y notificadas']);
            }

            return response()->json(['message' => 'Evento no manejado']);
        } catch (\Exception $e) {
            Log::error('Error en webhook: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generarNumeroOrden()
    {
        $ultimo = ordenes::max('id') ?? 0;
        $siguiente = $ultimo + 1;
        return 'ORD-' . str_pad($siguiente, 13, '0', STR_PAD_LEFT);
    }

    public function pagoExitoso(Request $request)
    {
        $sessionId = $request->query('session_id');
        Log::info("✅ Acceso a /stripe/exitoso con session_id: {$sessionId}");

        try {
            if (!$sessionId) {
                return response()->json(['status' => 'error', 'message' => 'Falta session_id'], 400);
            }

            $usuario = Auth::user();
            if (!$usuario) {
                return response()->json(['status' => 'error', 'message' => 'Usuario no autenticado'], 401);
            }

            $ordenes = ordenes::where('session_id', $sessionId)->where('usuario_id', $usuario->id)->get();
            if ($ordenes->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Ordenes no encontradas'], 404);
            }

            foreach ($ordenes as $orden) {
                if ($orden->estado !== 'PAGADA') {
                    $orden->estado = 'PAGADA';
                    $orden->save();

                    $detalles = detalles::where('orden_id', $orden->id)->get();
                    foreach ($detalles as $detalle) {
                        $producto = productos::find($detalle->producto_id);
                        if ($producto && $producto->cantidad >= $detalle->cantidad) {
                            $producto->cantidad -= $detalle->cantidad;
                            $producto->save();
                        } else {
                            // Esto ya debería ser manejado en el checkout, pero es una buena validación de respaldo
                            return response()->json([
                                'status' => 'error',
                                'message' => "Stock insuficiente para {$producto->nombre}"
                            ], 400);
                        }
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pago procesado correctamente.',
                'ordenes' => $ordenes
            ]);
        } catch (\Exception $e) {
            Log::error("❌ Error en pagoExitoso: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => "Error: " . $e->getMessage()], 500);
        }
    }

    public function pagoCancelado(Request $request)
    {
        $sessionId = $request->query('session_id');

        try {
            if (!$sessionId) return response()->json(['status' => 'error', 'message' => 'Falta session_id'], 400);

            $usuario = Auth::user();
            if (!$usuario) return response()->json(['status' => 'error', 'message' => 'Usuario no autenticado'], 401);

            $ordenes = ordenes::where('session_id', $sessionId)->where('usuario_id', $usuario->id)->get();

            foreach ($ordenes as $orden) {
                if ($orden->estado !== 'CANCELADA') {
                    $orden->estado = 'CANCELADA';
                    $orden->save();
                }
            }

            return response()->json([
                'status' => 'cancelled',
                'message' => 'Pago cancelado correctamente.',
                'sessionId' => $sessionId
            ]);
        } catch (\Exception $e) {
            Log::error("❌ Error en pagoCancelado: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => "Error: " . $e->getMessage()], 500);
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