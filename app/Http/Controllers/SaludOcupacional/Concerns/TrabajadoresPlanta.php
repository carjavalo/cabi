<?php

namespace App\Http\Controllers\SaludOcupacional\Concerns;

use App\Models\User;
use App\Models\Vinculacion;
use App\Support\GestorPermisos;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Reglas compartidas del módulo de Salud Ocupacional (Concepto Médico y
 * Agenda Médica): acceso al módulo y trabajadores atendibles (vinculación
 * "Planta").
 */
trait TrabajadoresPlanta
{
    /** Solo los trabajadores con esta vinculación pueden ser atendidos como pacientes. */
    public const VINCULACION_ATENDIBLE = 'Planta';

    /** Cache del id de la vinculación "Planta" (columna tipo_vinculacion_id). */
    private ?int $plantaId = null;
    private bool $plantaIdResolved = false;

    /**
     * Control de acceso del módulo de Salud Ocupacional.
     *
     * El acceso se otorga según los permisos configurados por rol en Gestión de
     * Permisos (GestorPermisos): "Super Admin" siempre entra y los demás roles
     * entran si el administrador les asignó la función correspondiente.
     */
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                if (!GestorPermisos::puedeRuta(Auth::user(), $request->route())) {
                    abort(403, 'Acceso restringido al módulo de Salud Ocupacional.');
                }
                return $next($request);
            },
        ];
    }

    /**
     * ¿El trabajador es de vinculación "Planta"? Contempla tanto el nombre
     * textual (tipo_vinculacion) como el id (tipo_vinculacion_id), pues en la
     * base de datos existen registros con uno u otro.
     */
    protected function esPlanta(User $u): bool
    {
        if (strcasecmp(trim((string) $u->tipo_vinculacion), self::VINCULACION_ATENDIBLE) === 0) {
            return true;
        }
        $pid = $this->plantaId();
        return $pid !== null && (int) $u->tipo_vinculacion_id === $pid;
    }

    /** Restringe una consulta de usuarios a los de vinculación "Planta". */
    protected function soloPlanta(Builder $query): Builder
    {
        $pid = $this->plantaId();
        return $query->where(function ($w) use ($pid) {
            $w->whereRaw('LOWER(TRIM(tipo_vinculacion)) = ?', [strtolower(self::VINCULACION_ATENDIBLE)]);
            if ($pid !== null) {
                $w->orWhere('tipo_vinculacion_id', $pid);
            }
        });
    }

    /** Nombre de la vinculación: texto guardado o, en su defecto, el del catálogo. */
    protected function vinculacionNombre(User $u): ?string
    {
        if (trim((string) $u->tipo_vinculacion) !== '') {
            return $u->tipo_vinculacion;
        }
        try {
            return $u->tipo_vinculacion_id ? optional(Vinculacion::find($u->tipo_vinculacion_id))->nombre : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Id de la vinculación "Planta" en el catálogo (o null si no existe). */
    protected function plantaId(): ?int
    {
        if (!$this->plantaIdResolved) {
            $this->plantaIdResolved = true;
            try {
                $this->plantaId = optional(
                    Vinculacion::whereRaw('LOWER(nombre) = ?', [strtolower(self::VINCULACION_ATENDIBLE)])->first()
                )->id;
            } catch (\Throwable $e) {
                $this->plantaId = null;
            }
        }
        return $this->plantaId;
    }
}
