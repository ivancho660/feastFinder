<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ordenes extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = [
        'estado',
        'estadoEntrega',
        'fechacreacion',
        'fechaentrega',
        'metodo_pago',
        'numero',
        'session_id',
        'total',
        'id_restaurante',
        'usuario_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(usuarios::class, 'usuario_id');
    }

    public function restaurante()
    {
        return $this->belongsTo(restaurantes::class, 'id_restaurante');
    }

    public function detalle()
    {
        return $this->hasMany(detalles::class, 'orden_id');
    }
}
