<?php

namespace App\Http\Controllers\SaludOcupacional;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaludOcupacional\Concerns\TrabajadoresPlanta;
use App\Models\ConceptoCorreccion;
use App\Models\ConceptoMedico;
use App\Support\ConsultaSstExcel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Consulta SST: listado de todos los conceptos médicos ocupacionales realizados,
 * con búsqueda (nombre, identificación o palabras clave), rango de fechas,
 * ver, corregir (con auditoría), anular (eliminar lógico) e imprimir.
 *
 * Cada acción tiene su propia ruta, por lo que aparece sola en
 * Configuración › Gestión de Permisos para asignarla o quitarla por rol.
 */
class ConsultaSstController extends Controller implements HasMiddleware
{
    use TrabajadoresPlanta;

    /** Columnas por las que se puede ordenar la tabla. */
    private const ORDENABLES = [
        'id' => 'id', 'fecha' => 'fecha_atencion', 'paciente' => 'paciente_nombre', 'identificacion' => 'identificacion',
        'tipo' => 'tipo_atencion', 'concepto' => 'concepto_resultado', 'medico' => 'medico',
    ];

    /** Columnas donde se buscan las palabras clave. */
    private const COLUMNAS_BUSQUEDA = [
        'paciente_nombre', 'identificacion', 'cargo_inicio', 'servicio', 'eps', 'afp', 'arl', 'medico',
        'tipo_atencion', 'concepto_resultado', 'motivo_consulta', 'diagnostico', 'recomendaciones',
        'restricciones', 'sst', 'observaciones_concepto',
    ];

    public function index()
    {
        return view('salud_ocupacional.consulta_sst.index', [
            'tipos'            => ConceptoMedico::TIPOS,
            'conceptos'        => ConceptoMedico::CONCEPTOS,
            'conceptosGrupos'  => ConceptoMedico::CONCEPTOS_GRUPOS,
            'conceptosEnfasis' => ConceptoMedico::CONCEPTOS_ENFASIS,
            'enfasis'          => ConceptoMedico::ENFASIS,
            'temporalidades'   => ConceptoMedico::TEMPORALIDADES,
            'migracionPendiente' => !Schema::hasTable('conceptos_medicos') || !Schema::hasColumn('conceptos_medicos', 'anulado'),
        ]);
    }

    /**
     * Datos de la tabla (paginados en el servidor) y los indicadores del filtro actual.
     */
    public function datos(Request $request)
    {
        if (!Schema::hasTable('conceptos_medicos')) {
            return response()->json(['items' => [], 'total' => 0, 'pagina' => 1, 'paginas' => 1, 'resumen' => []]);
        }

        $conAnulacion = Schema::hasColumn('conceptos_medicos', 'anulado');

        // Indicadores del rango filtrado (sin el filtro de estado ni de resultado)
        $resumen = $this->resumen($this->filtrar(ConceptoMedico::query(), $request), $conAnulacion);

        $query = $this->listado($request);

        $porPagina = min(max((int) $request->query('por_pagina', 10), 5), 100);
        $total = (clone $query)->count();
        $paginas = max(1, (int) ceil($total / $porPagina));
        $pagina = min(max((int) $request->query('pagina', 1), 1), $paginas);

        $relaciones = ['user:id,name,apellido1,apellido2,identificacion'];
        if (Schema::hasTable('concepto_correcciones')) {
            $query->withCount(['correcciones' => fn ($k) => $k->reorder()]);
        }
        $items = $query->with($relaciones)->withCount('documentos')
            ->skip(($pagina - 1) * $porPagina)->take($porPagina)->get()
            ->map(fn (ConceptoMedico $c) => $this->fila($c));

        return response()->json([
            'items'   => $items,
            'total'   => $total,
            'pagina'  => $pagina,
            'paginas' => $paginas,
            'resumen' => $resumen,
        ]);
    }

