<?php

namespace App\Http\Controllers\SaludOcupacional;

use App\Http\Controllers\Controller;
use App\Models\ConceptoMedico;
use App\Models\ConceptoDocumento;
use App\Models\User;
use App\Models\Cargo;
use App\Models\Servicio;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ConceptoMedicoController extends Controller implements HasMiddleware
{
    /** Datos institucionales del empleador (HUV). */
    private const EMPLEADOR = 'HOSPITAL UNIVERSITARIO DEL VALLE "EVARISTO GARCÍA" E.S.E';
    private const NIT       = '890303461-2';

    /**
     * Control de acceso del módulo de Salud Ocupacional.
     *
     * Por ahora el ingreso está restringido EXCLUSIVAMENTE al rol "Super Admin".
     * Cuando se construya el módulo de permisos por roles, este punto será el
     * lugar donde se otorgará el acceso a los demás roles según sus permisos.
     */
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                $user = Auth::user();
                if (!$user || $user->role !== 'Super Admin') {
                    abort(403, 'Acceso restringido al módulo de Salud Ocupacional.');
                }
                return $next($request);
            },
        ];
    }

    /**
     * Vista principal: asistente para diligenciar un nuevo concepto médico.
     */
    public function index()
    {
        $cargos    = $this->safeList(fn () => Cargo::orderBy('nombre')->pluck('nombre'));
        $servicios = $this->safeList(fn () => Servicio::orderBy('nombre')->pluck('nombre'));

        $recientes = ConceptoMedico::with('user')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $medico = trim(
            (Auth::user()->name ?? '') . ' ' .
            (Auth::user()->apellido1 ?? '') . ' ' .
            (Auth::user()->apellido2 ?? '')
        );

        return view('salud_ocupacional.concepto.index', [
            'cargos'      => $cargos,
            'servicios'   => $servicios,
            'recientes'   => $recientes,
            'empleador'   => self::EMPLEADOR,
            'nit'         => self::NIT,
            'medicoNombre'=> $medico !== '' ? $medico : (Auth::user()->name ?? ''),
            'tipos'       => ConceptoMedico::TIPOS,
            'conceptos'   => ConceptoMedico::CONCEPTOS,
        ]);
    }

    /**
     * Busca un paciente (trabajador) por identificación y devuelve sus datos
     * junto con su historial de conceptos. Alimenta el auto-completado del paso 2.
     */
    public function buscarPaciente($identificacion)
    {
        $identificacion = trim($identificacion);
        $user = User::where('identificacion', $identificacion)->first();

        if (!$user) {
            return response()->json(['found' => false]);
        }

        $historial = ConceptoMedico::where('user_id', $user->id)
            ->orderByDesc('fecha_atencion')
            ->orderByDesc('id')
            ->take(20)
            ->get()
            ->map(fn ($c) => [
                'id'        => $c->id,
                'fecha'     => optional($c->fecha_atencion)->format('d/m/Y'),
                'tipo'      => $c->tipo_label,
                'concepto'  => $c->concepto_label,
                'url'       => route('salud.concepto.show', $c->id),
            ]);

        return response()->json([
            'found'    => true,
            'paciente' => $this->pacientePayload($user),
            'historial'=> $historial,
        ]);
    }

    /**
     * Crea un nuevo paciente (trabajador) desde el modal del paso 2.
     */
    public function storePaciente(Request $request)
    {
        $data = $this->validatePaciente($request);

        if (User::where('identificacion', $data['identificacion'])->exists()) {
            return response()->json([
                'ok'      => false,
                'message' => 'Ya existe un paciente con esa identificación.',
            ], 422);
        }

        $payload = $this->buildPacientePayload($data);
        // Requisitos mínimos de la cuenta (el paciente es un trabajador del directorio)
        $payload['password'] = Hash::make(Str::random(24));
        $payload['role'] = 'Usuario';
        $payload['email_verified_at'] = now();

        $user = User::create($payload);

        return response()->json([
            'ok'       => true,
            'message'  => 'Paciente creado correctamente.',
            'paciente' => $this->pacientePayload($user),
        ]);
    }

    /**
     * Actualiza los datos de un paciente existente desde el modal del paso 2.
     */
    public function updatePaciente(Request $request, User $user)
    {
        $data = $this->validatePaciente($request, $user->id);

        $user->fill($this->buildPacientePayload($data));
        $user->save();

        return response()->json([
            'ok'       => true,
            'message'  => 'Datos del paciente actualizados.',
            'paciente' => $this->pacientePayload($user->fresh()),
        ]);
    }

    /**
     * Guarda un concepto médico ocupacional (una atención).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'            => ['nullable', 'integer', 'exists:users,id'],
            'identificacion'     => ['nullable', 'string', 'max:100'],
            'tipo_atencion'      => ['nullable', 'string', 'max:30'],
            'lugar_atencion'     => ['nullable', 'string', 'max:150'],
            'concepto_resultado' => ['nullable', 'string', 'max:40'],
            'documentos.*'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        $user = !empty($validated['user_id']) ? User::find($validated['user_id']) : null;

        $concepto = new ConceptoMedico();
        $concepto->user_id        = $user?->id;
        $concepto->identificacion = $validated['identificacion'] ?? $user?->identificacion;
        $concepto->fecha_atencion = now()->toDateString();   // Fecha automática (requisito 3)
        $concepto->hora_atencion  = now()->format('H:i:s');  // Hora automática (requisito 3)
        $concepto->lugar_atencion = $request->input('lugar_atencion', 'HUV');
        $concepto->tipo_atencion  = $validated['tipo_atencion'] ?? null;

        // Snapshot del paciente
        $concepto->paciente_nombre = $user
            ? trim($user->name . ' ' . ($user->apellido1 ?? '') . ' ' . ($user->apellido2 ?? ''))
            : $request->input('paciente_nombre');
        $concepto->edad   = $request->input('edad');
        $concepto->genero = $request->input('genero');

        // Datos laborales / afiliación (por visita)
        foreach (['cargo_inicio', 'servicio', 'empleador', 'nit', 'eps', 'afp', 'arl'] as $campo) {
            $concepto->{$campo} = $request->input($campo);
        }

        // Campos de texto simples de la historia clínica
        foreach ([
            'epp_usa', 'epp_detalle', 'restricciones_previas', 'restricciones_previas_detalle',
            'motivo_consulta', 'estado_salud', 'antecedentes_familiares', 'revision_sistemas',
            'aspecto_general', 'diagnostico', 'vigilancia_epidemiologica',
            'concepto_resultado', 'recomendaciones', 'restricciones', 'sst',
            'medico', 'registro', 'firma',
        ] as $campo) {
            $concepto->{$campo} = $request->input($campo);
        }

        // Grupos estructurados (se guardan como JSON)
        $concepto->factores_riesgo            = $this->decodeJsonField($request->input('factores_riesgo'));
        $concepto->antecedentes_ocupacionales = $this->decodeJsonField($request->input('antecedentes_ocupacionales'));
        $concepto->accidentes_laborales       = $this->decodeJsonField($request->input('accidentes_laborales'));
        $concepto->enfermedad_laboral         = $this->decodeJsonField($request->input('enfermedad_laboral'));
        $concepto->antecedentes_personales    = $this->decodeJsonField($request->input('antecedentes_personales'));
        $concepto->habitos                    = $this->decodeJsonField($request->input('habitos'));
        $concepto->signos_vitales             = $this->decodeJsonField($request->input('signos_vitales'));
        $concepto->examen_sistemas            = $this->decodeJsonField($request->input('examen_sistemas'));

        $concepto->created_by = Auth::id();
        $concepto->save();

        // Documentos adjuntos de la EPS
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                if (!$file->isValid()) {
                    continue;
                }
                $ruta = $file->store('conceptos/' . $concepto->id, 'public');
                ConceptoDocumento::create([
                    'concepto_medico_id' => $concepto->id,
                    'nombre_original'    => $file->getClientOriginalName(),
                    'ruta'               => $ruta,
                    'mime'               => $file->getClientMimeType(),
                    'tamano'             => $file->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('salud.concepto.show', $concepto->id)
            ->with('success', 'Concepto médico ocupacional guardado correctamente.');
    }

    /**
     * Muestra un concepto guardado en formato certificado (imprimible).
     */
    public function show(ConceptoMedico $concepto)
    {
        $concepto->load('user', 'documentos', 'creador');

        return view('salud_ocupacional.concepto.show', [
            'c'         => $concepto,
            'conceptos' => ConceptoMedico::CONCEPTOS,
            'tipos'     => ConceptoMedico::TIPOS,
        ]);
    }

    // ─────────────────────────── Helpers ───────────────────────────

    private function validatePaciente(Request $request, ?int $ignoreId = null): array
    {
        $unique = 'unique:users,identificacion' . ($ignoreId ? ',' . $ignoreId : '');

        return $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'apellido1'       => ['nullable', 'string', 'max:255'],
            'apellido2'       => ['nullable', 'string', 'max:255'],
            'identificacion'  => ['required', 'string', 'max:100', $unique],
            'genero'          => ['nullable', 'string', 'max:5'],
            'edad'            => ['nullable', 'integer', 'min:0', 'max:150'],
            'fnacimiento'     => ['nullable', 'date'],
            'grupo_sanguineo' => ['nullable', 'string', 'max:5'],
            'lugar_nacimiento'=> ['nullable', 'string', 'max:120'],
            'contacto'        => ['nullable', 'string', 'max:20'],
            'email'           => ['nullable', 'email', 'max:255'],
            'direccionr'      => ['nullable', 'string', 'max:150'],
            'estracto'        => ['nullable', 'string', 'max:30'],
            'tvivienda'       => ['nullable', 'string', 'max:30'],
            'escivil'         => ['nullable', 'string', 'max:30'],
            'numero_hijos'    => ['nullable', 'integer', 'min:0', 'max:50'],
            'escolaridad'     => ['nullable', 'string', 'max:40'],
            'profesion'       => ['nullable', 'string', 'max:120'],
            'cargo'           => ['nullable', 'string', 'max:100'],
            'servicio'        => ['nullable', 'string', 'max:255'],
            'eps'             => ['nullable', 'string', 'max:120'],
            'afp'             => ['nullable', 'string', 'max:120'],
            'arl'             => ['nullable', 'string', 'max:120'],
        ]);
    }

    private function buildPacientePayload(array $data): array
    {
        $payload = [
            'name'            => $data['name'],
            'apellido1'       => $data['apellido1'] ?? null,
            'apellido2'       => $data['apellido2'] ?? null,
            'identificacion'  => $data['identificacion'],
            'genero'          => $data['genero'] ?? null,
            'edad'            => $data['edad'] ?? null,
            'fnacimiento'     => $data['fnacimiento'] ?? null,
            'contacto'        => $data['contacto'] ?? null,
            'email'           => $data['email'] ?? null,
            'direccionr'      => $data['direccionr'] ?? null,
            'estracto'        => $data['estracto'] ?? null,
            'tvivienda'       => $data['tvivienda'] ?? null,
            'escivil'         => $data['escivil'] ?? null,
            'cargo'           => $data['cargo'] ?? '',
            'servicio'        => $data['servicio'] ?? null,
        ];

        // Columnas nuevas (defensivo por si la migración aún no corrió)
        foreach (['grupo_sanguineo', 'lugar_nacimiento', 'numero_hijos', 'escolaridad', 'profesion', 'eps', 'afp', 'arl'] as $campo) {
            if (Schema::hasColumn('users', $campo)) {
                $payload[$campo] = $data[$campo] ?? null;
            }
        }

        return $payload;
    }

    private function pacientePayload(User $user): array
    {
        return [
            'id'               => $user->id,
            'name'             => $user->name,
            'apellido1'        => $user->apellido1,
            'apellido2'        => $user->apellido2,
            'nombre_completo'  => trim($user->name . ' ' . ($user->apellido1 ?? '') . ' ' . ($user->apellido2 ?? '')),
            'identificacion'   => $user->identificacion,
            'genero'           => $user->genero,
            'edad'             => $user->edad,
            'fnacimiento'      => optional($user->fnacimiento)->format('Y-m-d'),
            'grupo_sanguineo'  => $user->grupo_sanguineo ?? null,
            'lugar_nacimiento' => $user->lugar_nacimiento ?? null,
            'contacto'         => $user->contacto,
            'email'            => $user->email,
            'direccionr'       => $user->direccionr,
            'estracto'         => $user->estracto,
            'tvivienda'        => $user->tvivienda,
            'escivil'          => $user->escivil,
            'numero_hijos'     => $user->numero_hijos ?? null,
            'escolaridad'      => $user->escolaridad ?? null,
            'profesion'        => $user->profesion ?? null,
            'cargo'            => $user->cargo,
            'servicio'         => $user->servicio,
            'eps'              => $user->eps ?? null,
            'afp'              => $user->afp ?? null,
            'arl'              => $user->arl ?? null,
        ];
    }

    private function decodeJsonField($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }
        return null;
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
