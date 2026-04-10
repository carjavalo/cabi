<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapacitacionSesion extends Model
{
    protected $table = 'capacitacion_sesiones';

    protected $fillable = [
        'capacitacion_id', 'token', 'fecha', 'hora_inicio', 'hora_fin', 'citados_ids',
    ];

    protected $casts = [
        'fecha' => 'date',
        'citados_ids' => 'array',
    ];

    public function capacitacion()
    {
        return $this->belongsTo(Capacitacion::class);
    }

    public function registros()
    {
        return $this->hasMany(CapacitacionAsistenciaRegistro::class, 'sesion_id');
    }

    /**
     * Verifica si la sesión está en ventana de tiempo para registrar asistencia.
     */
    public function estaAbierta(): bool
    {
        $ahora = now();
        $fechaSesion = $this->fecha->format('Y-m-d');
        $hoy = $ahora->format('Y-m-d');

        if ($hoy !== $fechaSesion) {
            return false;
        }

        if ($this->hora_inicio && $this->hora_fin) {
            $inicio = \Carbon\Carbon::parse($fechaSesion . ' ' . $this->hora_inicio);
            $fin = \Carbon\Carbon::parse($fechaSesion . ' ' . $this->hora_fin);
            return $ahora->between($inicio, $fin);
        }

        // Si no tiene horario, está abierta todo el día de la fecha
        return true;
    }
}
