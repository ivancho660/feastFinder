<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Str;

class UsuarioService
{
    /**
     * Genera y guarda un token de recuperación para el usuario
     */
    public function generarTokenRecuperacion(string $email): void
    {
        $usuario = Usuario::where('email', $email)->firstOrFail();
        $usuario->reset_token = Str::random(60); // Genera un token aleatorio
        $usuario->save();
    }

    /**
     * Busca un usuario por email (equivalente al findByEmail de Java)
     */
    public function findByEmail(string $email): ?Usuario
    {
        return Usuario::where('email', $email)->first();
    }
}