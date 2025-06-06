<?php

namespace App\Http\Controllers;

use App\Models\restaurantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class restaurantesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurantes = restaurantes::all();
        return response()->json($restaurantes);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'estado' => 'required|string|max:255',
            'imagen' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'administrador_id' => 'required|integer|min:0',
            'Horario' => 'required|string|max:255'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $restaurantes = restaurantes::create($validator->validated());

        return response()->json($restaurantes, 201);
    }

    public function show(string $id)
    {
        $restaurantes = restaurantes::find($id);
        if (!$restaurantes){
            return response()->json(['message'=> 'resaturante no encontrado'],404);
        }
        return response()->json($restaurantes);
    }


    public function update(Request $request, string $id)
    {
        $restaurantes = restaurantes::find($id);
        if (!$restaurantes){
            return response()->json(['message'=> 'resaturante no encontrado'],404);
        }
        $validator = Validator::make($request->all(),[
            'estado' => 'string|max:255',
            'imagen' => 'string|max:255',
            'nombre' => 'string|max:255',
            'ubicacion' => 'string|max:255',
            'administrador_id' => 'integer|min:0',
            'Horario' => 'string|max:255'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        $restaurantes->update($validator->validated());
        return response()->json($restaurantes);
    }

    
    public function destroy(string $id)
    {
        $restaurantes = restaurantes::find($id);
        if (!$restaurantes){
            return response()->json(['message'=> 'resaturante no encontrado'],404);
        }
        $restaurantes->delete();
        return response()->json(['mensage' => 'resaturante eliminado con exito']);
    }
}