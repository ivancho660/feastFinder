<?php

namespace App\Http\Controllers;

use App\Models\ordenes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ordenesController extends Controller
{

    public function index()
    {
        $ordenes = ordenes::all();
        return response()->json($ordenes);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'required|string|max:255',
            'estadoEntrega' => 'required|string|max:255',
            'fechacreacion' => 'required|date',
            'fechaentrega' => 'required|date',
            'metodo_pago' => 'required|string|max:255',
            'numero' => 'required|string|max:255',
            'session_id' => 'required|string|max:255',
            'total' => 'required|double|min:0',
            'id_restaurante' => 'required|integer|min:0',
            'usuario_id' => 'required|integer|min:0'
                                                                                                                            
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $ordenes = ordenes::create($validator->validated());
        return response()->json($ordenes, 201);
    }


    public function show(string $id)
    {
        $ordenes = ordenes::find($id);
        if (!$ordenes) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
        return response()->json($ordenes);
    }


    public function update(Request $request, string $id)
    {
        $ordenes = ordenes::find($id);
        if (!$ordenes) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
        $validator = Validator::make($request->all(), [
            'estado' => 'string|max:255',
            'estadoEntrega' => 'string|max:255',
            'fechacreacion' => 'datetime',
            'fechaentrega' => 'datetime',
            'metodo_pago' => 'string|max:255',
            'numero' => 'string|max:255',
            'session_id' => 'string|max:255',
            'total' => 'double|min:0'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $ordenes->update($validator->validated());
        return response()->json($ordenes);
    }


    public function destroy(string $id)
    {
        $ordenes = ordenes::find($id);
        if (!$ordenes) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
        $ordenes->delete();
        return response()->json(['messge' => 'Orden eliminada con éxito']); 
    }
}