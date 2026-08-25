<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de ARL (Administradoras de Riesgos Laborales).
 * Se administra desde el paso 3 del Concepto Médico Ocupacional.
 */
class Arl extends Model
{
    protected $table = 'arls';

    protected $fillable = ['nombre', 'codigo', 'nit', 'activo'];

    protected $casts = ['activo' => 'boolean'];
}