    /** Detalle de un concepto para el panel "Ver" y el formulario de corrección. */
    public function show(ConceptoMedico $concepto)
    {
        $concepto->load(['user', 'creador', 'documentos']);
        $tieneCorrecciones = Schema::hasTable('concepto_correcciones');
        if ($tieneCorrecciones) {
            $concepto->load('correcciones.user');
        }
        $dash = fn ($v) => ($v !== null && trim((string) $v) !== '') ? $v : null;
        $nombre = fn ($u) => $u ? trim($u->name . ' ' . ($u->apellido1 ?? '') . ' ' . ($u->apellido2 ?? '')) : null;

        return response()->json([
            'fila'     => $this->fila($concepto),
            'paciente' => [
                'nombre'         => $concepto->paciente_nombre ?: $nombre($concepto->user),
                'identificacion' => $concepto->identificacion,
                'edad'           => $concepto->edad,
                'genero'         => ['F' => 'Femenino', 'M' => 'Masculino', 'O' => 'Otro'][$concepto->genero ?? ''] ?? $dash($concepto->genero),
            ],
            'laborales' => [
                'Cargo' => $dash($concepto->cargo_inicio), 'Servicio' => $dash($concepto->servicio),
                'EPS' => $dash($concepto->eps), 'AFP' => $dash($concepto->afp), 'ARL' => $dash($concepto->arl),
                'Lugar de atención' => $dash($concepto->lugar_atencion),
            ],
            'concepto' => [
                'tipo_atencion'              => $concepto->tipo_atencion,
                'enfasis'                    => array_values((array) ($concepto->enfasis ?? [])),
                'concepto_resultado'         => $concepto->concepto_resultado,
                'concepto_enfasis'           => (object) ($concepto->concepto_enfasis ?? []),
                'observaciones_concepto'     => $concepto->observaciones_concepto,
                'recomendaciones'            => $concepto->recomendaciones,
                'restricciones'              => $concepto->restricciones,
                'restricciones_temporalidad' => $concepto->restricciones_temporalidad,
                'temporalidad_label'         => $concepto->temporalidad_label,
                'restricciones_hasta'        => optional($concepto->restricciones_hasta)->format('d/m/Y'),
                'sst'                        => $concepto->sst,
                'diagnostico'                => $concepto->diagnostico,
            ],
            'medico' => [
                'nombre'      => $dash($concepto->medico),
                'registro'    => $dash($concepto->registro),
                'registrado'  => $nombre($concepto->creador),
                'creado'      => optional($concepto->created_at)->format('d/m/Y h:i A'),
            ],
            'documentos' => $concepto->documentos->map(fn ($d) => [
                'nombre' => $d->nombre_original,
                'url'    => route('salud.concepto.documento', $d->id),
                'tamano' => $d->tamano ? number_format($d->tamano / 1048576, 1) . ' MB' : null,
            ]),
            'correcciones' => $tieneCorrecciones ? $concepto->correcciones->map(fn (ConceptoCorreccion $k) => [
                'fecha'   => optional($k->created_at)->format('d/m/Y h:i A'),
                'usuario' => $nombre($k->user) ?: 'Usuario eliminado',
                'motivo'  => $k->motivo,
                'cambios' => $k->cambios,
            ]) : [],
            'anulacion' => $concepto->anulado ? [
                'fecha'   => optional($concepto->anulado_at)->format('d/m/Y h:i A'),
                'usuario' => $nombre($concepto->anuladoPor) ?: '—',
                'motivo'  => $concepto->motivo_anulacion,
            ] : null,
        ]);
    }

