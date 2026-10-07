<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Cita de la Agenda Médica de Salud Ocupacional.
 */
class AgendaCita extends Model
{
    protected $table = 'agenda_citas';

    protected $fillable = [
        'user_id', 'fecha', 'hora', 'duracion_minutos', 'motivo', 'enfasis', 'telefono', 'email', 'cargo',
        'asistencia', 'estado', 'ingreso_matriz', 'observaciones', 'concepto_medico_id',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'fecha'   => 'date',
            'enfasis' => 'array',
        ];
    }

    public const ASISTENCIAS = [
        'pendiente'  => 'Pendiente',
        'en_espera'  => 'En sala',
        'asistio'    => 'Asistió',
        'no_asistio' => 'No asistió',
    ];

    public const ESTADOS = [
        'programada'  => 'Programada',
        'en_espera'   => 'En espera',
        'en_consulta' => 'En consulta',
        'completada'  => 'Completada',
        'cancelada'   => 'Cancelada',
    ];

    public const MATRIZ = [
        'no'         => 'No',
        'si'         => 'Sí',
        'en_proceso' => 'En proceso',
    ];

    /** Estados en los que la cita sigue ocupando el turno y espera atención. */
    public const ESTADOS_ACTIVOS = ['programada', 'en_espera', 'en_consulta'];

    /** Citas que ocupan su turno (todas menos las canceladas). */
    public function scopeOcupan(Builder $q): Builder
    {
        return $q->where('estado', '!=', 'cancelada');
    }

    /** Citas pendientes de atender (prioridad en el Concepto Médico). */
    public function scopePorAtender(Builder $q): Builder
    {
        return $q->whereIn('estado', self::ESTADOS_ACTIVOS)->where('asistencia', '!=', 'no_asistio');
    }

    /** Inicio de la cita en minutos desde la medianoche. */
    public function getInicioMinAttribute(): int
    {
        return AgendaConfiguracion::min($this->hora);
    }

    /** Fin de la cita (inicio + su propio tiempo de atención). */
    public function getFinMinAttribute(): int
    {
        return $this->inicio_min + max(5, (int) ($this->duracion_minutos ?: 30));
    }

    public function getHoraFinAttribute(): string
    {
        return AgendaConfiguracion::hhmm($this->fin_min);
    }

    public function getHoraCortaAttribute(): string
    {
        return $this->hora ? Carbon::parse($this->hora)->format('H:i') : '';
    }

    public function getMotivoLabelAttribute(): string
    {
        return ConceptoMedico::TIPOS[$this->motivo] ?? ($this->motivo ?: '—');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function concepto()
    {
        return $this->belongsTo(ConceptoMedico::class, 'concepto_medico_id');
    }
}
