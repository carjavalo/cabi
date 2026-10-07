<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Configuración de la Agenda Médica (un único registro): tiempo de atención
 * por cita y jornadas de mañana y tarde.
 */
class AgendaConfiguracion extends Model
{
    protected $table = 'agenda_configuracion';

    protected $fillable = ['duracion_minutos', 'manana_inicio', 'manana_fin', 'tarde_inicio', 'tarde_fin', 'updated_by'];

    /** Duraciones ofrecidas en la vista (minutos). */
    public const DURACIONES = [10, 15, 20, 25, 30, 40, 45, 60, 90, 120];

    /** Configuración vigente (o los valores por defecto si aún no existe). */
    public static function actual(): self
    {
        if (Schema::hasTable('agenda_configuracion') && ($c = static::query()->first())) {
            return $c;
        }

        return new static([
            'duracion_minutos' => 30,
            'manana_inicio'    => '07:00:00',
            'manana_fin'       => '12:30:00',
            'tarde_inicio'     => '14:00:00',
            'tarde_fin'        => '16:00:00',
        ]);
    }

    /** Jornadas como rangos en minutos desde la medianoche: [[inicio, fin], ...]. */
    public function jornadas(): array
    {
        $rangos = [[self::min($this->manana_inicio), self::min($this->manana_fin)]];
        if ($this->tarde_inicio && $this->tarde_fin) {
            $rangos[] = [self::min($this->tarde_inicio), self::min($this->tarde_fin)];
        }
        return array_values(array_filter($rangos, fn ($r) => $r[1] > $r[0]));
    }

    /** Turnos de la jornada según el tiempo de atención: [['hora' => 'HH:MM', 'fin' => 'HH:MM'], ...]. */
    public function turnos(): array
    {
        $dur = max(5, (int) $this->duracion_minutos);
        $out = [];
        foreach ($this->jornadas() as [$ini, $fin]) {
            for ($t = $ini; $t + $dur <= $fin; $t += $dur) {
                $out[] = ['hora' => self::hhmm($t), 'fin' => self::hhmm($t + $dur)];
            }
        }
        return $out;
    }

    /** ¿El intervalo [inicio, fin) cabe completo dentro de alguna jornada? */
    public function dentroDeJornada(int $inicio, int $fin): bool
    {
        foreach ($this->jornadas() as [$a, $b]) {
            if ($inicio >= $a && $fin <= $b) {
                return true;
            }
        }
        return false;
    }

    public function payload(): array
    {
        return [
            'duracion_minutos' => (int) $this->duracion_minutos,
            'manana_inicio'    => self::hhmm(self::min($this->manana_inicio)),
            'manana_fin'       => self::hhmm(self::min($this->manana_fin)),
            'tarde_inicio'     => $this->tarde_inicio ? self::hhmm(self::min($this->tarde_inicio)) : null,
            'tarde_fin'        => $this->tarde_fin ? self::hhmm(self::min($this->tarde_fin)) : null,
        ];
    }

    /** 'HH:MM[:SS]' → minutos desde la medianoche. */
    public static function min(?string $hora): int
    {
        if (!$hora) {
            return 0;
        }
        [$h, $m] = array_map('intval', explode(':', $hora) + [0, 0]);
        return $h * 60 + $m;
    }

    /** Minutos desde la medianoche → 'HH:MM'. */
    public static function hhmm(int $min): string
    {
        return sprintf('%02d:%02d', intdiv($min, 60), $min % 60);
    }
}
