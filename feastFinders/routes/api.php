<?php

use App\Http\Controllers\usuariosController;
use App\Http\Controllers\restaurantesController;
use App\Http\Controllers\UserController;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
Route::post('store',[usuariosController::class,'store']);
Route::post('login',[usuariosController::class,'login']);
=======
Route::post('registrar',[UserController::class,'registrar']);
Route::post('login',[UserController::class,'login']);
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a

//se usa para proteger
Route::middleware('jwt.auth')->group(function () {

Route::get('traerDatos',[UserController::class,'traerDatos']);
<<<<<<< HEAD
Route::get('cerrarSesion',[usuariosController::class,'cerrarSesion']);
=======
Route::get('cerrar',[UserController::class,'cerrar']);
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a

Route::get('listarUsuarios',[usuariosController::class,'index']);
Route::post('crearUsuarios',[usuariosController::class,'store']);
Route::get('listarRestaurantes',[restaurantesController::class,'index']);

});