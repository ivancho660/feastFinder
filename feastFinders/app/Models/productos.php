<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productos extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = [
        'cantidad',
        'codigoBarras',
        'descripcion',
        'imagen',
        'nombre',
        'nombreR',
        'precio',
        'restaurante_id',
        'usuario_id'
        
    ];

    public function detalle()
    {
        return $this->hasMany(detalles::class, 'producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(usuarios::class, 'usuario_id');
    }

    public function restaurante()
    {
        return $this->belongsTo(restaurantes::class, 'restaurante_id');
    }
}
