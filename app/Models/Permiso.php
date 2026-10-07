<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Función del aplicativo que se puede asignar a los roles (ver App\Support\GestorPermisos).
 */
class Permiso extends Model
{
    protected $table = 'permisos';

    protected $fillable = [
        'clave', 'nombre', 'modulo', 'descripcion', 'tipo', 'metodo', 'uri',
        'origen', 'activo', 'nuevo', 'huerfano', 'editado',
    ];

    protected $casts = [
        'activo'   => 'boolean',
        'nuevo'    => 'boolean',
        'huerfano' => 'boolean',
        'editado'  => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permiso_role')->withTimestamps();
    }
}
