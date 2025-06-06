<?php

namespace App\Http\Controllers;

use App\Models\detalles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class detallesController extends Controller
{

    public function index()
    {
        $detalles = detalles::all();
        return response()->json($detalles);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'cantidad' => 'required|double|min:0',
            'nombre' => 'required|string|max:255',
            'precio' => 'required|double|min:0',
            'total' => 'required|double|min:0',
            'orden_id' => 'required|integer|min:0',
            'producto_id' => 'required|integer|min:0'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $detalles = detalles::create($validator->validated());

        return response()->json($detalles, 201);
    }

    public function show(string $id)
    {
        $detalles = detalles::find($id);
        if (!$detalles){
            return response()->json(['message'=> 'detalle no encontrado'],404);
        }
        return response()->json($detalles);
    }

    public function update(Request $request, string $id)
    {
        $detalles = detalles::find($id);
        if (!$detalles){
            return response()->json(['message'=> 'detalle no encontrado'],404);
        }
        $validator = Validator::make($request->all(),[
            'cantidad' => 'double|min:0',
            'nombre' => 'string|max:255',
            'precio' => 'double|min:0',
            'total' => 'double|min:0',
            'orden_id' => 'integer|min:0',
            'producto_id' => 'integer|min:0'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        $detalles->update($validator->validated());
        return response()->json($detalles);
    }

    public function destroy(string $id)
    {
        $detalles = detalles::find($id);
        if (!$detalles){
            return response()->json(['message'=> 'detalle no encontrado'],404);
        }
        $detalles->delete();
        return response()->json(['mensage' => 'detalle eliminado con exito']);
    }
}