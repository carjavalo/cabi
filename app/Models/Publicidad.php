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

    /**
     * Accessor: always return an absolute URL for the banner.
     * Strips any hardcoded host (e.g. http://192.168.x.x:port/) and
     * rebuilds using the current APP_URL so it works in any environment.
     */
    public function getBannerAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        // If it's already a full URL, extract only the relative path
        if (preg_match('#^https?://#i', $value)) {
            // Remove scheme + host + optional port + leading slash
            $relative = preg_replace('#^https?://[^/]+/?#i', '', $value);
        } else {
            $relative = ltrim($value, '/');
        }

        return asset($relative);
    }
}
