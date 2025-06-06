<?php

use App\Http\Controllers\usuariosController;
use App\Http\Controllers\restaurantesController;
use App\Http\Controllers\UserController;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('store',[usuariosController::class,'store']);
Route::post('login',[usuariosController::class,'login']);

//se usa para proteger
Route::middleware('jwt.auth')->group(function () {

Route::get('traerDatos',[UserController::class,'traerDatos']);
Route::get('cerrarSesion',[usuariosController::class,'cerrarSesion']);

Route::get('listarUsuarios',[usuariosController::class,'index']);
Route::post('crearUsuarios',[usuariosController::class,'store']);
Route::get('listarRestaurantes',[restaurantesController::class,'index']);

});