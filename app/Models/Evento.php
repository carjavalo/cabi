<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'ubicacion',
        'fecha_inicio',
        'fecha_fin',
        'capacidad_maxima',
        'inscritos',
        'max_inscripciones_dia',
        'max_inscripciones_semana',
        'imagen',
        'activo',
        'dias_activos',
        'franjas_horarias',
        'created_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'activo' => 'boolean',
        'dias_activos' => 'array',
        'franjas_horarias' => 'array',
    ];

    public function eventoDias()
    {
        return $this->hasMany(EventoDia::class);
    }

    public function eventoFranjas()
    {
        return $this->hasMany(EventoFranja::class);
    }

    public function eventoInscripciones()
    {
        return $this->hasMany(EventoInscripcion::class);
    }
}
