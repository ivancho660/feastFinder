<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a

class ordenes extends Model
{
    public $timestamps = false;
<<<<<<< HEAD
    use HasFactory;
    protected $fillable = [
        'estado',
        'estadoEntrega',
=======

    use HasFactory;
    protected $fillable = [
        'estado',
        'esatdoEntrega',
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
        'fechacreacion',
        'fechaentrega',
        'metodo_pago',
        'numero',
<<<<<<< HEAD
        'session_id',
        'total',
        'id_restaurante',
        'usuario_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(usuarios::class, 'usuario_id');
    }
=======
        'sesion_id',
        'total',
        'id_restaurante',
        'usuario_id'

    ];
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a

    public function restaurante()
    {
        return $this->belongsTo(restaurantes::class, 'id_restaurante');
    }
<<<<<<< HEAD
=======
    public function usuario()
    {
        return $this->belongsTo(usuarios::class, 'usuario_id');
    }
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a

    public function detalle()
    {
        return $this->hasMany(detalles::class, 'orden_id');
    }
<<<<<<< HEAD

=======
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
}
