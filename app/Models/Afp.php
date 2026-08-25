<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de AFP (fondos de pensiones y cesantías).
 * Se administra desde el paso 3 del Concepto Médico Ocupacional.
 */
class Afp extends Model
{
    protected $table = 'afps';

    protected $fillable = ['nombre', 'codigo', 'nit', 'activo'];

    protected $casts = ['activo' => 'boolean'];
}
