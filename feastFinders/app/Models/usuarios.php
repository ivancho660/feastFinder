<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class usuarios extends Model implements AuthenticatableContract, JWTSubject
{
    use HasFactory, Authenticatable;

    public $timestamps = false;
    
    protected $fillable = [
        'direccion',
        'email',
        'estado',
        'imagen',
        'nombre',
        'password', // Asegúrate que este campo está incluido
        'resetToken',
        'telefono',
        'tipo',
        'tokenExpiracion',
        'username'
    ];

    /**
     * Relación con restaurantes (como administrador)
     */
    public function restaurante()
    {
        return $this->hasMany(restaurantes::class, 'administrador_id');
    }

    /**
     * Relación con productos
     */
    public function producto()
    {
        return $this->hasMany(productos::class, 'usuario_id');
    }

    /**
     * Relación con órdenes
     */
    public function orden()
    {
        return $this->hasMany(ordenes::class, 'usuario_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            // Puedes agregar claims personalizados aquí
            'tipo' => $this->tipo,
            'email' => $this->email,
            'nombre' => $this->nombre
        ];
    }

    /**
     * Ocultar campos sensibles en las respuestas JSON
     */
    protected $hidden = [
        'password',
        'resetToken',
        'tokenExpiracion'
    ];
}
