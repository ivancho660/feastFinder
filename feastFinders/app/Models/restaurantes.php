<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class restaurantes extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = [
        'estado',
        'imagen',
        'nombre',
        'ubicacion',
        'Horario',
        'administrador_id'
        
    ];

    public function orden()
    {
        return $this->hasMany(ordenes::class, 'id_restaurante');
    }

    public function producto()
    {
        return $this->hasMany(productos::class, 'restaurante_id');
    }

    public function usuario()
    {
        return $this->belongsTo(usuarios::class, 'administrador_id');
    }
}
