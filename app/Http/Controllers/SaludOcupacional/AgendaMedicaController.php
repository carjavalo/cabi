<?php

namespace App\Http\Controllers\SaludOcupacional;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaludOcupacional\Concerns\TrabajadoresPlanta;
use App\Models\AgendaBloqueo;
use App\Models\AgendaCita;
use App\Models\AgendaConfiguracion;
use App\Models\Cargo;
use App\Models\ConceptoMedico;
use App\Models\Eps;
use App\Models\Afp;
use App\Models\Arl;
use App\Models\Servicio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/**
 * Agenda Médica de Salud Ocupacional: programación de citas de los
 * trabajadores de vinculación Planta. Las citas agendadas tienen prioridad
 * en el Concepto Médico.
 */
class AgendaMedicaController extends Controller implements HasMiddleware
{
    use TrabajadoresPlanta;

    public function index(Request $request)
    {
        $fecha = $this->fechaValida($request->query('fecha'));

        return view('salud_ocupacional.agenda.index', [
            'fecha'       => $fecha,
            'config'      => AgendaConfiguracion::actual()->payload(),
            'duraciones'  => AgendaConfiguracion::DURACIONES,
            'motivos'     => ConceptoMedico::TIPOS,
            'enfasis'     => ConceptoMedico::ENFASIS,
            'asistencias' => AgendaCita::ASISTENCIAS,
            'estados'     => AgendaCita::ESTADOS,
            'matriz'      => AgendaCita::MATRIZ,
            'tiposIdentificacion' => ConceptoMedicoController::TIPOS_IDENTIFICACION,
            'cargos'      => $this->safeList(fn () => Cargo::orderBy('nombre')->pluck('nombre')),
            'servicios'   => $this->safeList(fn () => Servicio::orderBy('nombre')->pluck('nombre')),
            'epsList'     => $this->safeList(fn () => Eps::where('activo', true)->orderBy('nombre')->pluck('nombre')),
            'afpList'     => $this->safeList(fn () => Afp::where('activo', true)->orderBy('nombre')->pluck('nombre')),
            'arlList'     => $this->safeList(fn () => Arl::where('activo', true)->orderBy('nombre')->pluck('nombre')),
            'migracionPendiente' => !Schema::hasTable('agenda_citas') || !Schema::hasTable('agenda_bloqueos'),
        ]);
    }

    /** Citas de un día (JSON) con sus indicadores. */
    public function citas(Request $request)
    {
        $fecha = $this->fechaValida($request->query('fecha'));

        $citas = AgendaCita::with('user')
            ->whereDate('fecha', $fecha)
            ->orderBy('hora')
            ->orderBy('id')
            ->get();

        $activas = $citas->where('estado', '!=', 'cancelada');
        $bloqueos = AgendaBloqueo::whereDate('fecha', $fecha)->orderBy('hora_inicio')->get();
        $turnos = $this->turnosDelDia($activas, $bloqueos);
        $completadas = $citas->where('estado', 'completada');

        return response()->json([
            'fecha' => $fecha,
            'citas' => $citas->map(fn (AgendaCita $c) => $this->citaPayload($c))->values(),
            'turnos' => $turnos,
            'bloqueos' => $bloqueos->map(fn (AgendaBloqueo $b) => $this->bloqueoPayload($b))->values(),
            'config' => AgendaConfiguracion::actual()->payload(),
            'kpis'  => [
                'total'      => $activas->count(),
                'atendidos'  => $completadas->count(),
                'en_espera'  => $activas->filter(fn ($c) => $c->estado === 'en_espera' || $c->asistencia === 'en_espera')->count(),
                'matriz_pct' => $completadas->count()
                    ? (int) round($completadas->where('ingreso_matriz', 'si')->count() * 100 / $completadas->count())
                    : 0,
                'turnos'     => count(array_filter($turnos, fn ($t) => !$t['bloqueo'])),
            ],
        ]);
    }

    /**
     * Busca trabajadores de vinculación Planta por número de identificación
     * (prefijo) o por nombres y apellidos, para agendarlos.
     */
    public function pacientes(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 3) {
            return response()->json(['items' => []]);
        }

