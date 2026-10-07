<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Horario bloqueado en la Agenda Médica: en ese rango no se pueden agendar citas.
 */
class AgendaBloqueo extends Model
{
    protected $table = 'agenda_bloqueos';

    protected $fillable = ['fecha', 'hora_inicio', 'hora_fin', 'motivo', 'created_by'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function getInicioMinAttribute(): int
    {
        return AgendaConfiguracion::min($this->hora_inicio);
    }

    public function getFinMinAttribute(): int
    {
        return AgendaConfiguracion::min($this->hora_fin);
    }

    /** ¿Bloquea todo el día? */
    public function getDiaCompletoAttribute(): bool
    {
        return $this->inicio_min === 0 && $this->fin_min >= 23 * 60 + 59;
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
