<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalles extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = [
        'cantidad',
        'nombre',
        'precio',
        'total',
        'orden_id',
        'producto_id'
    ];

    public function producto()
    {
        return $this->belongsTo(productos::class, 'producto_id');
    }

    public function orden()
    {
        return $this->belongsTo(ordenes::class, 'orden_id');
    }
}
