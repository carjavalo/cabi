<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capacitacion extends Model
{
    protected $table = 'capacitaciones';

    protected $fillable = [
        'titulo', 'descripcion', 'ubicacion', 'instructor',
        'fecha', 'hora_inicio', 'hora_fin',
        'capacidad_maxima', 'activo', 'created_by',
    ];

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
}