    /**
     * Corrige los campos del concepto (no la historia clínica) y deja la auditoría
     * del valor anterior y nuevo de cada campo, con el motivo de la corrección.
     */
    public function corregir(Request $request, ConceptoMedico $concepto)
    {
        if (!Schema::hasTable('concepto_correcciones')) {
            return response()->json(['ok' => false, 'message' => 'Falta ejecutar las migraciones de Consulta SST en el servidor.'], 422);
        }
        if ($concepto->anulado) {
            return response()->json(['ok' => false, 'message' => 'El concepto está anulado y no se puede corregir.'], 422);
        }

        $data = $request->validate([
            'tipo_atencion'              => ['required', 'string', Rule::in(array_keys(ConceptoMedico::TIPOS))],
            'concepto_resultado'         => ['required', 'string'],
            'concepto_enfasis'           => ['nullable', 'array'],
            'observaciones_concepto'     => ['nullable', 'string', 'max:5000'],
            'recomendaciones'            => ['nullable', 'string', 'max:10000'],
            'restricciones'              => ['nullable', 'string', 'max:10000'],
            'restricciones_temporalidad' => ['nullable', 'string', Rule::in(array_map('strval', array_keys(ConceptoMedico::TEMPORALIDADES)))],
            'sst'                        => ['nullable', 'string', 'max:10000'],
            'motivo'                     => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'motivo.required' => 'Indique el motivo de la corrección.',
            'motivo.min'      => 'El motivo de la corrección debe tener al menos 10 caracteres.',
        ]);

        $grupo = ConceptoMedico::grupoConcepto($data['tipo_atencion']);
        if (!isset(ConceptoMedico::CONCEPTOS_GRUPOS[$grupo][$data['concepto_resultado']])) {
            return response()->json(['ok' => false, 'message' => 'El concepto emitido no corresponde al tipo de atención seleccionado.'], 422);
        }

        // Concepto por énfasis: solo para los énfasis que tiene la consulta y con opciones válidas
        $enfasis = (array) ($concepto->enfasis ?? []);
        $conceptoEnfasis = [];
        foreach ((array) ($data['concepto_enfasis'] ?? []) as $enf => $op) {
            if (is_string($op) && $op !== '' && in_array($enf, $enfasis, true) && isset(ConceptoMedico::CONCEPTOS_ENFASIS[$enf]['opciones'][$op])) {
                $conceptoEnfasis[$enf] = $op;
            }
        }

        $nuevos = [
            'tipo_atencion'              => $data['tipo_atencion'],
            'concepto_resultado'         => $data['concepto_resultado'],
            'concepto_enfasis'           => $conceptoEnfasis ?: null,
            'observaciones_concepto'     => $this->texto($data['observaciones_concepto'] ?? null),
            'recomendaciones'            => $this->texto($data['recomendaciones'] ?? null),
            'restricciones'              => $this->texto($data['restricciones'] ?? null),
            'restricciones_temporalidad' => $data['restricciones_temporalidad'] ?? null,
            'sst'                        => $this->texto($data['sst'] ?? null),
        ];

        $cambios = [];
        foreach ($nuevos as $campo => $valor) {
            if (!Schema::hasColumn('conceptos_medicos', $campo)) {
                continue;
            }
            $antes = $concepto->{$campo};
            if ($this->normalizar($antes) !== $this->normalizar($valor)) {
                $cambios[$campo] = ['antes' => $antes, 'despues' => $valor];
                $concepto->{$campo} = $valor;
            }
        }

        if (!$cambios) {
            return response()->json(['ok' => false, 'message' => 'No hay cambios para guardar.'], 422);
        }

        DB::transaction(function () use ($concepto, $cambios, $data) {
            $concepto->save();
            ConceptoCorreccion::create([
                'concepto_medico_id' => $concepto->id,
                'user_id'            => Auth::id(),
                'cambios'            => $cambios,
                'motivo'             => trim($data['motivo']),
            ]);
        });

        return response()->json(['ok' => true, 'message' => 'Concepto corregido. Se registró la auditoría del cambio.']);
    }

    /**
     * "Eliminar": anula el concepto. El registro clínico se conserva con quién lo anuló,
     * cuándo y por qué; deja de aparecer en los listados de vigentes.
     */
    public function anular(Request $request, ConceptoMedico $concepto)
    {
        if (!Schema::hasColumn('conceptos_medicos', 'anulado')) {
            return response()->json(['ok' => false, 'message' => 'Falta ejecutar las migraciones de Consulta SST en el servidor.'], 422);
        }
        if ($concepto->anulado) {
            return response()->json(['ok' => false, 'message' => 'El concepto ya está anulado.'], 422);
        }

        $data = $request->validate([
            'motivo' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'motivo.required' => 'Indique el motivo de la anulación.',
            'motivo.min'      => 'El motivo de la anulación debe tener al menos 10 caracteres.',
        ]);

        $concepto->forceFill([
            'anulado'          => true,
            'anulado_at'       => now(),
            'anulado_por'      => Auth::id(),
            'motivo_anulacion' => trim($data['motivo']),
        ])->save();

        return response()->json(['ok' => true, 'message' => 'Concepto anulado. El registro se conserva para auditoría.']);
    }

