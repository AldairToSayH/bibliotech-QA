<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'user_id',
        'prestamo_id',
        'monto',
        'fecha_pago',
        'fecha_habilitacion',
        'estado',
        'pagado_en',
    ];
}
