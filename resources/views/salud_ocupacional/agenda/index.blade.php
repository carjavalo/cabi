@extends('layouts.app')

@section('title', 'Agenda Médica Ocupacional')
@section('header', 'Salud Ocupacional · Agenda Médica')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    :root{
        --so-brand:#2e3a75; --so-brand-d:#232c5c; --so-brand-l:#4657a8;
        --so-bg:#eef0fb; --so-soft:#f4f6fb; --so-line:#e1e5f2; --so-line2:#d5daea;
        --so-text:#1f2440; --so-mut:#7c82a0; --so-ok:#2e9e6b; --so-warn:#c98a1e; --so-bad:#c4453b; --so-info:#2f6fb0; --so-violet:#6d4bb8;
    }
    .ag-wrap{ color:var(--so-text); }
    /* Encabezado institucional (igual al Concepto Médico) */
    .so-hero{ background:linear-gradient(120deg,var(--so-brand),var(--so-brand-l));color:#fff;border-radius:16px;padding:18px 22px;margin-bottom:18px;
        display:flex;align-items:center;gap:16px;flex-wrap:wrap;box-shadow:0 14px 30px -18px rgba(46,58,117,.75); }
    .so-hero .badge-huv{ width:52px;height:52px;border-radius:13px;background:rgba(255,255,255,.16);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:17px;flex-shrink:0; }
    .so-hero small{ text-transform:uppercase;letter-spacing:.14em;opacity:.85;font-size:11px; }
    .so-hero h4{ margin:2px 0 0;font-weight:800;letter-spacing:-.01em; }
    .so-chip-date{ margin-left:auto;background:rgba(255,255,255,.14);border-radius:11px;padding:8px 14px;font-weight:600;display:flex;align-items:center;gap:9px; }
    .so-live-dot{ width:9px;height:9px;border-radius:50%;background:#7ee2b0;box-shadow:0 0 0 3px rgba(126,226,176,.3); }

    .so-card{ background:#fff;border:1px solid var(--so-line);border-radius:16px;box-shadow:0 1px 2px rgba(46,58,117,.05),0 16px 34px -26px rgba(46,58,117,.4); }
    .so-lbl{ display:block;font-size:11.5px;font-weight:700;letter-spacing:.03em;text-transform:uppercase;color:var(--so-brand);margin-bottom:6px; }
    .so-in{ width:100%;padding:9px 12px;border:1px solid var(--so-line2);border-radius:10px;font-size:14px;background:#fff;outline:none;transition:border .15s,box-shadow .15s; }
    .so-in:focus{ border-color:var(--so-brand-l);box-shadow:0 0 0 3px rgba(46,58,117,.12); }
    .so-in[readonly]{ background:var(--so-soft);color:#4a4f66; }
    .so-grid{ display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px 18px; }
    .so-btn{ display:inline-flex;align-items:center;justify-content:center;gap:7px;height:38px;padding:0 16px;border-radius:10px;font-size:13.5px;font-weight:700;cursor:pointer;border:none;white-space:nowrap; }
    .so-btn.prim{ background:var(--so-brand);color:#fff;box-shadow:0 10px 22px -12px rgba(46,58,117,.8); }
    .so-btn.prim:hover{ background:var(--so-brand-d); }
    .so-btn.ghost{ background:#fff;color:var(--so-brand);border:1.5px solid var(--so-line2); }
    .so-btn.ghost:hover{ background:var(--so-bg); }

    /* Título + indicadores */
    .ag-top{ display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:16px;margin-bottom:16px; }
    .ag-eyebrow{ font-family:'Courier New',monospace;font-size:11.5px;letter-spacing:.14em;text-transform:uppercase;color:var(--so-brand);font-weight:700;display:flex;align-items:center;gap:7px; }
    .ag-top h2{ font-size:22px;font-weight:800;letter-spacing:-.02em;margin:4px 0 2px; }
    .ag-top p{ color:var(--so-mut);font-size:14px;margin:0;max-width:640px; }
    .ag-kpis{ display:grid;grid-template-columns:repeat(4,minmax(120px,1fr));gap:10px; }
    .ag-kpi{ background:#fff;border:1px solid var(--so-line);border-radius:14px;padding:10px 14px; }
    .ag-kpi .k{ font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;color:var(--so-mut);font-weight:700; }
    .ag-kpi .v{ display:flex;justify-content:space-between;align-items:baseline;margin-top:3px;font-size:22px;font-weight:800; }
    .ag-kpi .v i{ font-size:15px;opacity:.8; }

    /* Barra de herramientas */
    .ag-tools{ display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;align-items:center;padding:14px;margin-bottom:16px; }
    .ag-tools .left, .ag-tools .right{ display:flex;flex-wrap:wrap;gap:8px;align-items:center; }
    .ag-search{ position:relative;min-width:240px;flex:1; }
    .ag-search i{ position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--so-mut); }
    .ag-search .so-in{ padding-left:34px;height:38px; }
    .ag-date{ display:flex;align-items:center;gap:2px;background:var(--so-soft);border:1px solid var(--so-line2);border-radius:10px;padding:2px; }
    .ag-date button{ border:none;background:none;width:30px;height:32px;border-radius:8px;color:var(--so-brand);cursor:pointer; }
    .ag-date button:hover{ background:var(--so-bg); }
    .ag-date input{ border:none;background:none;font-weight:700;color:var(--so-brand);font-size:13.5px;outline:none;height:32px; }
    .ag-tools select.so-in{ width:auto;height:38px;padding:0 10px; }

    /* Tabla de la agenda */
    .ag-table-wrap{ overflow-x:auto; }
    table.ag-table{ width:100%;border-collapse:collapse;font-size:13px; }
    .ag-table thead th{ background:var(--so-brand);color:#fff;font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:700;padding:11px 10px;white-space:nowrap;position:sticky;top:0; }
    .ag-table td{ padding:7px 10px;border-bottom:1px solid var(--so-line);vertical-align:middle; }
    .ag-table tbody tr:nth-child(even){ background:var(--so-soft); }
    .ag-table tbody tr:hover{ background:var(--so-bg); }
    .ag-table .c{ text-align:center; }
    .ag-hora{ font-weight:800;color:var(--so-brand);font-variant-numeric:tabular-nums; }
    .ag-fecha{ color:var(--so-mut);font-variant-numeric:tabular-nums;white-space:nowrap; }
    .ag-func{ display:flex;align-items:center;gap:9px;min-width:230px; }
    .ag-av{ width:30px;height:30px;border-radius:50%;background:var(--so-bg);color:var(--so-brand);font-size:11px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .ag-func .n{ font-weight:700;color:var(--so-text);line-height:1.2; }
    .ag-func .s{ font-size:11px;color:var(--so-mut); }
    .ag-useredit{ width:24px;height:24px;border-radius:7px;border:1.5px solid var(--so-brand);background:#fff;color:var(--so-brand);font-size:11px;cursor:pointer;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center; }
    .ag-useredit:hover{ background:var(--so-brand);color:#fff; }
    .ag-link{ color:var(--so-info);white-space:nowrap; }
    .ag-mail{ display:block;max-width:190px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--so-mut); }
    .ag-obs{ max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--so-mut); }
    .ag-chip{ display:inline-block;font-size:10.5px;font-weight:700;padding:1px 7px;border-radius:99px;background:var(--so-bg);color:var(--so-brand);margin:2px 3px 0 0; }
    .ag-sel{ height:28px;border-radius:99px;border:1.5px solid transparent;font-size:11.5px;font-weight:700;padding:0 8px;cursor:pointer;outline:none; }
    .st-programada{ background:#fbf3e4;color:#92611a; }  .st-en_espera{ background:#e4effa;color:var(--so-info); }
    .st-en_consulta{ background:#efe9fb;color:var(--so-violet); } .st-completada{ background:#eaf7f0;color:var(--so-ok); }
    .st-cancelada{ background:#fbebe9;color:var(--so-bad); }
    .as-pendiente{ background:var(--so-soft);color:var(--so-mut); } .as-en_espera{ background:#e4effa;color:var(--so-info); }
    .as-asistio{ background:#eaf7f0;color:var(--so-ok); } .as-no_asistio{ background:#fbebe9;color:var(--so-bad); }
    .ag-mx{ display:inline-flex;align-items:center;gap:5px;border:none;border-radius:8px;padding:3px 9px;font-size:11.5px;font-weight:700;cursor:pointer; }
    .mx-si{ background:#eaf7f0;color:var(--so-ok); } .mx-no{ background:var(--so-soft);color:var(--so-mut); } .mx-en_proceso{ background:#e4effa;color:var(--so-info); }
    .ag-act{ display:inline-flex;align-items:center;justify-content:center;gap:5px;height:28px;min-width:28px;padding:0 8px;border-radius:8px;border:none;background:none;color:var(--so-brand);cursor:pointer;font-size:12px;font-weight:700;text-decoration:none; }
    .ag-act:hover{ background:var(--so-bg);color:var(--so-brand);text-decoration:none; }
    .ag-act.go{ background:var(--so-brand);color:#fff; }
    .ag-act.go:hover{ background:var(--so-brand-d);color:#fff; }
    tr.ag-libre td{ color:#aab0c6; }
    .ag-table tbody tr.ag-bloq{ background:repeating-linear-gradient(135deg,#fff,#fff 8px,#fdf3f2 8px,#fdf3f2 16px); }
    tr.ag-libre .lib{ font-style:italic; }
    tr.ag-cancel .n, tr.ag-cancel .ag-hora{ text-decoration:line-through;opacity:.7; }
    .ag-asignar{ border:1.5px dashed var(--so-brand-l);background:#fff;color:var(--so-brand);border-radius:8px;padding:3px 10px;font-size:12px;font-weight:700;cursor:pointer; }
    .ag-asignar:hover{ background:var(--so-brand);color:#fff;border-style:solid; }
    .ag-foot{ display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px;padding:11px 16px;background:var(--so-soft);color:var(--so-mut);font-size:12.5px;border-radius:0 0 16px 16px; }
    .ag-empty{ padding:30px;text-align:center;color:var(--so-mut); }

    /* Modal */
    .ag-sug-wrap{ position:relative; }
    .ag-sug{ position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:30;background:#fff;border:1.5px solid var(--so-line);border-radius:10px;box-shadow:0 10px 24px rgba(20,25,60,.12);max-height:260px;overflow:auto; }
    .ag-sug button{ display:flex;justify-content:space-between;gap:10px;width:100%;text-align:left;padding:9px 12px;border:0;background:none;cursor:pointer;font-size:13.5px; }
    .ag-sug button:hover{ background:var(--so-bg); }
    .ag-sug .n{ font-weight:600;color:var(--so-brand); } .ag-sug .m{ font-size:12px;opacity:.7;white-space:nowrap; }
    .ag-sug .empty{ padding:10px 12px;font-size:13px;color:var(--so-mut); }
    .ag-sel-user{ display:none;align-items:center;gap:10px;background:var(--so-bg);border:1px solid #d6dbf0;border-radius:10px;padding:9px 12px;margin-top:8px; }
    .ag-enf{ display:flex;flex-wrap:wrap;gap:8px; }
    .ag-enf label{ display:inline-flex;align-items:center;gap:7px;border:1.5px solid var(--so-line2);border-radius:99px;padding:6px 12px;font-size:13px;cursor:pointer;margin:0; }
    .ag-enf input{ accent-color:var(--so-brand); }
    .ag-note{ display:flex;gap:9px;background:var(--so-bg);border:1px solid #d6dbf0;border-radius:10px;padding:10px 13px;font-size:13px;color:#3a3e56;margin-bottom:14px; }

    @media(max-width:768px){ .ag-kpis{ grid-template-columns:repeat(2,1fr); width:100%; } }
    @media print{
        .main-header,.main-sidebar,.main-footer,.content-header,.so-hero,.ag-tools,.ag-kpis,.ag-noprint{ display:none !important; }
        .content-wrapper{ margin:0 !important;background:#fff !important; }
        .ag-table thead th{ background:#fff !important;color:#000 !important;border-bottom:2px solid #000; }
        .ag-sel,.ag-mx{ border:none;background:none !important;-webkit-appearance:none;appearance:none; }
        .so-card{ box-shadow:none;border:none; }
        @page{ size:landscape;margin:10mm; }
    }
</style>
@endpush

@section('content')
<div class="ag-wrap">

    <div class="so-hero">
        <div class="badge-huv">HUV</div>
        <div>
            <small>Hospital Universitario del Valle · Evaristo García E.S.E</small>
            <h4>Agenda Médica Ocupacional</h4>
        </div>
        <div class="so-chip-date">
            <span class="so-live-dot"></span>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}</span>
        </div>
    </div>

    @if($migracionPendiente)
    <div class="alert alert-warning" style="border-radius:12px;">
        <strong><i class="fas fa-database"></i> Configuración pendiente en el servidor:</strong>
        falta crear la tabla de la agenda. Ejecuta <code>php&nbsp;artisan&nbsp;migrate&nbsp;--force</code> en el hosting.
    </div>
    @endif

    {{-- Título e indicadores del día --}}
    <div class="ag-top">
        <div>
            <div class="ag-eyebrow"><i class="far fa-calendar-check"></i> Gestión de citas · Tiempo de atención <span class="js-duracion">{{ $config['duracion_minutos'] }}</span> min</div>
            <h2>Agenda de pacientes · Salud Ocupacional</h2>
            <p>Turnos médicos laborales de los trabajadores de vinculación <strong style="color:var(--so-brand)">Planta</strong>, control de asistencia e ingreso a la matriz de vigilancia. Los agendados tienen prioridad en el Concepto Médico.</p>
        </div>
        <div class="ag-kpis">
            <div class="ag-kpi"><div class="k">Citas del día</div><div class="v" style="color:var(--so-brand)"><span id="k-total">0</span><i class="far fa-calendar"></i></div></div>
            <div class="ag-kpi"><div class="k">Atendidos</div><div class="v" style="color:var(--so-ok)"><span id="k-atendidos">0</span><i class="far fa-check-circle"></i></div></div>
            <div class="ag-kpi"><div class="k">En espera</div><div class="v" style="color:var(--so-info)"><span id="k-espera">0</span><i class="fas fa-hourglass-half"></i></div></div>
            <div class="ag-kpi"><div class="k">En matriz</div><div class="v" style="color:var(--so-ok)"><span id="k-matriz">0%</span><i class="fas fa-user-shield"></i></div></div>
        </div>
    </div>

    {{-- Barra de herramientas --}}
    <div class="so-card ag-tools">
        <div class="left">
            <div class="ag-search"><i class="fas fa-search"></i><input type="text" id="ag-q" class="so-in" placeholder="Buscar funcionario o documento…"></div>
            <div class="ag-date">
                <button type="button" id="ag-prev" title="Día anterior"><i class="fas fa-chevron-left"></i></button>
                <input type="date" id="ag-fecha" value="{{ $fecha }}">
                <button type="button" id="ag-next" title="Día siguiente"><i class="fas fa-chevron-right"></i></button>
            </div>
            <button type="button" class="so-btn ghost" id="ag-hoy" style="height:38px;padding:0 12px;">Hoy</button>
            <select id="ag-f-motivo" class="so-in">
                <option value="">Todos los motivos</option>
                @foreach($motivos as $k => $m)<option value="{{ $k }}">{{ $m }}</option>@endforeach
            </select>
            <select id="ag-f-estado" class="so-in">
                <option value="">Todos los estados</option>
                @foreach($estados as $k => $e)<option value="{{ $k }}">{{ $e }}</option>@endforeach
            </select>
        </div>
        <div class="right">
            <button type="button" class="so-btn ghost" id="ag-config" title="Tiempo de atención y jornada"><i class="fas fa-cog"></i> Configurar</button>
            <button type="button" class="so-btn ghost" id="ag-bloquear" style="color:var(--so-bad);"><i class="fas fa-lock"></i> Bloquear horario</button>
            <button type="button" class="so-btn ghost" onclick="window.print()"><i class="fas fa-print"></i> Imprimir</button>
            <button type="button" class="so-btn ghost" id="ag-export"><i class="fas fa-file-excel"></i> Exportar Excel</button>
            <button type="button" class="so-btn prim" id="ag-nueva"><i class="fas fa-plus-circle"></i> Agendar funcionario</button>
        </div>
    </div>

    {{-- Agenda del día --}}
    <div class="so-card">
        <div class="ag-table-wrap">
            <table class="ag-table" id="ag-table">
                <thead>
                    <tr>
                        <th class="c">Fecha</th>
                        <th class="c">Hora</th>
                        <th>Nombre del funcionario</th>
                        <th>Teléfono</th>
                        <th>Correo electrónico</th>
                        <th>Motivo consulta</th>
                        <th class="c">Asistencia</th>
                        <th class="c">Estado</th>
                        <th class="c">Ingreso matriz</th>
                        <th>Observaciones</th>
                        <th class="c ag-noprint">Acciones</th>
                    </tr>
                </thead>
                <tbody id="ag-body">
                    <tr><td colspan="11" class="ag-empty"><i class="fas fa-spinner fa-spin"></i> Cargando agenda…</td></tr>
                </tbody>
            </table>
        </div>
        <div class="ag-foot">
            <span id="ag-resumen">—</span>
            <span>Tiempo de atención por cita: <strong class="js-duracion">{{ $config['duracion_minutos'] }}</strong> minutos · Jornada <span id="ag-jornada"></span></span>
        </div>
    </div>
</div>

{{-- MODAL CITA (crear / editar) --}}
<div class="modal fade" id="citaModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
      <div class="modal-header" style="background:var(--so-brand);color:#fff;border:none;">
        <div>
            <h5 class="modal-title" id="citaModalTitle"><i class="far fa-calendar-plus mr-2"></i>Programar cita ocupacional</h5>
            <small style="opacity:.8;">Completa los campos requeridos para la agenda médica</small>
        </div>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="cf-id">
        <input type="hidden" id="cf-user_id">
        <div class="so-grid">
            <div><label class="so-lbl">Fecha *</label><input type="date" id="cf-fecha" class="so-in"></div>
            <div><label class="so-lbl">Hora *</label>
                <select id="cf-hora" class="so-in"></select>
                <small id="cf-hora-info" style="color:var(--so-mut);"></small>
            </div>
        </div>

        <div style="margin-top:14px;" class="ag-sug-wrap">
            <label class="so-lbl">Funcionario (cédula o nombre) *</label>
            <input type="text" id="cf-buscar" class="so-in" placeholder="Digita la cédula o el nombre del trabajador de Planta…" autocomplete="off">
            <div id="cf-sug" class="ag-sug" style="display:none;"></div>
            <div id="cf-sel" class="ag-sel-user">
                <div class="ag-av" id="cf-sel-av">—</div>
                <div style="flex:1;min-width:0;"><div style="font-weight:700;color:var(--so-brand);" id="cf-sel-nombre"></div><div style="font-size:12px;color:var(--so-mut);" id="cf-sel-doc"></div></div>
                <button type="button" class="ag-useredit" id="cf-sel-edit" title="Editar o corregir datos del funcionario"><i class="fas fa-plus"></i></button>
            </div>
        </div>

        <div class="so-grid" style="margin-top:14px;">
            <div><label class="so-lbl">Teléfono</label><input type="text" id="cf-telefono" class="so-in" readonly placeholder="Se completa con el funcionario"></div>
            <div><label class="so-lbl">Correo electrónico</label><input type="text" id="cf-email" class="so-in" readonly placeholder="Se completa con el funcionario"></div>
            <div><label class="so-lbl">Cargo</label><input type="text" id="cf-cargo" class="so-in" readonly placeholder="Se completa con el funcionario"></div>
        </div>

        <div style="margin-top:14px;">
            <label class="so-lbl">Motivo consulta (tipo de atención) *</label>
            <select id="cf-motivo" class="so-in">
                @foreach($motivos as $k => $m)<option value="{{ $k }}" {{ $k==='periodico'?'selected':'' }}>{{ $m }}</option>@endforeach
            </select>
        </div>
        <div style="margin-top:14px;">
            <label class="so-lbl">Énfasis</label>
            <div class="ag-enf">
                @foreach($enfasis as $k => $e)<label><input type="checkbox" name="cf-enfasis" value="{{ $k }}"> {{ $e }}</label>@endforeach
            </div>
        </div>

        <div class="so-grid" style="margin-top:14px;">
            <div><label class="so-lbl">Asistencia</label>
                <select id="cf-asistencia" class="so-in">@foreach($asistencias as $k => $a)<option value="{{ $k }}">{{ $a }}</option>@endforeach</select></div>
            <div><label class="so-lbl">Estado</label>
                <select id="cf-estado" class="so-in">@foreach($estados as $k => $e)<option value="{{ $k }}">{{ $e }}</option>@endforeach</select></div>
            <div><label class="so-lbl">Ingreso matriz</label>
                <select id="cf-matriz" class="so-in">@foreach($matriz as $k => $m)<option value="{{ $k }}">{{ $m }}</option>@endforeach</select></div>
        </div>
        <div style="margin-top:14px;">
            <label class="so-lbl">Observaciones</label>
            <textarea id="cf-observaciones" rows="2" class="so-in" placeholder="Indicaciones previas (ayuno, reposo auditivo, paraclínicos pendientes…)"></textarea>
        </div>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--so-line);">
        <button type="button" class="so-btn ghost" data-dismiss="modal">Cancelar</button>
        <button type="button" class="so-btn prim" id="cf-guardar"><i class="fas fa-save"></i> Guardar en agenda</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL CONFIGURACIÓN (tiempo de atención y jornada) --}}
<div class="modal fade" id="configModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
      <div class="modal-header" style="background:var(--so-brand);color:#fff;border:none;">
        <h5 class="modal-title"><i class="fas fa-cog mr-2"></i>Configurar agenda</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="ag-note"><i class="fas fa-info-circle" style="color:var(--so-brand);margin-top:2px;"></i><span>Aplica a las citas nuevas. Las citas ya agendadas conservan su horario y su tiempo de atención.</span></div>
        <label class="so-lbl">Tiempo de atención por cita (minutos) *</label>
        <input type="number" id="cg-duracion" class="so-in" min="5" max="240" step="5" list="dl-duraciones">
        <datalist id="dl-duraciones">@foreach($duraciones as $d)<option value="{{ $d }}">@endforeach</datalist>
        <div class="so-grid" style="margin-top:14px;">
            <div><label class="so-lbl">Mañana · inicio *</label><input type="time" id="cg-manana_inicio" class="so-in"></div>
            <div><label class="so-lbl">Mañana · fin *</label><input type="time" id="cg-manana_fin" class="so-in"></div>
        </div>
        <label style="display:flex;align-items:center;gap:8px;margin:14px 0 8px;font-size:13.5px;cursor:pointer;"><input type="checkbox" id="cg-con-tarde" style="accent-color:var(--so-brand);"> Atender también en la tarde</label>
        <div class="so-grid" id="cg-tarde">
            <div><label class="so-lbl">Tarde · inicio</label><input type="time" id="cg-tarde_inicio" class="so-in"></div>
            <div><label class="so-lbl">Tarde · fin</label><input type="time" id="cg-tarde_fin" class="so-in"></div>
        </div>
        <div id="cg-preview" style="margin-top:14px;font-size:13px;color:var(--so-brand);font-weight:600;"></div>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--so-line);">
        <button type="button" class="so-btn ghost" data-dismiss="modal">Cancelar</button>
        <button type="button" class="so-btn prim" id="cg-guardar"><i class="fas fa-save"></i> Guardar configuración</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL BLOQUEO DE HORARIO --}}
<div class="modal fade" id="bloqueoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
      <div class="modal-header" style="background:var(--so-bad);color:#fff;border:none;">
        <h5 class="modal-title"><i class="fas fa-lock mr-2"></i>Bloquear horario</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="ag-note"><i class="fas fa-info-circle" style="color:var(--so-brand);margin-top:2px;"></i><span>En un horario bloqueado no se pueden agendar citas (reuniones, capacitaciones, ausencia del médico, etc.).</span></div>
        <label class="so-lbl">Fecha *</label>
        <input type="date" id="bf-fecha" class="so-in">
        <label style="display:flex;align-items:center;gap:8px;margin:14px 0 8px;font-size:13.5px;cursor:pointer;"><input type="checkbox" id="bf-dia" style="accent-color:var(--so-bad);"> Bloquear todo el día</label>
        <div class="so-grid" id="bf-horas">
            <div><label class="so-lbl">Desde *</label><input type="time" id="bf-inicio" class="so-in"></div>
            <div><label class="so-lbl">Hasta *</label><input type="time" id="bf-fin" class="so-in"></div>
        </div>
        <label class="so-lbl" style="margin-top:14px;">Motivo</label>
        <input type="text" id="bf-motivo" class="so-in" maxlength="150" placeholder="Ej. Comité, capacitación, ausencia del médico…">
        <div class="so-lbl" style="margin-top:18px;">Bloqueos de la fecha</div>
        <div id="bf-lista" style="font-size:13px;color:var(--so-mut);">—</div>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--so-line);">
        <button type="button" class="so-btn ghost" data-dismiss="modal">Cerrar</button>
        <button type="button" class="so-btn prim" id="bf-guardar" style="background:var(--so-bad);"><i class="fas fa-lock"></i> Bloquear</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL FUNCIONARIO (editar / corregir datos del usuario) --}}
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
      <div class="modal-header" style="background:var(--so-brand);color:#fff;border:none;">
        <h5 class="modal-title"><i class="fas fa-user-edit mr-2"></i>Editar datos del funcionario</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="uf-id">
        <div class="ag-note"><i class="fas fa-info-circle" style="color:var(--so-brand);margin-top:2px;"></i><span>Los cambios se guardan en los datos del trabajador y se reflejan en la agenda y en el Concepto Médico.</span></div>
        <div class="so-grid">
            <div><label class="so-lbl">Nombres *</label><input type="text" id="uf-name" class="so-in"></div>
            <div><label class="so-lbl">Primer apellido</label><input type="text" id="uf-apellido1" class="so-in"></div>
            <div><label class="so-lbl">Segundo apellido</label><input type="text" id="uf-apellido2" class="so-in"></div>
            <div><label class="so-lbl">Tipo de identificación</label>
                <select id="uf-tipo_identificacion" class="so-in">@foreach($tiposIdentificacion as $k => $t)<option value="{{ $k }}">{{ $t }}</option>@endforeach</select></div>
            <div><label class="so-lbl">N.º identificación *</label><input type="text" id="uf-identificacion" class="so-in"></div>
            <div><label class="so-lbl">Teléfono</label><input type="text" id="uf-contacto" class="so-in" maxlength="20"></div>
            <div><label class="so-lbl">Correo electrónico</label><input type="email" id="uf-email" class="so-in"></div>
            <div><label class="so-lbl">Cargo</label><input type="text" id="uf-cargo" class="so-in" list="dl-cargo"></div>
            <div><label class="so-lbl">Servicio</label><input type="text" id="uf-servicio" class="so-in" list="dl-serv"></div>
            <div><label class="so-lbl">EPS</label><input type="text" id="uf-eps" class="so-in" list="dl-eps"></div>
            <div><label class="so-lbl">AFP</label><input type="text" id="uf-afp" class="so-in" list="dl-afp"></div>
            <div><label class="so-lbl">ARL</label><input type="text" id="uf-arl" class="so-in" list="dl-arl"></div>
            <div style="grid-column:1/-1;"><label class="so-lbl">Dirección de residencia</label><input type="text" id="uf-direccionr" class="so-in"></div>
        </div>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--so-line);">
        <button type="button" class="so-btn ghost" data-dismiss="modal">Cancelar</button>
        <button type="button" class="so-btn prim" id="uf-guardar"><i class="fas fa-save"></i> Guardar datos</button>
      </div>
    </div>
  </div>
</div>

<datalist id="dl-cargo">@foreach($cargos as $c)<option value="{{ $c }}">@endforeach</datalist>
<datalist id="dl-serv">@foreach($servicios as $s)<option value="{{ $s }}">@endforeach</datalist>
<datalist id="dl-eps">@foreach($epsList as $e)<option value="{{ $e }}">@endforeach</datalist>
<datalist id="dl-afp">@foreach($afpList as $a)<option value="{{ $a }}">@endforeach</datalist>
<datalist id="dl-arl">@foreach($arlList as $a)<option value="{{ $a }}">@endforeach</datalist>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
    'use strict';
    const $  = (s,ctx=document)=>ctx.querySelector(s);
    const $$ = (s,ctx=document)=>Array.from(ctx.querySelectorAll(s));
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const ROUTES = {
        citas:     "{{ route('salud.agenda.citas') }}",
        pacientes: "{{ route('salud.agenda.pacientes') }}",
        store:     "{{ route('salud.agenda.store') }}",
        update:    "{{ url('salud-ocupacional/agenda/citas') }}",
        paciente:  "{{ url('salud-ocupacional/concepto/paciente') }}",   // GET /{identificacion} · PUT /{id}
        config:    "{{ route('salud.agenda.configuracion') }}",
        bloqueos:  "{{ url('salud-ocupacional/agenda/bloqueos') }}",
    };
    let CONFIG = @json($config);
    const MOTIVOS = @json($motivos);
    const ENFASIS = @json($enfasis);
    const ASISTENCIAS = @json($asistencias);
    const ESTADOS = @json($estados);
    const MATRIZ = @json($matriz);
    const MATRIZ_ICON = { si:'fa-check', no:'fa-minus', en_proceso:'fa-sync-alt' };
    const MATRIZ_SIG = { no:'en_proceso', en_proceso:'si', si:'no' };

    let fecha = $('#ag-fecha').value;
    let citas = [], turnos = [], bloqueos = [];
    const aMin = h => { const [a,b] = String(h||'0:0').split(':').map(Number); return a*60+(b||0); };

    const esc = s => String(s==null?'':s).replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const iniciales = n => (n||'').trim().split(/\s+/).slice(0,2).map(p=>p[0]||'').join('').toUpperCase() || '—';
    const fmtFecha = f => f ? f.split('-').reverse().join('/') : '';
    const toast = (icon, title) => Swal.fire({icon, title, timer:1700, showConfirmButton:false, toast:true, position:'top-end'});
    async function api(url, method='GET', body=null){
        const r = await fetch(url, { method, headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'}, body: body ? JSON.stringify(body) : null });
        const j = await r.json().catch(()=>({}));
        if(!r.ok){ throw new Error(j.message || (j.errors ? Object.values(j.errors).flat().join(' · ') : 'No se pudo completar la operación.')); }
        return j;
    }

    // ─── Carga y pintado de la agenda ───
    async function cargar(){
        try {
            const j = await api(ROUTES.citas+'?fecha='+encodeURIComponent(fecha));
            citas = j.citas || []; turnos = j.turnos || []; bloqueos = j.bloqueos || [];
            if(j.config){ CONFIG = j.config; pintarConfig(); }
            $('#k-total').textContent = j.kpis.total;
            $('#k-atendidos').textContent = j.kpis.atendidos;
            $('#k-espera').textContent = j.kpis.en_espera;
            $('#k-matriz').textContent = j.kpis.matriz_pct+'%';
            pintar();
            try { history.replaceState(null, '', '?fecha='+fecha); } catch(_){}
        } catch(e){
            $('#ag-body').innerHTML = `<tr><td colspan="11" class="ag-empty">${esc(e.message)}</td></tr>`;
        }
    }

    function filaCita(c){
        const sel = (cls, campo, opciones, valor) => `<select class="ag-sel ${cls}-${valor}" data-id="${c.id}" data-campo="${campo}">${
            Object.entries(opciones).map(([k,v])=>`<option value="${k}" ${k===valor?'selected':''}>${esc(v)}</option>`).join('')}</select>`;
        const enf = (c.enfasis||[]).map(e=>`<span class="ag-chip">${esc(ENFASIS[e]||e)}</span>`).join('');
        const acciones = c.concepto_url
            ? `<a class="ag-act" href="${c.concepto_url}" target="_blank" title="Ver concepto emitido"><i class="fas fa-file-medical"></i></a>`
            : (c.estado!=='cancelada' ? `<a class="ag-act go" href="${c.atender_url}" title="Atender en Concepto Médico"><i class="fas fa-stethoscope"></i> Atender</a>` : '');
        return `<tr class="${c.estado==='cancelada'?'ag-cancel':''}" data-id="${c.id}" data-motivo="${c.motivo}" data-estado="${c.estado}">
            <td class="c ag-fecha">${fmtFecha(c.fecha)}</td>
            <td class="c ag-hora">${esc(c.hora)}<div style="font-size:10.5px;font-weight:600;color:var(--so-mut);">a ${esc(c.hora_fin||'')}</div></td>
            <td><div class="ag-func">
                <div class="ag-av">${esc(iniciales(c.nombre))}</div>
                <div style="flex:1;min-width:0;"><div class="n">${esc(c.nombre)}</div><div class="s">${esc(c.tipo_identificacion||'CC')} ${esc(c.identificacion||'')}${c.cargo?' · '+esc(c.cargo):''}</div></div>
                <button type="button" class="ag-useredit ag-noprint" data-user="${esc(c.identificacion||'')}" title="Editar o corregir datos del funcionario"><i class="fas fa-plus"></i></button>
            </div></td>
            <td>${c.telefono ? `<a class="ag-link" href="tel:${esc(c.telefono)}"><i class="fas fa-phone-alt" style="font-size:11px;"></i> ${esc(c.telefono)}</a>` : '<span style="color:#aab0c6">—</span>'}</td>
            <td>${c.email ? `<a class="ag-mail" href="mailto:${esc(c.email)}" title="${esc(c.email)}">${esc(c.email)}</a>` : '<span style="color:#aab0c6">—</span>'}</td>
            <td><div style="font-weight:600;">${esc(c.motivo_label)}</div>${enf}</td>
            <td class="c">${sel('as','asistencia',ASISTENCIAS,c.asistencia)}</td>
            <td class="c">${sel('st','estado',ESTADOS,c.estado)}</td>
            <td class="c"><button type="button" class="ag-mx mx-${c.ingreso_matriz}" data-id="${c.id}" data-valor="${c.ingreso_matriz}" title="Clic para cambiar"><i class="fas ${MATRIZ_ICON[c.ingreso_matriz]||'fa-minus'}"></i> ${esc(MATRIZ[c.ingreso_matriz]||c.ingreso_matriz)}</button></td>
            <td><div class="ag-obs" title="${esc(c.observaciones||'')}">${esc(c.observaciones||'—')}</div></td>
            <td class="c ag-noprint" style="white-space:nowrap;">${acciones}
                <button type="button" class="ag-act" data-editar="${c.id}" title="Editar cita"><i class="fas fa-pen"></i></button></td>
        </tr>`;
    }
    function filaLibre(t){
        return `<tr class="ag-libre" data-libre="1">
            <td class="c ag-fecha">${fmtFecha(fecha)}</td>
            <td class="c ag-hora" style="color:#aab0c6;">${t.hora}<div style="font-size:10.5px;font-weight:600;">a ${t.fin}</div></td>
            <td><div class="ag-func"><div class="ag-av" style="background:var(--so-soft);color:#aab0c6;">--</div><span class="lib">Turno libre / cupo disponible</span></div></td>
            <td>—</td><td>—</td><td>—</td><td class="c">Disponible</td>
            <td class="c"><span class="ag-chip" style="background:var(--so-soft);color:var(--so-mut);">Libre</span></td>
            <td class="c">—</td><td class="lib">Espacio asignable</td>
            <td class="c ag-noprint" style="white-space:nowrap;">
                <button type="button" class="ag-asignar" data-hora="${t.hora}">+ Asignar</button>
                <button type="button" class="ag-act" data-bloquear="${t.hora}" data-fin="${t.fin}" title="Bloquear este horario"><i class="fas fa-lock"></i></button>
            </td>
        </tr>`;
    }
    function filaBloqueo(b){
        const rango = b.dia_completo ? 'Todo el día' : `${b.hora_inicio} a ${b.hora_fin}`;
        return `<tr class="ag-bloq">
            <td class="c ag-fecha">${fmtFecha(fecha)}</td>
            <td class="c ag-hora" style="color:var(--so-bad);">${b.dia_completo ? '<i class="fas fa-ban"></i>' : esc(b.hora_inicio)+'<div style="font-size:10.5px;font-weight:600;">a '+esc(b.hora_fin)+'</div>'}</td>
            <td colspan="8"><div class="ag-func"><div class="ag-av" style="background:#fbebe9;color:var(--so-bad);"><i class="fas fa-lock"></i></div>
                <div><div class="n" style="color:var(--so-bad);">Horario bloqueado · ${esc(rango)}</div><div class="s">${esc(b.motivo || 'Sin motivo registrado')} · no se pueden agendar citas</div></div></div></td>
            <td class="c ag-noprint"><button type="button" class="ag-act" data-desbloquear="${b.id}" title="Desbloquear horario" style="color:var(--so-bad);"><i class="fas fa-lock-open"></i> Desbloquear</button></td>
        </tr>`;
    }

    function pintar(){
        const q = $('#ag-q').value.trim().toLowerCase();
        const fm = $('#ag-f-motivo').value, fe = $('#ag-f-estado').value;
        const filtrando = q || fm || fe;

        const visibles = citas.filter(c=>{
            if(fm && c.motivo!==fm) return false;
            if(fe && c.estado!==fe) return false;
            if(q && !(`${c.nombre} ${c.identificacion} ${c.cargo||''} ${c.email||''}`.toLowerCase().includes(q))) return false;
            return true;
        });

        // Filas ordenadas por hora: bloqueos, citas y turnos libres (según el tiempo de atención configurado)
        const filas = [];
        bloqueos.forEach(b=> filas.push({t: aMin(b.hora_inicio), o:0, html: filaBloqueo(b)}));
        visibles.forEach(c=> filas.push({t: aMin(c.hora), o:1, html: filaCita(c)}));
        let libres = 0;
        if(!filtrando){
            turnos.filter(t=> !t.bloqueo && !(t.ocupado_por||[]).length).forEach(t=>{ filas.push({t: aMin(t.hora), o:2, html: filaLibre(t)}); libres++; });
        }
        filas.sort((a,b)=> a.t-b.t || a.o-b.o);
        $('#ag-body').innerHTML = filas.map(f=>f.html).join('') || `<tr><td colspan="11" class="ag-empty">${filtrando ? 'No hay citas que coincidan con la búsqueda.' : 'No hay turnos configurados para este día.'}</td></tr>`;

        const disponibles = turnos.filter(t=>!t.bloqueo).length;
        const ocupados = turnos.filter(t=>!t.bloqueo && (t.ocupado_por||[]).length).length;
        $('#ag-resumen').innerHTML = `Mostrando <strong>${visibles.length}</strong> de <strong>${citas.length}</strong> citas · <strong>${ocupados}</strong> de <strong>${disponibles}</strong> turnos ocupados${filtrando?'':' · '+libres+' libres'}`
            + (bloqueos.length ? ` · <span style="color:var(--so-bad);font-weight:700;"><i class="fas fa-lock"></i> ${bloqueos.length} bloqueo${bloqueos.length===1?'':'s'}</span>` : '');
    }

    // Cambios rápidos en la tabla (asistencia, estado, matriz)
    $('#ag-body').addEventListener('change', async e=>{
        const s = e.target.closest('.ag-sel'); if(!s) return;
        await actualizar(s.dataset.id, {[s.dataset.campo]: s.value});
    });
    $('#ag-body').addEventListener('click', async e=>{
        const mx = e.target.closest('.ag-mx');
        if(mx){ await actualizar(mx.dataset.id, {ingreso_matriz: MATRIZ_SIG[mx.dataset.valor] || 'si'}); return; }
        const ed = e.target.closest('[data-editar]');
        if(ed){ abrirCita(citas.find(c=>String(c.id)===ed.dataset.editar)); return; }
        const as = e.target.closest('.ag-asignar');
        if(as){ abrirCita(null, as.dataset.hora); return; }
        const bq = e.target.closest('[data-bloquear]');
        if(bq){ bloquearRapido(bq.dataset.bloquear, bq.dataset.fin); return; }
        const db = e.target.closest('[data-desbloquear]');
        if(db){ desbloquear(db.dataset.desbloquear); return; }
        const ue = e.target.closest('.ag-useredit');
        if(ue){ abrirUsuario(ue.dataset.user); }
    });
    async function actualizar(id, cambios){
        try { const j = await api(ROUTES.update+'/'+id, 'PUT', cambios); toast('success', j.message || 'Actualizado'); }
        catch(err){ Swal.fire({icon:'error', title:'No se pudo actualizar', text: err.message}); }
        cargar();
    }

    // Navegación y filtros
    function irA(f){ fecha = f; $('#ag-fecha').value = f; cargar(); }
    function sumarDias(n){ const d = new Date(fecha+'T00:00:00'); d.setDate(d.getDate()+n); return d.toISOString().slice(0,10); }
    $('#ag-prev').addEventListener('click', ()=> irA(sumarDias(-1)));
    $('#ag-next').addEventListener('click', ()=> irA(sumarDias(1)));
    $('#ag-hoy').addEventListener('click', ()=> irA(new Date(Date.now()-new Date().getTimezoneOffset()*60000).toISOString().slice(0,10)));
    $('#ag-fecha').addEventListener('change', e=> e.target.value && irA(e.target.value));
    ['#ag-q','#ag-f-motivo','#ag-f-estado'].forEach(s=> $(s).addEventListener(s==='#ag-q'?'input':'change', pintar));

    // Exportar a Excel (CSV con separador ; y BOM para que Excel respete las tildes)
    $('#ag-export').addEventListener('click', ()=>{
        const cols = ['Fecha','Hora','Nombre del funcionario','Tipo doc.','Identificación','Cargo','Teléfono','Correo electrónico','Motivo consulta','Énfasis','Asistencia','Estado','Ingreso matriz','Observaciones'];
        const q = v => '"'+String(v==null?'':v).replace(/"/g,'""')+'"';
        const filas = citas.map(c=>[fmtFecha(c.fecha), c.hora, c.nombre, c.tipo_identificacion, c.identificacion, c.cargo, c.telefono, c.email, c.motivo_label,
            (c.enfasis||[]).map(e=>ENFASIS[e]||e).join(', '), ASISTENCIAS[c.asistencia], ESTADOS[c.estado], MATRIZ[c.ingreso_matriz], c.observaciones].map(q).join(';'));
        const blob = new Blob(['﻿'+[cols.map(q).join(';'), ...filas].join('\r\n')], {type:'text/csv;charset=utf-8;'});
        const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'Agenda_Salud_Ocupacional_'+fecha+'.csv';
        document.body.appendChild(a); a.click(); a.remove();
    });

    // ─── Modal de cita ───
    let sugTimer = null, sugSeq = 0;
    function seleccionarUsuario(u){
        $('#cf-user_id').value = u ? u.id : '';
        $('#cf-telefono').value = u ? (u.telefono||'') : '';
        $('#cf-email').value = u ? (u.email||'') : '';
        $('#cf-cargo').value = u ? (u.cargo||'') : '';
        $('#cf-sel').style.display = u ? 'flex' : 'none';
        if(u){
            $('#cf-sel-av').textContent = iniciales(u.nombre_completo);
            $('#cf-sel-nombre').textContent = u.nombre_completo;
            $('#cf-sel-doc').textContent = (u.tipo_identificacion||'CC')+' '+u.identificacion;
            $('#cf-sel-edit').dataset.user = u.identificacion;
            $('#cf-buscar').value = '';
        }
        $('#cf-sug').style.display = 'none';
    }
    $('#cf-buscar').addEventListener('input', ()=>{
        clearTimeout(sugTimer);
        const v = $('#cf-buscar').value.trim();
        if(v.length < 3){ $('#cf-sug').style.display='none'; return; }
        sugTimer = setTimeout(async ()=>{
            const seq = ++sugSeq;
            try {
                const j = await api(ROUTES.pacientes+'?q='+encodeURIComponent(v));
                if(seq !== sugSeq) return;
                const items = j.items || [], box = $('#cf-sug');
                const digits = v.replace(/\D/g,'');
                const exacta = /^[\d.\s-]+$/.test(v) && items.find(i=>i.identificacion===digits);
                if(exacta){ seleccionarUsuario(exacta); return; }
                box.innerHTML = items.length
                    ? items.map((i,k)=>`<button type="button" data-k="${k}"><span class="n">${esc(i.nombre_completo)}</span><span class="m">${esc(i.identificacion)}${i.cargo?' · '+esc(i.cargo):''}</span></button>`).join('')
                    : '<div class="empty">Sin coincidencias entre los trabajadores de vinculación Planta.</div>';
                $$('button', box).forEach(b=> b.addEventListener('click', ()=> seleccionarUsuario(items[+b.dataset.k])));
                box.style.display = 'block';
            } catch(_) {}
        }, 300);
    });
    document.addEventListener('click', e=>{ if(!e.target.closest('.ag-sug-wrap')) $('#cf-sug').style.display='none'; });
    $('#cf-sel-edit').addEventListener('click', ()=> abrirUsuario($('#cf-sel-edit').dataset.user));

    let citaEditando = null;
    /** Llena el selector de hora con los turnos del día: los ocupados o bloqueados quedan deshabilitados. */
    async function cargarHoras(f, preferida){
        let lista = turnos;
        if(f !== fecha){
            try { lista = (await api(ROUTES.citas+'?fecha='+encodeURIComponent(f))).turnos || []; } catch(_) { lista = []; }
        }
        const propio = citaEditando ? citaEditando.id : null;
        const opts = lista.map(t=>{
            const otros = (t.ocupado_por||[]).filter(id=> id !== propio);
            const estado = t.bloqueo ? 'bloqueado' : (otros.length ? 'ocupado' : '');
            return {hora:t.hora, fin:t.fin, estado};
        });
        // La hora actual de la cita en edición se conserva aunque ya no coincida con los turnos configurados
        if(citaEditando && citaEditando.fecha === f && !opts.some(o=>o.hora===citaEditando.hora)){
            opts.push({hora:citaEditando.hora, fin:citaEditando.hora_fin, estado:''});
            opts.sort((a,b)=> aMin(a.hora)-aMin(b.hora));
        }
        const sel = $('#cf-hora');
        sel.innerHTML = opts.length
            ? opts.map(o=>`<option value="${o.hora}" ${o.estado?'disabled':''}>${o.hora} a ${o.fin}${o.estado?' · '+o.estado:''}</option>`).join('')
            : '<option value="" disabled>Sin turnos disponibles</option>';
        const libre = opts.find(o=>!o.estado && o.hora===preferida) || opts.find(o=>!o.estado);
        sel.value = libre ? libre.hora : '';
        $('#cf-hora-info').textContent = libre ? 'Tiempo de atención: '+CONFIG.duracion_minutos+' min' : 'No hay turnos libres este día';
    }
    $('#cf-fecha').addEventListener('change', ()=> cargarHoras($('#cf-fecha').value, $('#cf-hora').value));

    async function abrirCita(c, hora){
        citaEditando = c || null;
        $('#cf-id').value = c ? c.id : '';
        $('#citaModalTitle').innerHTML = c ? '<i class="fas fa-pen mr-2"></i>Editar cita ocupacional' : '<i class="far fa-calendar-plus mr-2"></i>Programar cita ocupacional';
        $('#cf-fecha').value = c ? c.fecha : fecha;
        await cargarHoras($('#cf-fecha').value, c ? c.hora : hora);
        seleccionarUsuario(c ? {id:c.user_id, nombre_completo:c.nombre, identificacion:c.identificacion, tipo_identificacion:c.tipo_identificacion, telefono:c.telefono, email:c.email, cargo:c.cargo} : null);
        $('#cf-buscar').value = '';
        $('#cf-motivo').value = c ? c.motivo : 'periodico';
        $$('input[name="cf-enfasis"]').forEach(i=> i.checked = !!(c && (c.enfasis||[]).includes(i.value)));
        $('#cf-asistencia').value = c ? c.asistencia : 'pendiente';
        $('#cf-estado').value = c ? c.estado : 'programada';
        $('#cf-matriz').value = c ? c.ingreso_matriz : 'no';
        $('#cf-observaciones').value = c ? (c.observaciones||'') : '';
        window.jQuery('#citaModal').modal('show');
    }
    $('#ag-nueva').addEventListener('click', ()=> abrirCita(null));

    $('#cf-guardar').addEventListener('click', async ()=>{
        if(!$('#cf-user_id').value){ Swal.fire({icon:'warning', title:'Falta el funcionario', text:'Busca y selecciona el trabajador de vinculación Planta a agendar.'}); return; }
        if(!$('#cf-fecha').value){ Swal.fire({icon:'warning', title:'Falta la fecha'}); return; }
        if(!$('#cf-hora').value){ Swal.fire({icon:'warning', title:'Sin horario disponible', text:'Elige otra fecha: los turnos de este día están ocupados o bloqueados.'}); return; }
        const id = $('#cf-id').value;
        const datos = {
            user_id: +$('#cf-user_id').value, fecha: $('#cf-fecha').value, hora: $('#cf-hora').value, motivo: $('#cf-motivo').value,
            enfasis: $$('input[name="cf-enfasis"]:checked').map(i=>i.value),
            asistencia: $('#cf-asistencia').value, estado: $('#cf-estado').value, ingreso_matriz: $('#cf-matriz').value,
            observaciones: $('#cf-observaciones').value,
        };
        const btn = $('#cf-guardar'); btn.disabled = true;
        try {
            const j = await api(id ? ROUTES.update+'/'+id : ROUTES.store, id ? 'PUT' : 'POST', datos);
            window.jQuery('#citaModal').modal('hide');
            Swal.fire({icon:'success', title:'Listo', text:j.message, timer:2200, showConfirmButton:false});
            if(datos.fecha !== fecha) irA(datos.fecha); else cargar();
        } catch(err){
            Swal.fire({icon:'error', title:'No se pudo guardar', text: err.message});
        } finally { btn.disabled = false; }
    });

    // ─── Modal del funcionario (editar / corregir datos del usuario) ───
    // Se trae el registro completo para no borrar los campos que no se muestran aquí.
    let usuarioCompleto = null;
    const UF = ['name','apellido1','apellido2','tipo_identificacion','identificacion','contacto','email','cargo','servicio','eps','afp','arl','direccionr'];
    async function abrirUsuario(identificacion){
        if(!identificacion) return;
        try {
            const j = await api(ROUTES.paciente+'/'+encodeURIComponent(identificacion));
            if(!j.found || !j.paciente){ Swal.fire({icon:'warning', title:'Funcionario no encontrado'}); return; }
            usuarioCompleto = j.paciente;
            $('#uf-id').value = j.paciente.id;
            UF.forEach(k=>{ const el = $('#uf-'+k); if(el) el.value = j.paciente[k] != null ? j.paciente[k] : ''; });
            if(!$('#uf-tipo_identificacion').value) $('#uf-tipo_identificacion').value = 'CC';
            window.jQuery('#userModal').modal('show');
        } catch(err){ Swal.fire({icon:'error', title:'Error', text: err.message}); }
    }
    $('#uf-guardar').addEventListener('click', async ()=>{
        if(!$('#uf-name').value.trim() || !$('#uf-identificacion').value.trim()){
            Swal.fire({icon:'warning', title:'Campos requeridos', text:'Nombres e identificación son obligatorios.'}); return;
        }
        const payload = Object.assign({}, usuarioCompleto);
        UF.forEach(k=> payload[k] = $('#uf-'+k).value);
        ['id','nombre_completo','vinculacion'].forEach(k=> delete payload[k]);
        const btn = $('#uf-guardar'); btn.disabled = true;
        try {
            const j = await api(ROUTES.paciente+'/'+$('#uf-id').value, 'PUT', payload);
            window.jQuery('#userModal').modal('hide');
            toast('success', j.message || 'Datos actualizados');
            // Si el funcionario está seleccionado en el modal de cita, se refrescan sus datos
            if(String($('#cf-user_id').value) === String(j.paciente.id)){
                seleccionarUsuario({id:j.paciente.id, nombre_completo:j.paciente.nombre_completo, identificacion:j.paciente.identificacion,
                    tipo_identificacion:j.paciente.tipo_identificacion, telefono:j.paciente.contacto, email:j.paciente.email, cargo:j.paciente.cargo});
            }
            cargar();
        } catch(err){ Swal.fire({icon:'error', title:'No se pudo guardar', text: err.message}); }
        finally { btn.disabled = false; }
    });

    // ─── Configuración: tiempo de atención por cita y jornada ───
    function pintarConfig(){
        $$('.js-duracion').forEach(el=> el.textContent = CONFIG.duracion_minutos);
        $('#ag-jornada').textContent = CONFIG.manana_inicio+'–'+CONFIG.manana_fin + (CONFIG.tarde_inicio ? ' y '+CONFIG.tarde_inicio+'–'+CONFIG.tarde_fin : '');
    }
    function previewConfig(){
        const dur = +$('#cg-duracion').value || 0;
        let n = 0;
        const rangos = [[$('#cg-manana_inicio').value, $('#cg-manana_fin').value]];
        if($('#cg-con-tarde').checked) rangos.push([$('#cg-tarde_inicio').value, $('#cg-tarde_fin').value]);
        rangos.forEach(([a,b])=>{ if(a && b && dur >= 5) n += Math.max(0, Math.floor((aMin(b)-aMin(a))/dur)); });
        $('#cg-preview').innerHTML = dur >= 5 ? `<i class="far fa-calendar-alt"></i> Se generan <strong>${n}</strong> turnos de ${dur} minutos por día.` : '';
    }
    $('#ag-config').addEventListener('click', ()=>{
        $('#cg-duracion').value = CONFIG.duracion_minutos;
        $('#cg-manana_inicio').value = CONFIG.manana_inicio;
        $('#cg-manana_fin').value = CONFIG.manana_fin;
        $('#cg-con-tarde').checked = !!CONFIG.tarde_inicio;
        $('#cg-tarde_inicio').value = CONFIG.tarde_inicio || '14:00';
        $('#cg-tarde_fin').value = CONFIG.tarde_fin || '16:00';
        $('#cg-tarde').style.display = $('#cg-con-tarde').checked ? '' : 'none';
        previewConfig();
        window.jQuery('#configModal').modal('show');
    });
    $('#cg-con-tarde').addEventListener('change', ()=>{ $('#cg-tarde').style.display = $('#cg-con-tarde').checked ? '' : 'none'; previewConfig(); });
    ['#cg-duracion','#cg-manana_inicio','#cg-manana_fin','#cg-tarde_inicio','#cg-tarde_fin'].forEach(s=> $(s).addEventListener('input', previewConfig));
    $('#cg-guardar').addEventListener('click', async ()=>{
        const conTarde = $('#cg-con-tarde').checked;
        const btn = $('#cg-guardar'); btn.disabled = true;
        try {
            const j = await api(ROUTES.config, 'PUT', {
                duracion_minutos: +$('#cg-duracion').value,
                manana_inicio: $('#cg-manana_inicio').value, manana_fin: $('#cg-manana_fin').value,
                tarde_inicio: conTarde ? $('#cg-tarde_inicio').value : null, tarde_fin: conTarde ? $('#cg-tarde_fin').value : null,
            });
            CONFIG = j.config; pintarConfig();
            window.jQuery('#configModal').modal('hide');
            toast('success', j.message);
            cargar();
        } catch(err){ Swal.fire({icon:'error', title:'No se pudo guardar', text: err.message}); }
        finally { btn.disabled = false; }
    });

    // ─── Bloqueos de horario ───
    async function pintarBloqueosModal(){
        const f = $('#bf-fecha').value, box = $('#bf-lista');
        if(!f){ box.textContent = '—'; return; }
        try {
            const lista = f === fecha ? bloqueos : ((await api(ROUTES.citas+'?fecha='+encodeURIComponent(f))).bloqueos || []);
            box.innerHTML = lista.length ? lista.map(b=>`<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid var(--so-line);">
                    <span><strong style="color:var(--so-bad);">${b.dia_completo ? 'Todo el día' : esc(b.hora_inicio)+' a '+esc(b.hora_fin)}</strong> · ${esc(b.motivo||'Sin motivo')}</span>
                    <button type="button" class="ag-act" data-desbloquear-modal="${b.id}" style="color:var(--so-bad);"><i class="fas fa-lock-open"></i> Desbloquear</button></div>`).join('')
                : 'No hay horarios bloqueados en esta fecha.';
            $$('[data-desbloquear-modal]', box).forEach(b=> b.addEventListener('click', async ()=>{ if(await desbloquear(b.dataset.desbloquearModal)) pintarBloqueosModal(); }));
        } catch(_) { box.textContent = '—'; }
    }
    function abrirBloqueo(inicio, fin){
        $('#bf-fecha').value = fecha;
        $('#bf-dia').checked = false; $('#bf-horas').style.display = '';
        $('#bf-inicio').value = inicio || CONFIG.manana_inicio;
        $('#bf-fin').value = fin || CONFIG.manana_fin;
        $('#bf-motivo').value = '';
        pintarBloqueosModal();
        window.jQuery('#bloqueoModal').modal('show');
    }
    $('#ag-bloquear').addEventListener('click', ()=> abrirBloqueo());
    $('#bf-dia').addEventListener('change', ()=> $('#bf-horas').style.display = $('#bf-dia').checked ? 'none' : '');
    $('#bf-fecha').addEventListener('change', pintarBloqueosModal);
    $('#bf-guardar').addEventListener('click', async ()=>{
        const dia = $('#bf-dia').checked;
        const btn = $('#bf-guardar'); btn.disabled = true;
        try {
            const j = await api(ROUTES.bloqueos, 'POST', {
                fecha: $('#bf-fecha').value, dia_completo: dia,
                hora_inicio: dia ? null : $('#bf-inicio').value, hora_fin: dia ? null : $('#bf-fin').value,
                motivo: $('#bf-motivo').value,
            });
            window.jQuery('#bloqueoModal').modal('hide');
            toast('success', j.message);
            if($('#bf-fecha').value !== fecha) irA($('#bf-fecha').value); else cargar();
        } catch(err){ Swal.fire({icon:'error', title:'No se pudo bloquear', text: err.message}); }
        finally { btn.disabled = false; }
    });
    async function bloquearRapido(inicio, fin){
        const r = await Swal.fire({icon:'question', title:'Bloquear '+inicio+' a '+fin,
            input:'text', inputPlaceholder:'Motivo (opcional)', showCancelButton:true,
            confirmButtonText:'Bloquear', cancelButtonText:'Cancelar', confirmButtonColor:'#c4453b'});
        if(!r.isConfirmed) return;
        try {
            const j = await api(ROUTES.bloqueos, 'POST', {fecha, hora_inicio:inicio, hora_fin:fin, motivo:r.value||''});
            toast('success', j.message);
        } catch(err){ Swal.fire({icon:'error', title:'No se pudo bloquear', text: err.message}); }
        cargar();
    }
    async function desbloquear(id){
        const r = await Swal.fire({icon:'question', title:'¿Desbloquear este horario?', text:'Volverá a estar disponible para agendar citas.',
            showCancelButton:true, confirmButtonText:'Desbloquear', cancelButtonText:'Cancelar', confirmButtonColor:'#2e3a75'});
        if(!r.isConfirmed) return false;
        try { const j = await api(ROUTES.bloqueos+'/'+id, 'DELETE'); toast('success', j.message); cargar(); return true; }
        catch(err){ Swal.fire({icon:'error', title:'No se pudo desbloquear', text: err.message}); return false; }
    }
    pintarConfig();

    // Al cerrar el modal del funcionario sobre el de la cita, se conserva el scroll del modal de la cita
    window.jQuery('#userModal').on('hidden.bs.modal', ()=>{ if(window.jQuery('#citaModal').hasClass('show')) document.body.classList.add('modal-open'); });

    cargar();
})();
</script>
@endpush
