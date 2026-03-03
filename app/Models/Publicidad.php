<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicidad extends Model
{
    protected $table = 'publicidad';

    protected $fillable = [
        'titulo',
        'tag',
        'descripcion',
        'banner',
        'link',
        'seccion_titulo',
        'seccion_subtitulo',
        'prioridad',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];
}
