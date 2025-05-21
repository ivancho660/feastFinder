<?php

namespace App\Http\Controllers;

use App\Models\usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class usuariosController extends Controller
{
    public $timestamps = false;

    public function index()
    {
        $usuarios = usuarios::all();
        return response()->json($usuarios);
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'direccion' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'resetToken' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'tokenExpiracion' => 'required|date|max:255',
            'username' => 'required|string|max:255'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $usuarios = usuarios::create($validator->validated());

        return response()->json($usuarios, 201);
    }

    public function show(string $id)
    {
        $usuarios = usuarios::find($id);
        if (!$usuarios){
            return response()->json(['message'=> 'usuario no encontrado'],404);
        }
        return response()->json($usuarios);
    }

    public function update(Request $request, string $id)
    {
        $usuarios = usuarios::find($id);
        if (!$usuarios){
            return response()->json(['message'=> 'usuario no encontrado'],404);
        }
        $validator = Validator::make($request->all(),[
            'direccion' => 'string|max:255',
            'email' => 'string|max:255',
            'estado' => 'string|max:255',
            'nombre' => 'string|max:255',
            'password' => 'string|max:255',
            'resetToken' => 'string|max:255',
            'telefono' => 'string|max:255',
            'tipo' => 'string|max:255',
            'tokenExpiracion' => 'date|max:255',
            'username' => 'string|max:255'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        $usuarios->update($validator->validated());
        return response()->json($usuarios);
    }

    public function destroy(string $id)
    {
        $usuarios = usuarios::find($id);
        if (!$usuarios){
            return response()->json(['message'=> 'usuario no encontrado'],404);
        }
        $usuarios->delete();
        return response()->json(['mensage' => 'usuario eliminada con exito']);
    }
}
