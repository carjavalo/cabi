<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Capacitacion extends Model
{
    protected $table = 'capacitaciones';

    protected $fillable = [
        'titulo', 'descripcion', 'ubicacion', 'instructor',
        'fecha', 'hora_inicio', 'hora_fin',
        'capacidad_maxima', 'activo', 'created_by', 'token',
    ];

    protected static function booted(): void
    {
        static::creating(function (Capacitacion $cap) {
            if (empty($cap->token)) {
                $cap->token = Str::random(32);
            }
        });
    }

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function asistencias()
    {
        return $this->hasMany(CapacitacionAsistencia::class);
    }

    public function asistentes()
    {
        return $this->belongsToMany(User::class, 'capacitacion_asistencias')
                     ->withPivot('asistio', 'observacion')
                     ->withTimestamps();
    }

    public function sesiones()
    {
        return $this->hasMany(CapacitacionSesion::class);
    }

    public function ultimaSesion()
    {
        return $this->hasOne(CapacitacionSesion::class)->latestOfMany();
    }

    public function registrosAsistencia()
    {
        return $this->hasMany(CapacitacionAsistenciaRegistro::class);
    }
}
