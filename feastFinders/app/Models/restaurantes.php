<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class restaurantes extends Model
{
    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'estado',
        'imagen',
        'nombre',
        'ubicacion',
        'administrador_id',
        'Horario'

    ];
public function usuario()
    {
        return $this->belongsTo(usuarios::class, 'administrador_id');
    }

    public function producto()
    {
        return $this->hasMany(prodcutos::class, 'restaurante_id');
    }

    public function orden()
    {
        return $this->hasMany(ordenes::class, 'id_restaurante');
    }
}
