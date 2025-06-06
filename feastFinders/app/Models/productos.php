<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a

class productos extends Model
{
    public $timestamps = false;
<<<<<<< HEAD
=======

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
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
<<<<<<< HEAD
        
    ];

    public function detalle()
    {
        return $this->hasMany(detalles::class, 'producto_id');
    }

=======
    ];
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    public function usuario()
    {
        return $this->belongsTo(usuarios::class, 'usuario_id');
    }

    public function restaurante()
    {
        return $this->belongsTo(restaurantes::class, 'restaurante_id');
    }
<<<<<<< HEAD
=======

    public function detalle()
    {
        return $this->hasMany(detalles::class, 'producto_id');
    }

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
}
