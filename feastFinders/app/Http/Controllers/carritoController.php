<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\productos;
use App\Models\ordenes;
use App\Models\detalles;
use App\Models\restaurantes;

class CarritoController extends Controller
{
    // Método para obtener datos iniciales (equivalente a home)
    public function getInitialData()
    {
        $data = [
            'restaurantes' => restaurantes::where('estado', 'activo')->get(),
            'productos_destacados' => Producto::where('cantidad', '>', 0)
                ->inRandomOrder()
                ->limit(12)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Método para mostrar un producto específico
    public function getProducto($id)
    {
        $producto = productos::find($id);
        
        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'producto' => $producto,
            'stock_disponible' => $producto->cantidad
        ]);
    }

    // Método para agregar al carrito
    public function addCart(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:0.1'
        ]);

        $userId = Auth::id();
        $carritoKey = "carrito_$userId";
        $ordenKey = "orden_$userId";

        $carrito = session()->get($carritoKey, []);
        $orden = session()->get($ordenKey, new ordenes());

        $producto = productos::find($request->id);

        // Verificar si el producto ya está en el carrito
        $index = collect($carrito)->search(function ($item) use ($request) {
            return $item['producto_id'] == $request->id;
        });

        if ($index !== false) {
            // Actualizar cantidad y total
            $carrito[$index]['cantidad'] += $request->cantidad;
            $carrito[$index]['total'] = $producto->precio * $carrito[$index]['cantidad'];
        } else {
            // Agregar nuevo detalle
            $carrito[] = [
                'producto_id' => $producto->id,
                'cantidad' => $request->cantidad,
                'precio' => $producto->precio,
                'nombre' => $producto->nombre,
                'total' => $producto->precio * $request->cantidad,
                'producto' => $producto
            ];
        }

        // Calcular total de la orden
        $sumaTotal = collect($carrito)->sum('total');
        $orden->total = $sumaTotal;

        // Guardar en sesión
        session()->put($carritoKey, $carrito);
        session()->put($ordenKey, $orden);

        return response()->json([
            'success' => true,
            'cart' => $carrito,
            'orden' => $orden
        ]);
    }

    // Método para eliminar del carrito
    public function deleteProductoCart($id)
    {
        $userId = Auth::id();
        $carritoKey = "carrito_$userId";
        $ordenKey = "orden_$userId";

        $carrito = session()->get($carritoKey, []);
        $orden = session()->get($ordenKey, new ordenes());

        // Filtrar el producto a eliminar
        $carrito = array_filter($carrito, function($item) use ($id) {
            return $item['producto_id'] != $id;
        });

        // Reindexar array
        $carrito = array_values($carrito);

        // Actualizar total
        $sumaTotal = collect($carrito)->sum('total');
        $orden->total = $sumaTotal;

        session()->put($carritoKey, $carrito);
        session()->put($ordenKey, $orden);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito',
            'cart' => $carrito,
            'orden' => $orden
        ]);
    }

    // Método para ver el carrito
    public function verCarrito()
    {
        $userId = Auth::id();
        $carritoKey = "carrito_$userId";
        $ordenKey = "orden_$userId";

        $carrito = session()->get($carritoKey, []);
        $orden = session()->get($ordenKey, new ordenes());

        if (empty($orden->total)) {
            $orden->total = 0;
        }

        return response()->json([
            'success' => true,
            'cart' => $carrito,
            'orden' => $orden
        ]);
    }

    // Método para el resumen de la orden
    public function order()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        $carritoKey = "carrito_$userId";
        $ordenKey = "orden_$userId";

        $carrito = session()->get($carritoKey, []);
        $orden = session()->get($ordenKey, new ordenes());

        $usuario = Auth::user();

        return response()->json([
            'success' => true,
            'cart' => $carrito,
            'orden' => $orden,
            'usuario' => $usuario
        ]);
    }

    // Método de búsqueda
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $productos = productos::where('nombre', 'LIKE', "%$query%")
            ->get();

        return response()->json([
            'success' => true,
            'productos' => $productos
        ]);
    }
}