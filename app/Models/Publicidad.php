<?php

namespace App\Models;

use App\Support\Archivos;
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

    /**
     * Accessor: enlace de la imagen del banner. Las nuevas están en el almacenamiento (S3,
     * con enlace temporal); las antiguas en public/img/publicidad se siguen mostrando.
     * Para la referencia guardada en la base use getRawOriginal('banner').
     */
    public function getBannerAttribute($value)
    {
        return empty($value) ? $value : Archivos::url($value);
    }
}
