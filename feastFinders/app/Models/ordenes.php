<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ordenes extends Model
{
    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'estado',
        'esatdoEntrega',
        'fechacreacion',
        'fechaentrega',
        'metodo_pago',
        'numero',
        'sesion_id',
        'total',
        'id_restaurante',
        'usuario_id'

    ];

    public function restaurante()
    {
        return $this->belongsTo(restaurantes::class, 'id_restaurante');
    }
    public function usuario()
    {
        return $this->belongsTo(usuarios::class, 'usuario_id');
    }

    public function detalle()
    {
        return $this->hasMany(detalles::class, 'orden_id');
    }
}
