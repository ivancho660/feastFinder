<?php

use App\Http\Controllers\usuariosController;
use App\Http\Controllers\restaurantesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rutas públicas (sin autenticación)
Route::post('crearUsuarios', [usuariosController::class, 'store']);
Route::post('login', [usuariosController::class, 'login']);
Route::get('/menu/{nr}', [usuariosController::class, 'traerMenu']);


// Rutas de recuperación de contraseña
Route::post('recuperacion/solicitar', [usuariosController::class, 'solicitarRecuperacion']);
Route::post('recuperacion/validar-codigo', [usuariosController::class, 'validarCodigoRecuperacion']);
Route::post('recuperacion/restablecer', [usuariosController::class, 'restablecerPassword']);

// Rutas protegidas por JWT
Route::middleware('jwt.auth')->group(function () {
    Route::get('traerDatos', [UserController::class, 'traerDatos']);
    Route::get('cerrarSesion', [usuariosController::class, 'cerrarSesion']);
    Route::get('listarUsuarios', [usuariosController::class, 'index']);
    Route::get('listarRestaurantes', [restaurantesController::class, 'index']);
    
});