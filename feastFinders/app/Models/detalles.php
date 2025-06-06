<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a

class detalles extends Model
{
    public $timestamps = false;
<<<<<<< HEAD
=======

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    use HasFactory;
    protected $fillable = [
        'cantidad',
        'nombre',
        'precio',
        'total',
        'orden_id',
        'producto_id'
<<<<<<< HEAD
    ];

    public function producto()
    {
        return $this->belongsTo(productos::class, 'producto_id');
    }

=======

    ];
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    public function orden()
    {
        return $this->belongsTo(ordenes::class, 'orden_id');
    }
<<<<<<< HEAD
=======
     public function producto()
    {
        return $this->belongsTo(productos::class, 'producto_id');
    }
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
}
