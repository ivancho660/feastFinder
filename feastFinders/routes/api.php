<?php

use App\Http\Controllers\usuariosController;
use App\Http\Controllers\restaurantesController;
use App\Http\Controllers\UserController;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('registrar',[UserController::class,'registrar']);
Route::post('login',[UserController::class,'login']);

//se usa para proteger
Route::middleware('jwt.auth')->group(function () {

Route::get('traerDatos',[UserController::class,'traerDatos']);
Route::get('cerrar',[UserController::class,'cerrar']);

Route::get('listarUsuarios',[usuariosController::class,'index']);
Route::post('crearUsuarios',[usuariosController::class,'store']);
Route::get('listarRestaurantes',[restaurantesController::class,'index']);

});