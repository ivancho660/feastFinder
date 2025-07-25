<?php

use App\Http\Controllers\usuariosController;
use App\Http\Controllers\restaurantesController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\CarritoController;
use Illuminate\Support\Facades\Route;

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
    // Usuarios
    Route::get('traerDatos', [usuariosController::class, 'perfil']);
    Route::get('cerrarSesion', [usuariosController::class, 'cerrarSesion']);
    Route::get('listarUsuarios', [usuariosController::class, 'index']);
    
    // Restaurantes
    Route::get('listarRestaurantes', [restaurantesController::class, 'index']);
    
    // Stripe
    Route::post('stripe/checkout', [StripeController::class, 'checkout']);
    Route::get('stripe/exitoso', [StripeController::class, 'pagoExitoso']);
    Route::get('stripe/cancelado', [StripeController::class, 'pagoCancelado']);
    Route::get('stripe/error', [StripeController::class, 'errorPago']);
    
    // Carrito - Actualizado para coincidir con los métodos del controlador
    Route::post('/carrito/agregar', [CarritoController::class, 'addCart']); // Cambiado a addCart
    Route::delete('/carrito/eliminar/{id}', [CarritoController::class, 'deleteProductoCart']); // Cambiado a deleteProductoCart
    Route::get('/carrito/ver', [CarritoController::class, 'verCarrito']);
    Route::get('/carrito/resumen', [CarritoController::class, 'order']); // Cambiado a order
    
    // Nuevas rutas adicionales de tu controlador
    Route::get('inicio', [CarritoController::class, 'getInitialData']);
    Route::get('productos/{id}', [CarritoController::class, 'getProducto']);
    Route::post('productos/buscar', [CarritoController::class, 'search']);
});

// Webhook de Stripe
Route::post('stripe/webhook', [StripeController::class, 'handleWebhook']);