        $query = $this->soloPlanta(User::query());
        if (preg_match('/^[\d.\s-]+$/', $q)) {
            $query->where('identificacion', 'like', preg_replace('/\D/', '', $q) . '%');
        } else {
            foreach (preg_split('/\s+/', $q) as $word) {
                $like = '%' . $word . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                      ->orWhere('apellido1', 'like', $like)
                      ->orWhere('apellido2', 'like', $like);
                });
            }
        }

        $items = $query->orderBy('name')->take(10)->get()->map(fn (User $u) => [
            'id'              => $u->id,
            'identificacion'  => $u->identificacion,
            'nombre_completo' => $this->nombreCompleto($u),
            'telefono'        => $u->contacto,
            'email'           => $u->email,
            'cargo'           => $u->cargo,
        ]);

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $user = User::findOrFail($data['user_id']);
        if (!$this->esPlanta($user)) {
            return $this->error('Solo se pueden agendar trabajadores de vinculación ' . self::VINCULACION_ATENDIBLE . '.');
        }
        $data['duracion_minutos'] = (int) AgendaConfiguracion::actual()->duracion_minutos;
        if ($msg = $this->conflicto($data, $user)) {
            return $this->error($msg);
        }

        $cita = new AgendaCita($data);
        $cita->telefono   = $user->contacto;
        $cita->email      = $user->email;
        $cita->cargo      = $user->cargo ?: null;
        $cita->created_by = Auth::id();
        $cita->updated_by = Auth::id();
        $cita->save();

        return response()->json([
            'ok'      => true,
            'message' => 'Cita agendada para ' . $this->nombreCompleto($user) . ' el '
                . $cita->fecha->format('d/m/Y') . ' a las ' . $cita->hora_corta . '.',
            'cita'    => $this->citaPayload($cita->load('user')),
        ]);
    }

    /**
     * Actualiza una cita. Admite edición completa (modal) o parcial
     * (cambios rápidos de asistencia, estado o ingreso a matriz en la tabla).
     */
    public function update(Request $request, AgendaCita $cita)
    {
        $data = $this->validar($request, true);

        $user = isset($data['user_id']) ? User::findOrFail($data['user_id']) : $cita->user;
        if (isset($data['user_id']) && (int) $data['user_id'] !== (int) $cita->user_id && !$this->esPlanta($user)) {
            return $this->error('Solo se pueden agendar trabajadores de vinculación ' . self::VINCULACION_ATENDIBLE . '.');
        }

        $reprograma = isset($data['fecha']) || isset($data['hora']);
        if ($reprograma && (Carbon::parse($data['fecha'] ?? $cita->fecha)->toDateString() !== $cita->fecha->toDateString()
                || ($data['hora'] ?? $cita->hora_corta) !== $cita->hora_corta)) {
            $data['duracion_minutos'] = (int) AgendaConfiguracion::actual()->duracion_minutos;
        }
        $nuevo = array_merge($cita->only(['fecha', 'hora', 'user_id', 'estado', 'duracion_minutos']), $data);
        $nuevo['fecha'] = Carbon::parse($nuevo['fecha'])->toDateString();
        $nuevo['hora']  = substr((string) $nuevo['hora'], 0, 5);
        // Solo se valida el turno si la cita queda ocupándolo y cambia fecha, hora, trabajador o se reactiva
        $cambiaTurno = isset($data['fecha']) || isset($data['hora']) || isset($data['user_id'])
            || ($cita->estado === 'cancelada' && $nuevo['estado'] !== 'cancelada');
        if ($nuevo['estado'] !== 'cancelada' && $cambiaTurno && ($msg = $this->conflicto($nuevo, $user, $cita->id))) {
            return $this->error($msg);
        }

        $cita->fill($data);
        if (isset($data['user_id'])) {
            $cita->telefono = $user->contacto;
            $cita->email    = $user->email;
            $cita->cargo    = $user->cargo ?: null;
        }
        // Coherencia entre asistencia y estado en los cambios rápidos
        if (($data['asistencia'] ?? null) === 'en_espera' && $cita->estado === 'programada') {
            $cita->estado = 'en_espera';
        }
        if (($data['asistencia'] ?? null) === 'no_asistio' && in_array($cita->estado, AgendaCita::ESTADOS_ACTIVOS, true)) {
            $cita->estado = 'cancelada';
        }
        $cita->updated_by = Auth::id();
        $cita->save();

        return response()->json([
            'ok'      => true,
            'message' => 'Cita actualizada.',
            'cita'    => $this->citaPayload($cita->fresh('user')),
        ]);
    }

    /**
     * Guarda el tiempo de atención por cita y las jornadas. Aplica a las citas
     * nuevas; las ya agendadas conservan su propio tiempo de atención.
     */
    public function configuracion(Request $request)
    {
        $data = $request->validate([
            'duracion_minutos' => ['required', 'integer', 'min:5', 'max:240'],
            'manana_inicio'    => ['required', 'date_format:H:i'],
            'manana_fin'       => ['required', 'date_format:H:i', 'after:manana_inicio'],
            'tarde_inicio'     => ['nullable', 'date_format:H:i', 'required_with:tarde_fin'],
            'tarde_fin'        => ['nullable', 'date_format:H:i', 'required_with:tarde_inicio', 'after:tarde_inicio'],
        ], [
            'duracion_minutos.min' => 'El tiempo de atención mínimo es de 5 minutos.',
            'duracion_minutos.max' => 'El tiempo de atención máximo es de 240 minutos.',
            'manana_fin.after'     => 'La jornada de la mañana debe terminar después de iniciar.',
            'tarde_fin.after'      => 'La jornada de la tarde debe terminar después de iniciar.',
        ]);

        if (!empty($data['tarde_inicio']) && AgendaConfiguracion::min($data['tarde_inicio']) < AgendaConfiguracion::min($data['manana_fin'])) {
            return $this->error('La jornada de la tarde debe iniciar después de terminar la de la mañana.');
        }
        if ((int) $data['duracion_minutos'] > AgendaConfiguracion::min($data['manana_fin']) - AgendaConfiguracion::min($data['manana_inicio'])) {
            return $this->error('El tiempo de atención es mayor que la jornada de la mañana.');
        }

        $config = AgendaConfiguracion::query()->first() ?? new AgendaConfiguracion();
        $config->fill([
            'duracion_minutos' => (int) $data['duracion_minutos'],
            'manana_inicio'    => $data['manana_inicio'],
            'manana_fin'       => $data['manana_fin'],
            'tarde_inicio'     => $data['tarde_inicio'] ?? null,
            'tarde_fin'        => $data['tarde_fin'] ?? null,
            'updated_by'       => Auth::id(),
        ])->save();

        return response()->json([
            'ok'      => true,
            'message' => 'Configuración guardada: citas de ' . $config->duracion_minutos . ' minutos.',
            'config'  => $config->payload(),
        ]);
    }

    /** Bloquea un rango de horas (o el día completo) para que no se agenden citas. */
    public function bloquear(Request $request)
    {
        $data = $request->validate([
            'fecha'        => ['required', 'date'],
            'dia_completo' => ['sometimes', 'boolean'],
            'hora_inicio'  => ['required_unless:dia_completo,true', 'nullable', 'date_format:H:i'],
            'hora_fin'     => ['required_unless:dia_completo,true', 'nullable', 'date_format:H:i', 'after:hora_inicio'],
            'motivo'       => ['nullable', 'string', 'max:150'],
        ], [
            'hora_inicio.required_unless' => 'Indica la hora de inicio del bloqueo.',
            'hora_fin.required_unless'    => 'Indica la hora final del bloqueo.',
            'hora_fin.after'              => 'La hora final debe ser posterior a la hora de inicio.',
        ]);

        $diaCompleto = !empty($data['dia_completo']);
        $ini = $diaCompleto ? 0 : AgendaConfiguracion::min($data['hora_inicio']);
        $fin = $diaCompleto ? 23 * 60 + 59 : AgendaConfiguracion::min($data['hora_fin']);
        $fecha = Carbon::parse($data['fecha'])->toDateString();

        // No se bloquea un horario con citas activas: primero se reprograman o cancelan
        $citas = AgendaCita::ocupan()->with('user')->whereDate('fecha', $fecha)->get()
            ->filter(fn (AgendaCita $c) => $c->estado !== 'completada' && $ini < $c->fin_min && $c->inicio_min < $fin);
        if ($citas->count()) {
            return $this->error('No se puede bloquear: hay ' . $citas->count() . ' cita(s) en ese horario ('
                . $citas->map(fn ($c) => $c->hora_corta . ' ' . $this->nombreCompleto($c->user))->implode(', ')
                . '). Reprográmalas o cancélalas primero.');
        }

        $cruce = AgendaBloqueo::whereDate('fecha', $fecha)->get()
            ->first(fn (AgendaBloqueo $b) => $ini < $b->fin_min && $b->inicio_min < $fin);
        if ($cruce) {
            return $this->error('Ese horario se cruza con un bloqueo existente ('
                . AgendaConfiguracion::hhmm($cruce->inicio_min) . ' a ' . AgendaConfiguracion::hhmm($cruce->fin_min) . ').');
        }

        $bloqueo = AgendaBloqueo::create([
            'fecha'       => $fecha,
            'hora_inicio' => AgendaConfiguracion::hhmm($ini),
            'hora_fin'    => AgendaConfiguracion::hhmm($fin),
            'motivo'      => $data['motivo'] ?? null,
            'created_by'  => Auth::id(),
        ]);

        return response()->json([
            'ok'      => true,
            'message' => $diaCompleto
                ? 'Se bloqueó el día ' . Carbon::parse($fecha)->format('d/m/Y') . '.'
                : 'Horario bloqueado de ' . AgendaConfiguracion::hhmm($ini) . ' a ' . AgendaConfiguracion::hhmm($fin) . '.',
            'bloqueo' => $this->bloqueoPayload($bloqueo),
        ]);
    }

    /** Libera un horario bloqueado. */
    public function desbloquear(AgendaBloqueo $bloqueo)
    {
        $bloqueo->delete();

        return response()->json(['ok' => true, 'message' => 'Horario desbloqueado.']);
    }

    // ─────────────────────────── Helpers ───────────────────────────

    private function validar(Request $request, bool $parcial = false): array
    {
        $req = $parcial ? 'sometimes' : 'required';

        return $request->validate([
            'user_id'        => [$req, 'integer', 'exists:users,id'],
            'fecha'          => [$req, 'date'],
            'hora'           => [$req, 'date_format:H:i'],
            'motivo'         => [$req, Rule::in(array_keys(ConceptoMedico::TIPOS))],
            'enfasis'        => ['sometimes', 'nullable', 'array'],
            'enfasis.*'      => [Rule::in(array_keys(ConceptoMedico::ENFASIS))],
            'asistencia'     => ['sometimes', Rule::in(array_keys(AgendaCita::ASISTENCIAS))],
            'estado'         => ['sometimes', Rule::in(array_keys(AgendaCita::ESTADOS))],
            'ingreso_matriz' => ['sometimes', Rule::in(array_keys(AgendaCita::MATRIZ))],
            'observaciones'  => ['sometimes', 'nullable', 'string', 'max:2000'],
        ], [
            'user_id.required' => 'Selecciona el funcionario a agendar.',
            'hora.date_format' => 'La hora debe tener el formato HH:MM.',
        ]);
    }

    /**
     * Valida que el horario de la cita esté disponible:
     *  - dentro de la jornada configurada,
     *  - sin traslaparse con un horario bloqueado,
     *  - sin traslaparse con otra cita (no se agendan dos pacientes a la misma hora),
     *  - y que el trabajador no tenga otra cita ese día.
     */
    private function conflicto(array $d, User $user, ?int $exceptoId = null): ?string
    {
        $config = AgendaConfiguracion::actual();
        $ini = AgendaConfiguracion::min($d['hora']);
        $fin = $ini + max(5, (int) ($d['duracion_minutos'] ?? $config->duracion_minutos));
        $rango = AgendaConfiguracion::hhmm($ini) . ' a ' . AgendaConfiguracion::hhmm($fin);

        if (!$config->dentroDeJornada($ini, $fin)) {
            return 'El horario ' . $rango . ' está fuera de la jornada de atención configurada.';
        }

        $bloqueo = AgendaBloqueo::whereDate('fecha', $d['fecha'])->get()
            ->first(fn (AgendaBloqueo $b) => $ini < $b->fin_min && $b->inicio_min < $fin);
        if ($bloqueo) {
            return 'El horario ' . $rango . ' está bloqueado' . ($bloqueo->motivo ? ' (' . $bloqueo->motivo . ')' : '') . '.';
        }

        $citas = AgendaCita::ocupan()->with('user')
            ->whereDate('fecha', $d['fecha'])
            ->when($exceptoId, fn ($q) => $q->where('id', '!=', $exceptoId))
            ->get();

        $cruce = $citas->first(fn (AgendaCita $c) => $ini < $c->fin_min && $c->inicio_min < $fin);
        if ($cruce) {
            return 'El horario ' . $rango . ' se cruza con la cita de ' . $this->nombreCompleto($cruce->user)
                . ' (' . $cruce->hora_corta . ' a ' . $cruce->hora_fin . '). No se pueden agendar dos pacientes a la misma hora.';
        }

        $otra = $citas->firstWhere('user_id', $user->id);
        if ($otra) {
            return $this->nombreCompleto($user) . ' ya tiene una cita ese día a las ' . $otra->hora_corta . '.';
        }

        return null;
    }

    /**
     * Turnos del día según la configuración, con su estado: libre, ocupado
     * (por qué citas) o bloqueado (por qué bloqueo).
     */
    private function turnosDelDia($activas, $bloqueos): array
    {
        return array_map(function ($t) use ($activas, $bloqueos) {
            $ini = AgendaConfiguracion::min($t['hora']);
            $fin = AgendaConfiguracion::min($t['fin']);
            $bloqueo = $bloqueos->first(fn ($b) => $ini < $b->fin_min && $b->inicio_min < $fin);

            return $t + [
                'ocupado_por' => $activas->filter(fn ($c) => $ini < $c->fin_min && $c->inicio_min < $fin)->pluck('id')->values(),
                'bloqueo'     => $bloqueo ? ['id' => $bloqueo->id, 'motivo' => $bloqueo->motivo] : null,
            ];
        }, AgendaConfiguracion::actual()->turnos());
    }

    private function bloqueoPayload(AgendaBloqueo $b): array
    {
        return [
            'id'          => $b->id,
            'fecha'       => $b->fecha->format('Y-m-d'),
            'hora_inicio' => AgendaConfiguracion::hhmm($b->inicio_min),
            'hora_fin'    => AgendaConfiguracion::hhmm($b->fin_min),
            'dia_completo'=> $b->dia_completo,
            'motivo'      => $b->motivo,
        ];
    }

    private function citaPayload(AgendaCita $c): array
    {
        $u = $c->user;

        return [
            'id'              => $c->id,
            'fecha'           => $c->fecha->format('Y-m-d'),
            'hora'            => $c->hora_corta,
            'hora_fin'        => $c->hora_fin,
            'duracion'        => (int) $c->duracion_minutos,
            'user_id'         => $c->user_id,
            'nombre'          => $u ? $this->nombreCompleto($u) : '—',
            'identificacion'  => $u?->identificacion,
            'tipo_identificacion' => $u?->tipo_identificacion ?? 'CC',
            // Datos vigentes del trabajador; si faltan, los registrados al agendar
            'cargo'           => ($u?->cargo ?: null) ?? $c->cargo,
            'telefono'        => $u?->contacto ?: $c->telefono,
            'email'           => $u?->email ?: $c->email,
            'motivo'          => $c->motivo,
            'motivo_label'    => $c->motivo_label,
            'enfasis'         => $c->enfasis ?? [],
            'asistencia'      => $c->asistencia,
            'estado'          => $c->estado,
            'ingreso_matriz'  => $c->ingreso_matriz,
            'observaciones'   => $c->observaciones,
            'concepto_url'    => $c->concepto_medico_id ? route('salud.concepto.show', $c->concepto_medico_id) : null,
            'atender_url'     => route('salud.concepto.index', ['cita' => $c->id]),
        ];
    }

    private function nombreCompleto(User $u): string
    {
        return trim($u->name . ' ' . ($u->apellido1 ?? '') . ' ' . ($u->apellido2 ?? ''));
    }

    private function fechaValida($valor): string
    {
        try {
            return $valor ? Carbon::parse($valor)->toDateString() : now()->toDateString();
        } catch (\Throwable $e) {
            return now()->toDateString();
        }
    }

    private function error(string $msg)
    {
        return response()->json(['ok' => false, 'message' => $msg], 422);
    }

    private function safeList(\Closure $fn)
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
