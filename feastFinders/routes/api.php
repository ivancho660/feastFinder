<?php

use App\Http\Controllers\usuariosController;
use App\Http\Controllers\restaurantesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;

// Rutas públicas (sin autenticación)
Route::post('registrar', [usuariosController::class, 'store']);
Route::post('login', [usuariosController::class, 'login']);
Route::get('/menu/{nr}', [usuariosController::class, 'traerMenu']);


// Rutas de recuperación de contraseña
Route::post('recuperacion/solicitar', [usuariosController::class, 'solicitarRecuperacion']);
Route::post('recuperacion/validar-codigo', [usuariosController::class, 'validarCodigoRecuperacion']);
Route::post('recuperacion/restablecer', [usuariosController::class, 'restablecerPassword']);

// Rutas protegidas por JWT
Route::middleware('jwt.auth')->group(function () {
    Route::get('traerDatos', [UsuariosController::class, 'perfil']);
    Route::get('cerrarSesion', [usuariosController::class, 'cerrarSesion']);
    Route::get('listarUsuarios', [usuariosController::class, 'index']);
    Route::get('listarRestaurantes', [restaurantesController::class, 'index']);

     // Rutas para Stripe (protegidas por JWT)
    Route::post('stripe/checkout', [StripeController::class, 'checkout']);
    Route::get('stripe/exitoso', [StripeController::class, 'pagoExitoso']);
    Route::get('stripe/cancelado', [StripeController::class, 'pagoCancelado']);
    Route::get('stripe/error', [StripeController::class, 'errorPago']);   
});

// Ruta para webhook de Stripe (debe ser pública ya que Stripe la llama directamente)
Route::post('stripe/webhook', [StripeController::class, 'handleWebhook']);