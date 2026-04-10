<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Capacitacion;
use App\Models\CapacitacionAsistencia;
use App\Models\CapacitacionSesion;
use App\Models\CapacitacionAsistenciaRegistro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CapacitacionController extends Controller
{
    public function index()
    {
        $hasSesiones = Schema::hasTable('capacitacion_sesiones');

        $query = Capacitacion::query();
        if ($hasSesiones) {
            $query->with('ultimaSesion');
        }
        $capacitaciones = $query->withCount(['asistencias', 'asistencias as asistieron_count' => function ($q) {
                $q->where('asistio', true);
            }])->orderBy('fecha', 'desc')->paginate(15);

        $activas = Capacitacion::where('activo', true)->where('fecha', '>=', now()->toDateString())->count();
        $totalAsistieron = ($hasSesiones && Schema::hasTable('capacitacion_asistencia_registros'))
            ? CapacitacionAsistenciaRegistro::count()
            : CapacitacionAsistencia::where('asistio', true)->count();

        return view('config.capacitaciones.index', compact('capacitaciones', 'activas', 'totalAsistieron', 'hasSesiones'));
    }

    public function create()
    {
        $usuarios = User::where('role', 'Usuario')->orderBy('name')->get();
        return view('config.capacitaciones.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:200',
            'instructor' => 'nullable|string|max:200',
            'fecha' => 'required|date',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
            'capacidad_maxima' => 'nullable|integer|min:0',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:users,id',
        ]);

        $capacitacion = Capacitacion::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'ubicacion' => $request->ubicacion,
            'instructor' => $request->instructor,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'capacidad_maxima' => $request->capacidad_maxima ?? 0,
            'activo' => true,
            'created_by' => Auth::id(),
        ]);

        $usuariosIds = $request->usuarios ?? [];

        if (!empty($usuariosIds)) {
            foreach ($usuariosIds as $userId) {
                CapacitacionAsistencia::create([
                    'capacitacion_id' => $capacitacion->id,
                    'user_id' => $userId,
                    'asistio' => false,
                ]);
            }
        }

        // Crear primera sesión
        if (Schema::hasTable('capacitacion_sesiones')) {
            CapacitacionSesion::create([
                'capacitacion_id' => $capacitacion->id,
                'token' => Str::random(32),
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'citados_ids' => $usuariosIds,
            ]);
        }

        return redirect()->route('config.capacitaciones.index')
                         ->with('success', 'Capacitación creada exitosamente.');
    }

    public function show(Capacitacion $capacitacione)
    {
        $capacitacion = $capacitacione;
        $capacitacion->load(['asistencias.user']);
        $usuarios = User::where('role', 'Usuario')->orderBy('name')->get();
        $asignadosIds = $capacitacion->asistencias->pluck('user_id')->toArray();

        return view('config.capacitaciones.show', compact('capacitacion', 'usuarios', 'asignadosIds'));
    }

    public function edit(Capacitacion $capacitacione)
    {
        $capacitacion = $capacitacione;
        $capacitacion->load('asistencias');
        $usuarios = User::where('role', 'Usuario')->orderBy('name')->get();
        $asignadosIds = $capacitacion->asistencias->pluck('user_id')->toArray();

        return view('config.capacitaciones.edit', compact('capacitacion', 'usuarios', 'asignadosIds'));
    }

    public function update(Request $request, Capacitacion $capacitacione)
    {
        $capacitacion = $capacitacione;

        $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:200',
            'instructor' => 'nullable|string|max:200',
            'fecha' => 'required|date',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
            'capacidad_maxima' => 'nullable|integer|min:0',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:users,id',
        ]);

        // Detectar si cambió fecha/hora o citados para crear nueva sesión
        $fechaCambio = $capacitacion->fecha->format('Y-m-d') !== $request->fecha
            || $capacitacion->hora_inicio !== $request->hora_inicio
            || $capacitacion->hora_fin !== $request->hora_fin;

        $nuevosIds = $request->usuarios ?? [];
        $existentesIds = $capacitacion->asistencias()->pluck('user_id')->toArray();
        sort($nuevosIds);
        sort($existentesIds);
        $citadosCambio = $nuevosIds !== $existentesIds;

        $capacitacion->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'ubicacion' => $request->ubicacion,
            'instructor' => $request->instructor,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'capacidad_maxima' => $request->capacidad_maxima ?? 0,
        ]);

        // Sincronizar citados
        $removidos = array_diff($existentesIds, $nuevosIds);
        if (!empty($removidos)) {
            $capacitacion->asistencias()->whereIn('user_id', $removidos)->delete();
        }
        $nuevos = array_diff($nuevosIds, $existentesIds);
        foreach ($nuevos as $userId) {
            CapacitacionAsistencia::create([
                'capacitacion_id' => $capacitacion->id,
                'user_id' => $userId,
                'asistio' => false,
            ]);
        }

        // Si cambiaron fechas/hora o citados, crear nueva sesión con nuevo token
        if (($fechaCambio || $citadosCambio) && Schema::hasTable('capacitacion_sesiones')) {
            CapacitacionSesion::create([
                'capacitacion_id' => $capacitacion->id,
                'token' => Str::random(32),
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'citados_ids' => $nuevosIds,
            ]);
        }

        return redirect()->route('config.capacitaciones.index')
                         ->with('success', 'Capacitación actualizada exitosamente.');
    }

    public function destroy(Capacitacion $capacitacione)
    {
        $capacitacione->delete();
        return redirect()->route('config.capacitaciones.index')
                         ->with('success', 'Capacitación eliminada.');
    }

    // ─── API: Marcar/desmarcar asistencia ───
    public function toggleAsistencia(Request $request)
    {
        $request->validate([
            'capacitacion_id' => 'required|exists:capacitaciones,id',
            'user_id' => 'required|exists:users,id',
            'asistio' => 'required|boolean',
        ]);

        $asistencia = CapacitacionAsistencia::where('capacitacion_id', $request->capacitacion_id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($asistencia) {
            $asistencia->update(['asistio' => $request->asistio]);
            return response()->json(['ok' => true, 'asistio' => $asistencia->asistio]);
        }

        return response()->json(['ok' => false, 'error' => 'Registro no encontrado'], 404);
    }

    // ─── API: Agregar usuario a capacitación ───
    public function agregarUsuario(Request $request)
    {
        $request->validate([
            'capacitacion_id' => 'required|exists:capacitaciones,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $existe = CapacitacionAsistencia::where('capacitacion_id', $request->capacitacion_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($existe) {
            return response()->json(['ok' => false, 'error' => 'El usuario ya está asignado'], 422);
        }

        CapacitacionAsistencia::create([
            'capacitacion_id' => $request->capacitacion_id,
            'user_id' => $request->user_id,
            'asistio' => false,
        ]);

        return response()->json(['ok' => true]);
    }

    // ─── API: Remover usuario de capacitación ───
    public function removerUsuario(Request $request)
    {
        $request->validate([
            'capacitacion_id' => 'required|exists:capacitaciones,id',
            'user_id' => 'required|exists:users,id',
        ]);

        CapacitacionAsistencia::where('capacitacion_id', $request->capacitacion_id)
            ->where('user_id', $request->user_id)
            ->delete();

        return response()->json(['ok' => true]);
    }

    // ─── API: Obtener datos de informes por sesiones ───
    public function informes(Capacitacion $capacitacione)
    {
        if (!Schema::hasTable('capacitacion_sesiones')) {
            return response()->json(['ok' => false, 'error' => 'Las tablas de sesiones aún no están disponibles.'], 200);
        }

        $capacitacion = $capacitacione;
        $sesiones = $capacitacion->sesiones()->with('registros')->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($sesiones as $sesion) {
            $citadosIds = $sesion->citados_ids ?? [];
            $registros = $sesion->registros;

            // Citados que asistieron
            $citadosAsistieron = $registros->where('es_citado', true);
            // No citados que asistieron
            $noCitadosAsistieron = $registros->where('es_citado', false);
            // Citados que NO asistieron
            $idsAsistieron = $registros->where('es_citado', true)->pluck('user_id')->filter()->toArray();
            $citadosNoAsistieron = [];
            foreach ($citadosIds as $uid) {
                if (!in_array($uid, $idsAsistieron)) {
                    $user = User::find($uid);
                    if ($user) {
                        $citadosNoAsistieron[] = [
                            'nombre' => $user->name . ' ' . ($user->apellido1 ?? '') . ' ' . ($user->apellido2 ?? ''),
                            'identificacion' => $user->identificacion ?? '—',
                        ];
                    }
                }
            }

            $data[] = [
                'id' => $sesion->id,
                'fecha' => $sesion->fecha->format('d/m/Y'),
                'hora_inicio' => $sesion->hora_inicio ? \Carbon\Carbon::parse($sesion->hora_inicio)->format('H:i') : '—',
                'hora_fin' => $sesion->hora_fin ? \Carbon\Carbon::parse($sesion->hora_fin)->format('H:i') : '—',
                'created_at' => $sesion->created_at->format('d/m/Y H:i'),
                'total_citados' => count($citadosIds),
                'citados_asistieron' => $citadosAsistieron->map(fn($r) => [
                    'nombre' => $r->nombre . ' ' . ($r->apellidos ?? ''),
                    'identificacion' => $r->identificacion,
                    'area_servicio' => $r->area_servicio,
                    'cargo' => $r->cargo,
                    'tipo_contrato' => $r->tipo_contrato,
                    'hora_registro' => $r->created_at->format('d/m/Y H:i'),
                ])->values(),
                'no_citados_asistieron' => $noCitadosAsistieron->map(fn($r) => [
                    'nombre' => $r->nombre . ' ' . ($r->apellidos ?? ''),
                    'identificacion' => $r->identificacion,
                    'area_servicio' => $r->area_servicio,
                    'cargo' => $r->cargo,
                    'tipo_contrato' => $r->tipo_contrato,
                    'hora_registro' => $r->created_at->format('d/m/Y H:i'),
                ])->values(),
                'citados_no_asistieron' => $citadosNoAsistieron,
            ];
        }

        return response()->json(['ok' => true, 'capacitacion' => $capacitacion->titulo, 'sesiones' => $data]);
    }

    // ─── Exportar Excel (.xlsx) de una sesión ───
    public function exportarSesion(Capacitacion $capacitacione, CapacitacionSesion $sesion)
    {
        if (!Schema::hasTable('capacitacion_sesiones')) {
            abort(404, 'Tablas de sesiones no disponibles.');
        }

        if ($sesion->capacitacion_id !== $capacitacione->id) {
            abort(404);
        }

        $registros = $sesion->registros()->orderBy('created_at')->get();
        $citadosIds = $sesion->citados_ids ?? [];

        // Citados que NO asistieron
        $idsAsistieron = $registros->where('es_citado', true)->pluck('user_id')->filter()->toArray();
        $ausentes = [];
        foreach ($citadosIds as $uid) {
            if (!in_array($uid, $idsAsistieron)) {
                $user = User::find($uid);
                if ($user) {
                    $ausentes[] = $user;
                }
            }
        }

        $filename = 'asistencia_' . Str::slug($capacitacione->titulo) . '_sesion_' . $sesion->fecha->format('Y-m-d') . '.xls';

        // Generar XML Spreadsheet 2003 (formato nativo de Excel)
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"';
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel">' . "\n";

        // Estilos
        $xml .= '<Styles>';
        $xml .= '<Style ss:ID="header"><Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="10"/><Interior ss:Color="#2E3A75" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/></Borders></Style>';
        $xml .= '<Style ss:ID="title"><Font ss:Bold="1" ss:Size="14" ss:Color="#2E3A75"/></Style>';
        $xml .= '<Style ss:ID="subtitle"><Font ss:Bold="1" ss:Size="10" ss:Color="#555555"/></Style>';
        $xml .= '<Style ss:ID="section"><Font ss:Bold="1" ss:Size="11" ss:Color="#FFFFFF"/><Interior ss:Color="#3B4A8A" ss:Pattern="Solid"/></Style>';
        $xml .= '<Style ss:ID="default"><Font ss:Size="10"/><Alignment ss:Vertical="Center"/></Style>';
        $xml .= '<Style ss:ID="alt"><Font ss:Size="10"/><Interior ss:Color="#F0F2F8" ss:Pattern="Solid"/><Alignment ss:Vertical="Center"/></Style>';
        $xml .= '<Style ss:ID="badge_si"><Font ss:Bold="1" ss:Color="#28A745" ss:Size="10"/><Alignment ss:Horizontal="Center"/></Style>';
        $xml .= '<Style ss:ID="badge_no"><Font ss:Bold="1" ss:Color="#DC3545" ss:Size="10"/><Alignment ss:Horizontal="Center"/></Style>';
        $xml .= '</Styles>';

        // ─── Hoja 1: Asistentes ───
        $xml .= '<Worksheet ss:Name="Asistentes">';
        $xml .= '<Table>';

        // Anchos de columna
        $xml .= '<Column ss:Width="180"/><Column ss:Width="160"/><Column ss:Width="120"/><Column ss:Width="160"/><Column ss:Width="140"/><Column ss:Width="130"/><Column ss:Width="70"/><Column ss:Width="90"/><Column ss:Width="140"/>';

        // Título
        $xml .= '<Row ss:Height="30"><Cell ss:StyleID="title" ss:MergeAcross="8"><Data ss:Type="String">REPORTE DE ASISTENCIA</Data></Cell></Row>';
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">Capacitación:</Data></Cell><Cell ss:MergeAcross="3"><Data ss:Type="String">' . $this->xmlEscape($capacitacione->titulo) . '</Data></Cell></Row>';
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">Fecha sesión:</Data></Cell><Cell><Data ss:Type="String">' . $sesion->fecha->format('d/m/Y') . '</Data></Cell>';
        $xml .= '<Cell ss:Index="4" ss:StyleID="subtitle"><Data ss:Type="String">Horario:</Data></Cell><Cell><Data ss:Type="String">' . ($sesion->hora_inicio ?? '—') . ' - ' . ($sesion->hora_fin ?? '—') . '</Data></Cell></Row>';
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">Instructor:</Data></Cell><Cell><Data ss:Type="String">' . $this->xmlEscape($capacitacione->instructor ?? '—') . '</Data></Cell>';
        $xml .= '<Cell ss:Index="4" ss:StyleID="subtitle"><Data ss:Type="String">Total asistentes:</Data></Cell><Cell><Data ss:Type="Number">' . $registros->count() . '</Data></Cell></Row>';
        $xml .= '<Row></Row>';

        // Encabezados
        $headers = ['Nombre', 'Apellidos', 'Identificación', 'Área/Servicio', 'Cargo', 'Tipo Vinculación', 'Citado', 'Autoriza Firma', 'Fecha/Hora Registro'];
        $xml .= '<Row ss:Height="25">';
        foreach ($headers as $h) {
            $xml .= '<Cell ss:StyleID="header"><Data ss:Type="String">' . $h . '</Data></Cell>';
        }
        $xml .= '</Row>';

        // Datos
        $rowIdx = 0;
        foreach ($registros as $r) {
            $style = ($rowIdx % 2 === 0) ? 'default' : 'alt';
            $xml .= '<Row>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->nombre) . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->apellidos ?? '—') . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->identificacion) . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->area_servicio ?? '—') . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->cargo ?? '—') . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->tipo_contrato ?? '—') . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . ($r->es_citado ? 'badge_si' : 'badge_no') . '"><Data ss:Type="String">' . ($r->es_citado ? 'Sí' : 'No') . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . ($r->autoriza_firma ? 'badge_si' : 'badge_no') . '"><Data ss:Type="String">' . ($r->autoriza_firma ? 'Sí' : 'No') . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $r->created_at->format('d/m/Y H:i:s') . '</Data></Cell>';
            $xml .= '</Row>';
            $rowIdx++;
        }

        $xml .= '</Table></Worksheet>';

        // ─── Hoja 2: Ausentes ───
        $xml .= '<Worksheet ss:Name="Citados Ausentes">';
        $xml .= '<Table>';
        $xml .= '<Column ss:Width="250"/><Column ss:Width="150"/>';
        $xml .= '<Row ss:Height="25"><Cell ss:StyleID="header"><Data ss:Type="String">Nombre</Data></Cell><Cell ss:StyleID="header"><Data ss:Type="String">Identificación</Data></Cell></Row>';

        foreach ($ausentes as $u) {
            $nombre = $u->name . ' ' . ($u->apellido1 ?? '') . ' ' . ($u->apellido2 ?? '');
            $xml .= '<Row><Cell ss:StyleID="default"><Data ss:Type="String">' . $this->xmlEscape(trim($nombre)) . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="default"><Data ss:Type="String">' . $this->xmlEscape($u->identificacion ?? '—') . '</Data></Cell></Row>';
        }

        if (empty($ausentes)) {
            $xml .= '<Row><Cell ss:MergeAcross="1"><Data ss:Type="String">Todos los citados asistieron.</Data></Cell></Row>';
        }

        $xml .= '</Table></Worksheet>';

        // ─── Hoja 3: No Citados que Asistieron ───
        $noCitados = $registros->where('es_citado', false);
        $xml .= '<Worksheet ss:Name="No Citados">';
        $xml .= '<Table>';
        $xml .= '<Column ss:Width="180"/><Column ss:Width="160"/><Column ss:Width="120"/><Column ss:Width="160"/><Column ss:Width="140"/><Column ss:Width="130"/><Column ss:Width="140"/>';

        $headersNC = ['Nombre', 'Apellidos', 'Identificación', 'Área/Servicio', 'Cargo', 'Tipo Vinculación', 'Fecha/Hora Registro'];
        $xml .= '<Row ss:Height="25">';
        foreach ($headersNC as $h) {
            $xml .= '<Cell ss:StyleID="header"><Data ss:Type="String">' . $h . '</Data></Cell>';
        }
        $xml .= '</Row>';

        $rowIdx = 0;
        foreach ($noCitados as $r) {
            $style = ($rowIdx % 2 === 0) ? 'default' : 'alt';
            $xml .= '<Row>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->nombre) . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->apellidos ?? '—') . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->identificacion) . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->area_servicio ?? '—') . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->cargo ?? '—') . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $this->xmlEscape($r->tipo_contrato ?? '—') . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $r->created_at->format('d/m/Y H:i:s') . '</Data></Cell>';
            $xml .= '</Row>';
            $rowIdx++;
        }

        if ($noCitados->isEmpty()) {
            $xml .= '<Row><Cell ss:MergeAcross="6"><Data ss:Type="String">No hubo asistentes no citados.</Data></Cell></Row>';
        }

        $xml .= '</Table></Worksheet>';

        // ─── Hoja 4: Resumen ───
        $xml .= '<Worksheet ss:Name="Resumen">';
        $xml .= '<Table>';
        $xml .= '<Column ss:Width="200"/><Column ss:Width="100"/>';
        $xml .= '<Row ss:Height="25"><Cell ss:StyleID="title" ss:MergeAcross="1"><Data ss:Type="String">RESUMEN</Data></Cell></Row>';
        $xml .= '<Row></Row>';
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">Total citados</Data></Cell><Cell><Data ss:Type="Number">' . count($citadosIds) . '</Data></Cell></Row>';
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">Citados que asistieron</Data></Cell><Cell><Data ss:Type="Number">' . $registros->where('es_citado', true)->count() . '</Data></Cell></Row>';
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">No citados que asistieron</Data></Cell><Cell><Data ss:Type="Number">' . $registros->where('es_citado', false)->count() . '</Data></Cell></Row>';
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">Citados ausentes</Data></Cell><Cell><Data ss:Type="Number">' . count($ausentes) . '</Data></Cell></Row>';
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">Total asistentes</Data></Cell><Cell><Data ss:Type="Number">' . $registros->count() . '</Data></Cell></Row>';
        $xml .= '<Row></Row>';
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">Generado:</Data></Cell><Cell><Data ss:Type="String">' . now()->format('d/m/Y H:i:s') . '</Data></Cell></Row>';
        $xml .= '</Table></Worksheet>';

        $xml .= '</Workbook>';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function xmlEscape(string $str): string
    {
        return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