    /** Certificado imprimible (documento independiente, sin el diseño del aplicativo). */
    public function imprimir(ConceptoMedico $concepto)
    {
        $concepto->load('user');
        return view('salud_ocupacional.concepto.certificado', ['c' => $concepto]);
    }

    /**
     * Exporta a Excel (.xlsx) la información y/o las estadísticas de los conceptos,
     * con los mismos filtros que tiene la tabla en pantalla.
     * contenido: todo (estadísticas + listado) | estadisticas | listado
     *
     * El archivo se arma completo antes de enviarlo: si algo falla se informa el motivo
     * (y queda en el log) en vez de mostrar un error 500.
     */
    public function exportar(Request $request)
    {
        abort_unless(Schema::hasTable('conceptos_medicos'), 404);
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $contenido = in_array($request->query('contenido'), ['estadisticas', 'listado'], true) ? $request->query('contenido') : 'todo';
        $conAnulacion = Schema::hasColumn('conceptos_medicos', 'anulado');
        // Solo las columnas necesarias y existentes (la firma guarda imágenes y no se exporta)
        $columnas = fn (array $cols) => array_values(array_filter($cols, fn ($c) => Schema::hasColumn('conceptos_medicos', $c)));

        try {
            $excel = new ConsultaSstExcel($this->filtrosAplicados($request), Auth::user());

            if ($contenido !== 'listado') {
                // Estadísticas del rango filtrado (vigentes), igual que los indicadores de la pantalla
                $base = $this->filtrar(ConceptoMedico::query(), $request);
                $vigentes = (clone $base)->when($conAnulacion, fn ($q) => $q->where('anulado', false))
                    ->get($columnas(['id', 'user_id', 'identificacion', 'fecha_atencion', 'tipo_atencion', 'enfasis', 'concepto_resultado',
                        'concepto_enfasis', 'restricciones_temporalidad', 'cargo_inicio', 'servicio', 'medico']));
                $anulados = $conAnulacion ? (clone $base)->where('anulado', true)->count() : 0;
                $excel->estadisticas($vigentes, $anulados);
            }

            if ($contenido !== 'estadisticas') {
                $query = $this->listado($request)
                    ->select($columnas(['id', 'user_id', 'identificacion', 'fecha_atencion', 'hora_atencion', 'paciente_nombre', 'edad', 'genero',
                        'cargo_inicio', 'servicio', 'eps', 'afp', 'arl', 'tipo_atencion', 'enfasis', 'concepto_resultado', 'concepto_enfasis',
                        'recomendaciones', 'restricciones', 'restricciones_temporalidad', 'sst', 'observaciones_concepto', 'medico', 'registro',
                        'anulado', 'motivo_anulacion']))
                    ->with('user:id,name,apellido1,apellido2');
                if (Schema::hasTable('concepto_correcciones')) {
                    $query->withCount(['correcciones' => fn ($k) => $k->reorder()]);
                }
                $excel->listado($query);
            }

            $archivo = $excel->contenido();
        } catch (\Throwable $e) {
            report($e);
            $mensaje = 'No fue posible generar el archivo de Excel: ' . Str::limit($e->getMessage(), 250);
            return $request->expectsJson() || $request->ajax()
                ? response()->json(['ok' => false, 'message' => $mensaje], 500)
                : redirect()->route('salud.consulta-sst.index')->with('error', $mensaje);
        }

        $nombre = 'consulta_sst_' . ($contenido === 'todo' ? '' : $contenido . '_') . now()->format('Y-m-d_His') . '.xlsx';
        return response($archivo, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nombre . '"',
            'Content-Length'      => strlen($archivo),
            'Cache-Control'       => 'max-age=0, no-cache, no-store',
        ]);
    }

    // ─────────────────────────── Helpers ───────────────────────────

    /** Consulta de la tabla: filtros + estado (vigentes/anulados/todos) + resultado + orden. */
    private function listado(Request $request): Builder
    {
        $query = $this->filtrar(ConceptoMedico::query(), $request);

        if (Schema::hasColumn('conceptos_medicos', 'anulado')) {
            $estado = $request->query('estado', 'vigentes');
            if ($estado === 'vigentes') {
                $query->where('anulado', false);
            } elseif ($estado === 'anulados') {
                $query->where('anulado', true);
            }
        }
        if ($resultado = $request->query('resultado')) {
            $claves = $this->clavesPorClase($resultado);
            $claves ? $query->whereIn('concepto_resultado', $claves) : $query->whereRaw('1 = 0');
        }

        $orden = self::ORDENABLES[$request->query('orden', 'fecha')] ?? 'fecha_atencion';
        $dir = $request->query('dir') === 'asc' ? 'asc' : 'desc';
        return $query->orderBy($orden, $dir)->orderBy('id', $dir);
    }

    /** Descripción legible de los filtros, para el encabezado del Excel. */
    private function filtrosAplicados(Request $request): array
    {
        $out = [];
        if ($q = trim((string) $request->query('q', ''))) {
            $out['Búsqueda'] = $q;
        }
        $desde = $this->fecha($request->query('desde'));
        $hasta = $this->fecha($request->query('hasta'));
        $fmt = fn ($f) => \Carbon\Carbon::parse($f)->format('d/m/Y');
        $out['Periodo'] = match (true) {
            $desde && $hasta => 'Del ' . $fmt($desde) . ' al ' . $fmt($hasta),
            (bool) $desde    => 'Desde el ' . $fmt($desde),
            (bool) $hasta    => 'Hasta el ' . $fmt($hasta),
            default          => 'Todos los registros',
        };
        if (($tipo = $request->query('tipo')) && isset(ConceptoMedico::TIPOS[$tipo])) {
            $out['Tipo de evaluación'] = ConceptoMedico::TIPOS[$tipo];
        }
        $out['Estado'] = ['vigentes' => 'Vigentes', 'anulados' => 'Anulados', 'todos' => 'Todos'][$request->query('estado', 'vigentes')] ?? 'Vigentes';
        if ($r = ['ok' => 'Aptos', 'warn' => 'Con restricciones', 'bad' => 'No aptos'][$request->query('resultado', '')] ?? null) {
            $out['Resultado'] = $r;
        }
        return $out;
    }

    private function filtrar(Builder $query, Request $request): Builder
    {
        if ($desde = $this->fecha($request->query('desde'))) {
            $query->whereDate('fecha_atencion', '>=', $desde);
        }
        if ($hasta = $this->fecha($request->query('hasta'))) {
            $query->whereDate('fecha_atencion', '<=', $hasta);
        }
        if (($tipo = $request->query('tipo')) && isset(ConceptoMedico::TIPOS[$tipo])) {
            $query->where('tipo_atencion', $tipo);
        }

        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return $query;
        }

        // Número de identificación (con o sin puntos): coincidencia por prefijo
        if (preg_match('/^[\d.\s-]+$/', $q)) {
            $digitos = preg_replace('/\D/', '', $q);
            return $query->where(fn ($w) => $w->where('identificacion', 'like', $digitos . '%')->orWhere('id', (int) $digitos));
        }

        // Nombre o palabras clave: cada palabra debe aparecer en algún campo (en cualquier orden)
        foreach (array_slice(preg_split('/\s+/', $q), 0, 8) as $palabra) {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $palabra) . '%';
            $clavesConcepto = $this->clavesQueContienen($palabra);
            $query->where(function ($w) use ($like, $clavesConcepto) {
                foreach (self::COLUMNAS_BUSQUEDA as $col) {
                    $w->orWhere($col, 'like', $like);
                }
                if ($clavesConcepto) {
                    $w->orWhereIn('concepto_resultado', $clavesConcepto);
                }
                $w->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like)
                    ->orWhere('apellido1', 'like', $like)->orWhere('apellido2', 'like', $like));
            });
        }

        return $query;
    }

    /** Claves de concepto cuya etiqueta contiene la palabra (p. ej. "apto", "egreso", "brigada"). */
    private function clavesQueContienen(string $palabra): array
    {
        $p = mb_strtolower($palabra);
        return array_keys(array_filter(ConceptoMedico::CONCEPTOS, fn ($label) => str_contains(mb_strtolower($label), $p)));
    }

    /** Claves de concepto según su clase visual: ok (apto), warn (con restricciones), bad (no apto). */
    private function clavesPorClase(string $clase): array
    {
        $out = [];
        foreach (ConceptoMedico::CONCEPTOS_GRUPOS as $ops) {
            foreach ($ops as $clave => $op) {
                if ($op[2] === $clase) {
                    $out[] = $clave;
                }
            }
        }
        return $out;
    }

    private function resumen(Builder $query, bool $conAnulacion): array
    {
        $porResultado = (clone $query)->when($conAnulacion, fn ($q) => $q->where('anulado', false))
            ->select('concepto_resultado', DB::raw('COUNT(*) as n'))->groupBy('concepto_resultado')
            ->pluck('n', 'concepto_resultado');

        $suma = fn (string $clase) => (int) $porResultado->only($this->clavesPorClase($clase))->sum();

        return [
            'vigentes' => (int) $porResultado->sum(),
            'ok'       => $suma('ok'),
            'warn'     => $suma('warn'),
            'bad'      => $suma('bad'),
            'anulados' => $conAnulacion ? (clone $query)->where('anulado', true)->count() : 0,
        ];
    }

    /** Fila de la tabla. */
    private function fila(ConceptoMedico $c): array
    {
        $paciente = $c->paciente_nombre
            ?: ($c->user ? trim($c->user->name . ' ' . ($c->user->apellido1 ?? '') . ' ' . ($c->user->apellido2 ?? '')) : '—');

        return [
            'id'             => $c->id,
            'numero'         => str_pad((string) $c->id, 6, '0', STR_PAD_LEFT),
            'fecha'          => optional($c->fecha_atencion)->format('d/m/Y'),
            'fecha_iso'      => optional($c->fecha_atencion)->format('Y-m-d'),
            'hora'           => $c->hora_atencion ? substr((string) $c->hora_atencion, 0, 5) : null,
            'paciente'       => $paciente,
            'identificacion' => $c->identificacion,
            'cargo'          => $c->cargo_inicio,
            'servicio'       => $c->servicio,
            'tipo'           => $c->tipo_label,
            'enfasis'        => $c->enfasis_label,
            'concepto'       => $c->concepto_label,
            'clase'          => $c->concepto_clase,
            'conceptos_enfasis' => array_map(fn ($e) => ['titulo' => $e[0], 'etiqueta' => $e[1], 'clase' => $e[2]], $c->conceptos_enfasis_lista),
            'restricciones_hasta' => optional($c->restricciones_hasta)->format('d/m/Y'),
            'restricciones_indefinidas' => $c->restricciones_temporalidad === 'indefinido',
            'medico'         => $c->medico,
            'documentos'     => (int) ($c->documentos_count ?? 0),
            'correcciones'   => (int) ($c->correcciones_count ?? 0),
            'anulado'        => (bool) $c->anulado,
            'motivo_anulacion' => $c->motivo_anulacion,
            'url_completa'   => route('salud.concepto.show', $c->id),
            'url_imprimir'   => route('salud.consulta-sst.imprimir', $c->id),
        ];
    }

    private function fecha($valor): ?string
    {
        $valor = trim((string) $valor);
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor) ? $valor : null;
    }

    private function texto($valor): ?string
    {
        $valor = trim((string) $valor);
        return $valor === '' ? null : $valor;
    }

    /** Para comparar valores antes/después sin falsos cambios (null vs "", arreglos en distinto orden). */
    private function normalizar($valor): string
    {
        if (is_array($valor)) {
            ksort($valor);
            return $valor ? json_encode($valor) : '';
        }
        return trim((string) $valor);
    }
}
