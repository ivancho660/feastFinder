<?php

namespace App\Http\Controllers;

use App\Models\productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL; // Agrega este use si no está arriba

class productosController extends Controller
{
    


public function index()
    {
        $productos = productos::all()->map(function ($producto) {
            if ($producto->imagen) {
                // CAMBIO AQUÍ: Usar la función url() de Laravel
                $producto->imagen = url('images/' . $producto->imagen); 
            }
            return $producto;
        });

        return response()->json($productos);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer|min:0',
            'codigoBarras' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'imagen' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'nombreR' => 'required|string|max:255',
            'precio' => 'required|double|min:0',
            'restaurante_id' => 'required|integer|min:0',
            'usuario_id' => 'required|integer|min:0'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $productos = productos::create($validator->validated());
        return response()->json($productos, 201);
    }

  
    public function show(string $id)
    {
         $productos = productos::find($id);
        if (!$productos) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        return response()->json($productos);
    }

  
    public function update(Request $request, string $id)
    {
        $productos = productos::find($id);
        if (!$productos) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'cantidad' => 'integer|min:0',
            'codigoBarras' => 'string|max:255',
            'descripcion' => 'string|max:255',
            'imagen' => 'string|max:255',
            'nombre' => 'string|max:255',
            'nombreR' => 'string|max:255',
            'precio' => 'double|min:0'
           
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $productos->update($validator->validated());
        return response()->json($productos);

    }

  
    public function destroy(string $id)
    {
        $productos = productos::find($id);
        if (!$productos) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        $productos->delete();
        return response()->json(['messge' => 'Producto eliminado con éxito']); 
    }
}
