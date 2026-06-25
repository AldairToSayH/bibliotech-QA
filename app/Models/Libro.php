<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Libro extends Model
{
    protected $fillable = [
        'titulo',
        'autor',
        'isbn',
        'estado',
    ];

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }
}
