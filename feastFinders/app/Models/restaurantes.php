<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a

class restaurantes extends Model
{
    public $timestamps = false;
<<<<<<< HEAD
=======

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    use HasFactory;
    protected $fillable = [
        'estado',
        'imagen',
        'nombre',
        'ubicacion',
<<<<<<< HEAD
        'Horario',
        'administrador_id'
        
    ];
=======
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
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a

    public function orden()
    {
        return $this->hasMany(ordenes::class, 'id_restaurante');
    }
<<<<<<< HEAD

    public function producto()
    {
        return $this->hasMany(productos::class, 'restaurante_id');
    }

    public function usuario()
    {
        return $this->belongsTo(usuarios::class, 'administrador_id');
    }
=======
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
}
