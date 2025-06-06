<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class usuarios extends Model implements AuthenticatableContract, JWTSubject
{
    use HasFactory, Authenticatable;

    public $timestamps = false;
    
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class usuarios extends Model
{
    public $timestamps = false;

    use HasFactory;
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    protected $fillable = [
        'direccion',
        'email',
        'estado',
<<<<<<< HEAD
        'imagen',
        'nombre',
        'password', // Asegúrate que este campo está incluido
=======
        'nombre',
        'password',
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
        'resetToken',
        'telefono',
        'tipo',
        'tokenExpiracion',
        'username'
    ];
<<<<<<< HEAD

    /**
     * Relación con restaurantes (como administrador)
     */
=======
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    public function restaurante()
    {
        return $this->hasMany(restaurantes::class, 'administrador_id');
    }
<<<<<<< HEAD

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
=======
    public function producto()
    {
        return $this->hasMany(prodcutos::class, 'usuario_id');
    }

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    public function orden()
    {
        return $this->hasMany(ordenes::class, 'usuario_id');
    }
<<<<<<< HEAD

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
=======
}
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
