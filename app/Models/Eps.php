<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de EPS (Entidades Promotoras de Salud).
 * Se administra desde el paso 3 del Concepto Médico Ocupacional.
 */
class Eps extends Model
{
    protected $table = 'eps';

    protected $fillable = ['nombre', 'codigo', 'nit', 'activo'];

    protected $casts = ['activo' => 'boolean'];
}
