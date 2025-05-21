<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class usuarios extends Model
{
    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'direccion',
        'email',
        'estado',
        'nombre',
        'password',
        'resetToken',
        'telefono',
        'tipo',
        'tokenExpiracion',
        'username'
    ];
    public function restaurante()
    {
        return $this->hasMany(restaurantes::class, 'administrador_id');
    }
    public function producto()
    {
        return $this->hasMany(prodcutos::class, 'usuario_id');
    }

    public function orden()
    {
        return $this->hasMany(ordenes::class, 'usuario_id');
    }
}
