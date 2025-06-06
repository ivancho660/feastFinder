<?php

namespace App\Http\Controllers;

use App\Models\ordenes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ordenesController extends Controller
{
<<<<<<< HEAD
=======

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    public function index()
    {
        $ordenes = ordenes::all();
        return response()->json($ordenes);
    }

<<<<<<< HEAD
=======

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
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
<<<<<<< HEAD

=======
                                                                                                                            
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $ordenes = ordenes::create($validator->validated());
        return response()->json($ordenes, 201);
    }

<<<<<<< HEAD
=======

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    public function show(string $id)
    {
        $ordenes = ordenes::find($id);
        if (!$ordenes) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
        return response()->json($ordenes);
    }

<<<<<<< HEAD
=======

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    public function update(Request $request, string $id)
    {
        $ordenes = ordenes::find($id);
        if (!$ordenes) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
        $validator = Validator::make($request->all(), [
            'estado' => 'string|max:255',
            'estadoEntrega' => 'string|max:255',
<<<<<<< HEAD
            'fechacreacion' => 'date',
            'fechaentrega' => 'date',
=======
            'fechacreacion' => 'datetime',
            'fechaentrega' => 'datetime',
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
            'metodo_pago' => 'string|max:255',
            'numero' => 'string|max:255',
            'session_id' => 'string|max:255',
            'total' => 'double|min:0'
        ]);
<<<<<<< HEAD
        
=======
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $ordenes->update($validator->validated());
        return response()->json($ordenes);
    }

<<<<<<< HEAD
=======

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    public function destroy(string $id)
    {
        $ordenes = ordenes::find($id);
        if (!$ordenes) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
        $ordenes->delete();
        return response()->json(['messge' => 'Orden eliminada con éxito']); 
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
