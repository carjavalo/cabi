@extends('layouts.app')

@section('title', 'Concepto Médico Ocupacional')
@section('header', 'Salud Ocupacional · Concepto Médico')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    :root{
        --so-brand:#2e3a75; --so-brand-d:#232c5c; --so-brand-l:#4657a8;
        --so-bg:#eef0fb; --so-soft:#f4f6fb; --so-line:#e1e5f2; --so-line2:#d5daea;
        --so-text:#1f2440; --so-mut:#7c82a0; --so-ok:#2e9e6b; --so-warn:#c98a1e; --so-bad:#c4453b;
    }
    .so-wrap{ font-family:inherit; color:var(--so-text); }
    .so-mono{ font-family:'Courier New',monospace; letter-spacing:.04em; }
    /* Header banner */
    .so-hero{
        background:linear-gradient(120deg,var(--so-brand),var(--so-brand-l));
        color:#fff; border-radius:16px; padding:18px 22px; margin-bottom:18px;
        display:flex; align-items:center; gap:16px; flex-wrap:wrap;
        box-shadow:0 14px 30px -18px rgba(46,58,117,.75);
    }
    .so-hero .badge-huv{
        width:52px;height:52px;border-radius:13px;background:rgba(255,255,255,.16);
        display:flex;align-items:center;justify-content:center;font-weight:700;font-size:17px;flex-shrink:0;
    }
    .so-hero small{ text-transform:uppercase; letter-spacing:.14em; opacity:.85; font-size:11px; }
    .so-hero h4{ margin:2px 0 0; font-weight:800; letter-spacing:-.01em; }
    .so-chip-date{ margin-left:auto; background:rgba(255,255,255,.14); border-radius:11px; padding:8px 14px; font-weight:600; display:flex; align-items:center; gap:9px; }
    .so-live-dot{ width:9px;height:9px;border-radius:50%;background:#7ee2b0;box-shadow:0 0 0 3px rgba(126,226,176,.3); }

    /* Cards */
    .so-card{ background:#fff; border:1px solid var(--so-line); border-radius:16px;
        box-shadow:0 1px 2px rgba(46,58,117,.05),0 16px 34px -26px rgba(46,58,117,.4); }
    .so-side{ position:sticky; top:14px; }

    /* Progress steps */
    .so-prog-head{ display:flex; justify-content:space-between; align-items:baseline; padding:18px 18px 6px; }
    .so-prog-head .lbl{ font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--so-mut);font-weight:700; }
    .so-prog-head .pct{ font-size:16px;font-weight:800;color:var(--so-brand); }
    .so-bar{ height:8px;border-radius:99px;background:var(--so-bg);margin:0 18px 10px;overflow:hidden; }
    .so-bar > span{ display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,var(--so-brand),var(--so-brand-l));transition:width .35s ease; }
    .so-steps{ list-style:none;margin:0;padding:6px 10px 12px; }
    .so-steps li{ margin:1px 0; }
    .so-steps button{ display:flex;align-items:center;gap:11px;width:100%;text-align:left;border:none;background:transparent;
        padding:9px 10px;border-radius:11px;cursor:pointer;transition:background .15s; }
    .so-steps button:hover{ background:var(--so-soft); }
    .so-steps button.active{ background:var(--so-bg); }
    .so-steps .num{ flex-shrink:0;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;
        font-size:12px;font-weight:700;background:#e7eaf3;color:#9297ae; }
    .so-steps button.active .num{ background:var(--so-brand);color:#fff; }
    .so-steps button.done .num{ background:var(--so-ok);color:#fff; }
    .so-steps .txt{ font-size:13.5px;font-weight:600;color:#8a90a8;line-height:1.2; }
    .so-steps button.active .txt{ color:var(--so-brand);font-weight:700; }
    .so-steps button.done .txt{ color:#4a4f66; }

    /* Panels */
    .so-panel{ padding:clamp(18px,2.4vw,30px); }
    .so-eyebrow{ font-family:'Courier New',monospace;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--so-brand);font-weight:700; }
    .so-panel h2{ font-size:22px;font-weight:800;letter-spacing:-.02em;margin:5px 0 3px; }
    .so-panel .sub{ color:var(--so-mut);font-size:14.5px;margin:0; }
    .so-sec{ font-family:'Courier New',monospace;font-size:11px;letter-spacing:.11em;text-transform:uppercase;color:var(--so-brand);
        font-weight:700;background:var(--so-bg);padding:9px 13px;border-radius:8px;margin:22px 0 13px; }
    .so-sec.dark{ background:var(--so-brand);color:#fff;text-align:center;letter-spacing:.14em; }

    .so-lbl{ display:block;font-size:11.5px;font-weight:700;letter-spacing:.03em;text-transform:uppercase;color:var(--so-brand);
        margin-bottom:6px;font-family:'Courier New',monospace; }
    .so-lbl.mut{ color:var(--so-mut); }
    .so-in{ width:100%;padding:10px 12px;border:1px solid var(--so-line2);border-radius:10px;font-size:14.5px;background:#fff;outline:none;transition:border .15s,box-shadow .15s; }
    .so-in:focus{ border-color:var(--so-brand);box-shadow:0 0 0 3px rgba(46,58,117,.13); }
    .so-in.ro,.so-in[readonly]{ background:var(--so-soft);color:#4a4f66; }
    textarea.so-in{ resize:vertical;line-height:1.5; }
    .so-grid{ display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px 18px; }
    .so-grid.tight{ grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px; }
    .so-row2{ display:grid;grid-template-columns:170px 1fr;gap:12px;align-items:center;margin-bottom:9px; }
    .so-row2 label{ font-size:13px;font-weight:600;color:var(--so-brand);margin:0; }
    @media(max-width:520px){ .so-row2{ grid-template-columns:1fr; align-items:flex-start; } }

    /* Selectable option cards (tipo / concepto) */
    .so-opts{ display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px; }
    .so-opt{ position:relative; }
    .so-opt input{ position:absolute;opacity:0;pointer-events:none; }
    .so-opt label{ display:flex;align-items:center;gap:10px;padding:13px 15px;border:1.5px solid var(--so-line);border-radius:12px;
        cursor:pointer;font-size:15px;font-weight:600;color:#4a4f66;margin:0;transition:all .15s;background:#fff; }
    .so-opt .tick{ width:20px;height:20px;border-radius:6px;border:1.5px solid var(--so-line2);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;color:transparent; }
    .so-opt input:checked + label{ background:var(--so-bg);border-color:var(--so-brand);color:var(--so-brand); }
    .so-opt input:checked + label .tick{ background:var(--so-brand);border-color:var(--so-brand);color:#fff; }
    .so-opt input:focus-visible + label{ box-shadow:0 0 0 3px rgba(46,58,117,.18); }

    /* Concept big cards */
    .so-cc label{ display:flex;gap:14px;align-items:flex-start;padding:16px 18px;border:2px solid var(--so-line);border-radius:14px;cursor:pointer;margin:0 0 12px;transition:all .15s;background:#fff; }
    .so-cc .dot{ flex-shrink:0;margin-top:2px;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;background:#edeff6;color:transparent; }
    .so-cc .t{ font-size:16px;font-weight:700;color:#2b2f44;margin-bottom:2px;display:block; }
    .so-cc .d{ font-size:13.5px;color:var(--so-mut);line-height:1.4; }
    .so-cc.ok input:checked + label{ border-color:var(--so-ok);background:#eaf7f0; }
    .so-cc.ok input:checked + label .dot{ background:var(--so-ok);color:#fff; }
    .so-cc.ok input:checked + label .t{ color:var(--so-ok); }
    .so-cc.warn input:checked + label{ border-color:var(--so-warn);background:#fbf3e4; }
    .so-cc.warn input:checked + label .dot{ background:var(--so-warn);color:#fff; }
    .so-cc.warn input:checked + label .t{ color:var(--so-warn); }
    .so-cc.bad input:checked + label{ border-color:var(--so-bad);background:#fbebe9; }
    .so-cc.bad input:checked + label .dot{ background:var(--so-bad);color:#fff; }
    .so-cc.bad input:checked + label .t{ color:var(--so-bad); }
    .so-cc input{ position:absolute;opacity:0;pointer-events:none; }

    /* Risk factor chips */
    .so-risk{ display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:9px; }
    .so-risk .chip input{ position:absolute;opacity:0;pointer-events:none; }
    .so-risk .chip label{ display:flex;align-items:center;gap:8px;padding:9px 11px;border:1.5px solid var(--so-line);border-radius:9px;
        cursor:pointer;font-size:13px;font-weight:600;color:#5a5f76;margin:0;transition:all .13s;background:#fff; }
    .so-risk .chip .b{ width:16px;height:16px;border-radius:5px;border:1.5px solid var(--so-line2);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:10px;color:transparent; }
    .so-risk .chip input:checked + label{ background:var(--so-bg);border-color:var(--so-brand);color:var(--so-brand); }
    .so-risk .chip input:checked + label .b{ background:var(--so-brand);border-color:var(--so-brand);color:#fff; }

    /* Segmented Sí/No */
    .so-seg{ display:flex;gap:8px; }
    .so-seg .s input{ position:absolute;opacity:0;pointer-events:none; }
    .so-seg .s label{ padding:9px 20px;border:1.5px solid var(--so-line2);border-radius:9px;cursor:pointer;font-size:14px;font-weight:700;color:var(--so-mut);margin:0; }
    .so-seg .s input:checked + label{ background:var(--so-brand);border-color:var(--so-brand);color:#fff; }

    /* Identificación box + symbol */
    .so-ident{ display:flex;gap:8px;align-items:stretch; }
    .so-ident .so-in{ flex:1; }
    .so-ident-btn{ flex-shrink:0;width:46px;border:1.5px solid var(--so-brand);background:var(--so-bg);color:var(--so-brand);border-radius:10px;
        cursor:pointer;font-size:17px;display:flex;align-items:center;justify-content:center;transition:all .15s; }
    .so-ident-btn:hover{ background:var(--so-brand);color:#fff; }
    .so-pill{ display:inline-flex;align-items:center;gap:7px;font-size:12.5px;font-weight:700;padding:5px 12px;border-radius:99px;margin-top:9px; }
    .so-pill.found{ background:#eaf7f0;color:var(--so-ok); }
    .so-pill.new{ background:#fbf3e4;color:var(--so-warn); }
    .so-pill.bad{ background:#fbebe9;color:var(--so-bad); }
    .so-pill.idle{ background:var(--so-soft);color:var(--so-mut); }
    .so-note{ display:flex;align-items:flex-start;gap:8px;font-size:12.5px;color:var(--so-mut);margin-top:7px;line-height:1.4; }

    /* Attach + docs */
    .so-attach{ display:inline-flex;align-items:center;gap:9px;padding:11px 19px;border:1.5px dashed var(--so-brand);background:var(--so-bg);
        color:var(--so-brand);font-size:14px;font-weight:700;border-radius:11px;cursor:pointer; }
    .so-attach:hover{ background:#e3e7fa; }
    .so-doc{ display:flex;align-items:center;gap:12px;padding:10px 13px;background:var(--so-soft);border:1px solid var(--so-line);border-radius:10px;margin-top:8px; }
    .so-doc .ic{ width:32px;height:32px;flex-shrink:0;border-radius:8px;background:var(--so-brand);color:#fff;display:flex;align-items:center;justify-content:center; }
    .so-doc .nm{ font-size:13.5px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .so-doc .mt{ font-size:11.5px;color:var(--so-mut); }
    .so-doc .rm{ border:none;background:transparent;color:var(--so-bad);font-size:12.5px;font-weight:700;cursor:pointer; }

    /* Signature */
    #so-sign{ width:100%;height:150px;border:1.5px dashed #c1c7dd;border-radius:12px;background:#fbfbfe;touch-action:none;cursor:crosshair;display:block; }

    /* Footer nav */
    .so-nav{ display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:18px;flex-wrap:wrap; }
    .so-btn{ display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;border:none; }
    .so-btn.prim{ background:var(--so-brand);color:#fff;box-shadow:0 10px 22px -12px rgba(46,58,117,.8); }
    .so-btn.prim:hover{ background:var(--so-brand-d);color:#fff; }
    .so-btn.ghost{ background:#fff;color:#4a4f66;border:1.5px solid var(--so-line2); }
    .so-btn.ok{ background:var(--so-ok);color:#fff; }

    /* Historial */
    .so-hist a{ display:block;padding:9px 11px;border-radius:9px;text-decoration:none;border:1px solid var(--so-line);margin-bottom:7px;background:#fff;transition:all .13s; }
    .so-hist a:hover{ border-color:var(--so-brand);background:var(--so-bg); }
    .so-hist .d{ font-size:12px;color:var(--so-mut); }
    .so-hist .c{ font-size:13px;font-weight:700;color:var(--so-brand); }

    /* Tarjeta de paciente seleccionado */
    .so-pcard .top{ display:flex;gap:12px;align-items:center; }
    .so-pcard .av{ width:48px;height:48px;border-radius:13px;background:linear-gradient(135deg,var(--so-brand),var(--so-brand-l));color:#fff;
        display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;flex-shrink:0;box-shadow:0 8px 18px -10px rgba(46,58,117,.7); }
    .so-pcard .nm{ font-weight:800;font-size:15px;line-height:1.15;color:var(--so-text);word-break:break-word; }
    .so-pcard .id{ font-size:12px;color:var(--so-mut); }
    .so-pchips{ display:flex;flex-wrap:wrap;gap:6px;margin-top:11px; }
    .so-pchip{ font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;background:var(--so-bg);color:var(--so-brand); }
    .so-pchip.vinc{ background:#eaf7f0;color:var(--so-ok); }
    .so-prow{ display:flex;justify-content:space-between;gap:10px;font-size:12.5px;padding:6px 0;border-top:1px dashed var(--so-line); }
    .so-prow:first-of-type{ border-top:none; }
    .so-prow .k{ color:var(--so-mut);white-space:nowrap; }
    .so-prow .v{ font-weight:600;text-align:right;color:var(--so-text);word-break:break-word; }

    /* Review certificate */
    .so-cert{ border:1px solid var(--so-line);border-radius:14px;overflow:hidden; }
    .so-cert-h{ display:flex;align-items:center;gap:14px;background:var(--so-brand);color:#fff;padding:16px 20px; }
    .so-cert-h .lg{ width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.16);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px; }
    .so-kv{ display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px 22px;margin-bottom:18px; }
    .so-kv .k{ font-size:10.5px;color:#9297ae;text-transform:uppercase;letter-spacing:.05em; }
    .so-kv .v{ font-size:14.5px;font-weight:600;color:var(--so-text); }
    .so-cert-badge{ background:var(--so-bg);border:1px solid #d6dbf0;border-radius:12px;padding:15px 18px;margin-bottom:16px; }

    @media (max-width:991px){ .so-side{ position:static; } }
    @media print{
        body{ background:#fff !important; }
        .main-header,.main-sidebar,.main-footer,.so-hero,.so-side,.so-nav,.content-header{ display:none !important; }
        .content-wrapper{ margin:0 !important; background:#fff !important; }
    }
</style>
@endpush

@section('content')
<div class="so-wrap">

    <div class="so-hero">
        <div class="badge-huv">HUV</div>
        <div>
            <small>Hospital Universitario del Valle · Evaristo García E.S.E</small>
            <h4>Concepto Médico Ocupacional</h4>
        </div>
        <div class="so-chip-date">
            <span class="so-live-dot"></span>
            <span id="so-fecha-hero">{{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}</span>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <strong>Revisa los siguientes campos:</strong>
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    @if(!empty($migracionesPendientes))
    <div class="alert alert-warning" style="border-radius:12px;">
        <strong><i class="fas fa-database"></i> Configuración pendiente en el servidor:</strong>
        faltan las migraciones de este módulo. Ejecuta <code>php&nbsp;artisan&nbsp;migrate&nbsp;--force</code>
        en el hosting para habilitar el registro de conceptos y los datos del paciente.
    </div>
    @endif

    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-lg-3 mb-3">
            <div class="so-side">
                <div class="so-card">
                    <div class="so-prog-head">
                        <span class="lbl">Progreso</span>
                        <span class="pct" id="so-pct">0%</span>
                    </div>
                    <div class="so-bar"><span id="so-bar" style="width:0%"></span></div>
                    <ul class="so-steps" id="so-steps">
                        @php $labels = ['Datos de la atención','Identificación del paciente','Datos laborales','Historia clínica','Concepto ocupacional','Recomendaciones y firma','Revisión y certificado']; @endphp
                        @foreach($labels as $i => $lbl)
                        <li><button type="button" data-step="{{ $i }}" class="{{ $i===0?'active':'' }}">
                            <span class="num">{{ $i+1 }}</span><span class="txt">{{ $lbl }}</span>
                        </button></li>
                        @endforeach
                    </ul>
                </div>

                <div class="so-card mt-3" id="so-patient-card" style="display:none;">
                    <div style="height:5px;background:linear-gradient(90deg,var(--so-brand),var(--so-brand-l));"></div>
                    <div class="p-3">
                        <div class="lbl so-mono" style="font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--so-mut);font-weight:700;margin-bottom:11px;">
                            <i class="fas fa-user-check" style="color:var(--so-ok);"></i> Paciente seleccionado
                        </div>
                        <div class="so-pcard">
                            <div class="top">
                                <div class="av" id="pc-av">PA</div>
                                <div style="min-width:0;">
                                    <div class="nm" id="pc-nombre">—</div>
                                    <div class="id so-mono" id="pc-ident">—</div>
                                </div>
                            </div>
                            <div class="so-pchips" id="pc-chips"></div>
                            <div style="margin-top:12px;">
                                <div class="so-prow"><span class="k">Vinculación</span><span class="v" id="pc-vinc">—</span></div>
                                <div class="so-prow"><span class="k">EPS</span><span class="v" id="pc-eps">—</span></div>
                                <div class="so-prow"><span class="k">Teléfono</span><span class="v" id="pc-tel">—</span></div>
                                <div class="so-prow"><span class="k">Cargo</span><span class="v" id="pc-cargo">—</span></div>
                                <div class="so-prow"><span class="k">Servicio</span><span class="v" id="pc-serv">—</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="so-card mt-3" id="so-hist-card" style="display:none;">
                    <div class="p-3">
                        <div class="lbl so-mono" style="font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--so-mut);font-weight:700;margin-bottom:9px;">
                            Historial del paciente <span id="so-hist-count" class="badge badge-secondary"></span>
                        </div>
                        <div class="so-hist" id="so-hist-list"></div>
                    </div>
                </div>

                @if($recientes->count())
                <div class="so-card mt-3">
                    <div class="p-3">
                        <div class="lbl so-mono" style="font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--so-mut);font-weight:700;margin-bottom:9px;">Conceptos recientes</div>
                        <div class="so-hist">
                            @foreach($recientes as $r)
                            <a href="{{ route('salud.concepto.show', $r->id) }}">
                                <span class="c">{{ $r->paciente_nombre ?: ($r->user->name ?? 'Paciente') }}</span>
                                <span class="d">{{ optional($r->fecha_atencion)->format('d/m/Y') }} · {{ $r->concepto_label }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- MAIN FORM -->
        <div class="col-lg-9">
            <form id="so-form" method="POST" action="{{ route('salud.concepto.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" id="f-user_id">
                <input type="hidden" name="paciente_nombre" id="f-paciente_nombre">
                <input type="hidden" name="firma" id="f-firma">

                <div class="so-card">

                    {{-- PASO 1 · ATENCIÓN --}}
                    <section class="so-panel so-step" data-step="0">
                        <div class="so-eyebrow">Paso 1 de 7</div>
                        <h2>Datos de la atención</h2>
                        <p class="sub">La fecha y la hora se registran automáticamente. Selecciona el tipo de atención.</p>

                        <div class="so-grid" style="margin-top:20px;">
                            <div>
                                <label class="so-lbl">Fecha de atención (automática)</label>
                                <input type="text" class="so-in ro" value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}" readonly>
                            </div>
                            <div>
                                <label class="so-lbl">Hora de atención (automática)</label>
                                <input type="text" class="so-in ro" value="{{ \Carbon\Carbon::now()->format('h:i A') }}" readonly>
                            </div>
                            <div>
                                <label class="so-lbl">Lugar de atención</label>
                                <input type="text" name="lugar_atencion" class="so-in" value="HUV">
                            </div>
                        </div>

                        <label class="so-lbl" style="margin-top:20px;">Tipo de atención</label>
                        <div class="so-opts">
                            @foreach($tipos as $k => $t)
                            <div class="so-opt">
                                <input type="radio" name="tipo_atencion" id="tipo-{{ $k }}" value="{{ $k }}" {{ $loop->first?'checked':'' }}>
                                <label for="tipo-{{ $k }}"><span class="tick">✓</span>{{ $t }}</label>
                            </div>
                            @endforeach
                        </div>
                    </section>

                    {{-- PASO 2 · IDENTIFICACIÓN --}}
                    <section class="so-panel so-step" data-step="1" style="display:none;">
                        <div class="so-eyebrow">Paso 2 de 7</div>
                        <h2>Identificación del paciente</h2>
                        <p class="sub">Digita la identificación para traer los datos del paciente. Usa el botón para crear o editar.</p>

                        <div class="so-grid" style="margin-top:20px;">
                            <div style="grid-column:1/-1;">
                                <label class="so-lbl">Tipo y N.º de identificación
                                    <button type="button" id="so-ident-action" class="so-ident-btn" style="width:34px;height:26px;display:inline-flex;font-size:14px;vertical-align:middle;margin-left:6px;" title="Crear o editar paciente">
                                        <i class="fas fa-user-plus" id="so-ident-icon"></i>
                                    </button>
                                </label>
                                <div class="so-ident">
                                    <input type="text" id="f-identificacion" name="identificacion" class="so-in" placeholder="C.C. 00.000.000" autocomplete="off">
                                    <button type="button" class="so-ident-btn" id="so-ident-search" title="Buscar paciente"><i class="fas fa-search"></i></button>
                                </div>
                                <div id="so-ident-pill" class="so-pill idle"><i class="fas fa-circle" style="font-size:7px;"></i> Escribe una identificación para buscar</div>
                                <div class="so-note"><i class="fas fa-info-circle" style="margin-top:2px;color:var(--so-brand);"></i> Solo se atienden trabajadores de vinculación <strong style="color:var(--so-brand)">Planta</strong>.</div>
                            </div>

                            <div><label class="so-lbl mut">Nombre del paciente</label><input type="text" id="p-nombre" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Vinculación</label><input type="text" id="p-vinculacion" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Edad</label><input type="text" id="p-edad" name="edad" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Género</label><input type="text" id="p-genero-txt" class="so-in ro" readonly><input type="hidden" id="p-genero" name="genero"></div>
                            <div><label class="so-lbl mut">Grupo sanguíneo</label><input type="text" id="p-grupo" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Fecha de nacimiento</label><input type="text" id="p-fnac" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Lugar de nacimiento</label><input type="text" id="p-lugarnac" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Teléfono</label><input type="text" id="p-contacto" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Correo electrónico</label><input type="text" id="p-correo" class="so-in ro" readonly></div>
                            <div style="grid-column:1/-1;"><label class="so-lbl mut">Dirección de residencia</label><input type="text" id="p-direccion" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Estrato</label><input type="text" id="p-estrato" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Tipo de vivienda</label><input type="text" id="p-vivienda" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Estado civil</label><input type="text" id="p-escivil" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">N.º de hijos</label><input type="text" id="p-hijos" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Escolaridad</label><input type="text" id="p-escolaridad" class="so-in ro" readonly></div>
                            <div><label class="so-lbl mut">Profesión</label><input type="text" id="p-profesion" class="so-in ro" readonly></div>
                        </div>
                    </section>

                    {{-- PASO 3 · LABORAL --}}
                    <section class="so-panel so-step" data-step="2" style="display:none;">
                        <div class="so-eyebrow">Paso 3 de 7</div>
                        <h2>Datos laborales y de afiliación</h2>
                        <p class="sub">Cargo, empleador y seguridad social vigentes en esta atención.</p>

                        <div class="so-grid" style="margin-top:20px;">
                            <div><label class="so-lbl">Cargo</label><input type="text" name="cargo_inicio" id="f-cargo" list="dl-cargo" class="so-in" placeholder="Escribe o selecciona…"></div>
                            <div><label class="so-lbl">Servicio</label><input type="text" name="servicio" id="f-servicio" list="dl-serv" class="so-in" placeholder="Área o servicio"></div>
                            <div style="grid-column:1/-1;"><label class="so-lbl">Empleador</label><input type="text" name="empleador" class="so-in ro" value="{{ $empleador }}"></div>
                            <div><label class="so-lbl">NIT</label><input type="text" name="nit" class="so-in ro so-mono" value="{{ $nit }}"></div>
                            <div><label class="so-lbl">EPS</label><input type="text" name="eps" id="f-eps" list="dl-eps" class="so-in" placeholder="Escribe o selecciona…"></div>
                            <div><label class="so-lbl">AFP</label><input type="text" name="afp" id="f-afp" list="dl-afp" class="so-in" placeholder="Fondo de pensiones"></div>
                            <div><label class="so-lbl">ARL</label><input type="text" name="arl" id="f-arl" list="dl-arl" class="so-in" placeholder="Escribe o selecciona…"></div>
                        </div>

                        <div class="so-sec" style="margin-top:26px;">Documentos de la EPS</div>
                        <p class="sub" style="margin-bottom:12px;">Adjunta exámenes, órdenes o incapacidades. Solo <strong style="color:var(--so-brand)">PDF o imágenes</strong>, hasta <strong style="color:var(--so-brand)">2 MB</strong> por archivo.</p>
                        <input type="file" id="f-docs" name="documentos[]" multiple accept="image/*,application/pdf" style="display:none;">
                        <button type="button" class="so-attach" id="so-attach-btn"><i class="fas fa-paperclip"></i> Adjuntar documentos</button>
                        <div id="so-doc-list" style="margin-top:12px;"></div>
                    </section>

                    {{-- PASO 4 · HISTORIA --}}
                    <section class="so-panel so-step" data-step="3" style="display:none;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap;">
                            <div>
                                <div class="so-eyebrow">Paso 4 de 7</div>
                                <h2>Historia clínica ocupacional</h2>
                                <p class="sub">Diligenciada por el médico: anamnesis, examen físico y evolución.</p>
                            </div>
                            <button type="button" id="so-view-docs" class="so-btn prim" style="display:none;padding:10px 16px;font-size:13.5px;"><i class="fas fa-eye"></i> Ver documentos EPS <span id="so-view-docs-n" class="badge badge-light"></span></button>
                        </div>

                        <div class="so-sec">Factores de riesgo ocupacional en el cargo actual</div>
                        <div class="so-risk">
                            @php $riesgos = ['Ergonómicos','Ruido','Vibración','Iluminación','Temperatura','Químico','Polvo','Humos','Gases','Vapores','Biológico','Virus','Hongos','Parásitos','Bacterias','Mecánico','Máquinas','Herramientas','Proyecciones','Caídas','Psicosocial','Estrés','Monotonía','Relaciones interpersonales','Fatiga','Público','Robos','Agresiones','Locativo','Estructura','Orden y aseo','Mobiliario','Señalización']; @endphp
                            @foreach($riesgos as $i => $rg)
                            <div class="chip">
                                <input type="checkbox" name="factores_riesgo[]" id="rg-{{ $i }}" value="{{ $rg }}">
                                <label for="rg-{{ $i }}"><span class="b">✓</span>{{ $rg }}</label>
                            </div>
                            @endforeach
                        </div>

                        <div class="so-grid" style="margin-top:24px;">
                            <div>
                                <label class="so-lbl mut">Uso de elementos de protección personal</label>
                                <div class="so-seg" style="margin-bottom:9px;">
                                    <div class="s"><input type="radio" name="epp_usa" id="epp-si" value="SI"><label for="epp-si">Sí</label></div>
                                    <div class="s"><input type="radio" name="epp_usa" id="epp-no" value="NO"><label for="epp-no">No</label></div>
                                </div>
                                <input type="text" name="epp_detalle" class="so-in" placeholder="¿Cuáles? / observaciones">
                            </div>
                            <div>
                                <label class="so-lbl mut">Cuenta con restricciones médicas</label>
                                <div class="so-seg" style="margin-bottom:9px;">
                                    <div class="s"><input type="radio" name="restricciones_previas" id="rp-si" value="SI"><label for="rp-si">Sí</label></div>
                                    <div class="s"><input type="radio" name="restricciones_previas" id="rp-no" value="NO"><label for="rp-no">No</label></div>
                                </div>
                                <input type="text" name="restricciones_previas_detalle" class="so-in" placeholder="¿Cuáles?">
                            </div>
                        </div>
                        <div style="margin-top:14px;">
                            <label class="so-lbl mut">Motivo de consulta</label>
                            <input type="text" name="motivo_consulta" class="so-in" placeholder="Ej. Examen de ingreso">
                        </div>

                        <div class="so-sec">Descripción del estado de salud actual</div>
                        <textarea name="estado_salud" rows="3" class="so-in" placeholder="Describe el estado de salud actual del paciente…"></textarea>

                        <div class="so-sec">Antecedentes ocupacionales</div>
                        <div class="so-grid tight">
                            <div><label class="so-lbl mut">Empresa</label><input type="text" name="antecedentes_ocupacionales[empresa]" class="so-in"></div>
                            <div><label class="so-lbl mut">Cargo</label><input type="text" name="antecedentes_ocupacionales[cargo]" class="so-in"></div>
                            <div><label class="so-lbl mut">Tiempo de exposición</label><input type="text" name="antecedentes_ocupacionales[tiempo]" class="so-in"></div>
                            <div><label class="so-lbl mut">Descripción de la tarea</label><input type="text" name="antecedentes_ocupacionales[tarea]" class="so-in"></div>
                        </div>

                        <div class="so-sec">Accidentes laborales</div>
                        <div class="so-row2"><label>Empresa</label><input type="text" name="accidentes_laborales[empresa]" class="so-in"></div>
                        <div class="so-row2"><label>Cargo</label><input type="text" name="accidentes_laborales[cargo]" class="so-in"></div>
                        <div class="so-row2"><label>Fecha del evento</label><input type="date" name="accidentes_laborales[fecha]" class="so-in"></div>
                        <div class="so-row2" style="align-items:flex-start;"><label>Descripción</label><textarea name="accidentes_laborales[descripcion]" rows="2" class="so-in"></textarea></div>

                        <div class="so-sec">Enfermedad laboral</div>
                        <div class="so-row2"><label>Empresa</label><input type="text" name="enfermedad_laboral[empresa]" class="so-in"></div>
                        <div class="so-row2"><label>Cargo</label><input type="text" name="enfermedad_laboral[cargo]" class="so-in"></div>
                        <div class="so-row2"><label>Fecha del evento</label><input type="date" name="enfermedad_laboral[fecha]" class="so-in"></div>
                        <div class="so-row2" style="align-items:flex-start;"><label>Descripción</label><textarea name="enfermedad_laboral[descripcion]" rows="2" class="so-in"></textarea></div>

                        <div class="so-sec">Antecedentes familiares</div>
                        <textarea name="antecedentes_familiares" rows="2" class="so-in" placeholder="Antecedentes familiares relevantes…"></textarea>

                        <div class="so-sec">Antecedentes personales</div>
                        @foreach(['patologicos'=>'Patológicos','quirurgicos'=>'Quirúrgicos','farmacologicos'=>'Farmacológicos','traumaticos'=>'Traumáticos','gineco'=>'Ginecoobstétricos','hospitalarios'=>'Hospitalarios','alergicos'=>'Alérgicos','otros'=>'Otros, vacunación'] as $k=>$lb)
                        <div class="so-row2"><label>{{ $lb }}</label><input type="text" name="antecedentes_personales[{{ $k }}]" class="so-in"></div>
                        @endforeach

                        <div class="so-sec">Hábitos extralaborales</div>
                        @foreach(['fuma'=>'Fuma','licor'=>'Licor','ejercicio'=>'Ejercicio','sustancias'=>'Sustancias psicoactivas'] as $k=>$lb)
                        <div class="so-row2"><label>{{ $lb }}</label><input type="text" name="habitos[{{ $k }}]" class="so-in"></div>
                        @endforeach

                        <div class="so-sec">Revisión de síntomas por sistemas</div>
                        <textarea name="revision_sistemas" rows="3" class="so-in" placeholder="Revisión de síntomas por sistemas…"></textarea>

                        <div class="so-sec dark">Examen físico ocupacional</div>
                        <div class="so-grid tight">
                            <div><label class="so-lbl mut">Peso (kg)</label><input type="text" name="signos_vitales[peso]" id="sv-peso" class="so-in"></div>
                            <div><label class="so-lbl mut">Talla (cm)</label><input type="text" name="signos_vitales[talla]" id="sv-talla" class="so-in"></div>
                            <div><label class="so-lbl mut">IMC</label><input type="text" name="signos_vitales[imc]" id="sv-imc" class="so-in"></div>
                            <div><label class="so-lbl mut">Dominancia</label><input type="text" name="signos_vitales[dominancia]" class="so-in"></div>
                            <div><label class="so-lbl mut">P/A</label><input type="text" name="signos_vitales[pa]" class="so-in"></div>
                            <div><label class="so-lbl mut">Clasificación</label><input type="text" name="signos_vitales[clasificacion]" class="so-in"></div>
                            <div><label class="so-lbl mut">FC</label><input type="text" name="signos_vitales[fc]" class="so-in"></div>
                            <div><label class="so-lbl mut">FR</label><input type="text" name="signos_vitales[fr]" class="so-in"></div>
                            <div><label class="so-lbl mut">T°</label><input type="text" name="signos_vitales[temp]" class="so-in"></div>
                        </div>
                        <div style="margin-top:14px;"><label class="so-lbl mut">Aspecto general</label><input type="text" name="aspecto_general" class="so-in"></div>

                        <div class="so-sec">Examen físico por sistemas</div>
                        @foreach(['snc'=>'SNC','cabeza_cuello'=>'Cabeza y cuello','torax'=>'Tórax','corazon'=>'Corazón','pulmones'=>'Pulmones','abdomen'=>'Abdomen','piel'=>'Piel','miembros_sup'=>'Miembros superiores','miembros_inf'=>'Miembros inferiores','columna'=>'Columna'] as $k=>$lb)
                        <div class="so-row2"><label>{{ $lb }}</label><input type="text" name="examen_sistemas[{{ $k }}]" class="so-in"></div>
                        @endforeach
                        <div class="so-row2" style="align-items:flex-start;"><label>Osteomuscular</label><textarea name="examen_sistemas[osteomuscular]" rows="3" class="so-in"></textarea></div>

                        <div class="so-sec">Diagnóstico</div>
                        <textarea name="diagnostico" rows="3" class="so-in" placeholder="Diagnóstico(s)…"></textarea>

                        <div class="so-sec">Sistemas de vigilancia epidemiológica</div>
                        <textarea name="vigilancia_epidemiologica" rows="3" class="so-in" placeholder="Sistemas de vigilancia epidemiológica aplicables…"></textarea>
                    </section>

                    {{-- PASO 5 · CONCEPTO --}}
                    <section class="so-panel so-step" data-step="4" style="display:none;">
                        <div class="so-eyebrow">Paso 5 de 7</div>
                        <h2>Concepto médico ocupacional</h2>
                        <p class="sub">Selecciona el concepto emitido según la evaluación médica.</p>

                        <div class="so-cc" style="margin-top:20px;">
                            <div class="ok">
                                <input type="radio" name="concepto_resultado" id="cc-apto" value="apto">
                                <label for="cc-apto"><span class="dot">✓</span><span><span class="t">Apto</span><span class="d">Sin restricciones para el desempeño del cargo.</span></span></label>
                            </div>
                            <div class="warn">
                                <input type="radio" name="concepto_resultado" id="cc-aptor" value="apto_restricciones">
                                <label for="cc-aptor"><span class="dot">✓</span><span><span class="t">Apto con restricciones</span><span class="d">Puede desempeñar el cargo con recomendaciones.</span></span></label>
                            </div>
                            <div class="bad">
                                <input type="radio" name="concepto_resultado" id="cc-conr" value="con_restricciones">
                                <label for="cc-conr"><span class="dot">✓</span><span><span class="t">Con restricciones que impiden el desempeño</span><span class="d">No apto para el cargo evaluado.</span></span></label>
                            </div>
                        </div>
                        <div style="margin-top:14px;padding:13px 17px;background:var(--so-soft);border-radius:12px;font-size:13.5px;color:var(--so-mut);">
                            Certificado emitido de acuerdo con la Evaluación Médica Ocupacional realizada el <strong style="color:var(--so-brand)">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong>.
                        </div>
                    </section>

                    {{-- PASO 6 · RECOMENDACIONES + FIRMA --}}
                    <section class="so-panel so-step" data-step="5" style="display:none;">
                        <div class="so-eyebrow">Paso 6 de 7</div>
                        <h2>Recomendaciones y firma</h2>
                        <p class="sub">Registra las recomendaciones, restricciones y firma del médico.</p>

                        <div style="margin-top:20px;">
                            <label class="so-lbl">Recomendaciones médicas y compromisos</label>
                            <textarea name="recomendaciones" rows="3" class="so-in" placeholder="Describe las recomendaciones y compromisos…"></textarea>
                        </div>
                        <div style="margin-top:16px;">
                            <label class="so-lbl">Restricciones médicas ocupacionales</label>
                            <textarea name="restricciones" rows="3" class="so-in" placeholder="Describe las restricciones ocupacionales…"></textarea>
                        </div>
                        <div style="margin-top:16px;">
                            <label class="so-lbl">Recomendaciones para el área de SST</label>
                            <textarea name="sst" rows="3" class="so-in" placeholder="Recomendaciones para Seguridad y Salud en el Trabajo…"></textarea>
                        </div>

                        <div style="margin-top:22px;border-top:1px dashed var(--so-line);padding-top:18px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:9px;">
                                <label class="so-lbl" style="margin:0;">Firma del médico</label>
                                <button type="button" id="so-sign-clear" class="so-btn ghost" style="padding:6px 13px;font-size:13px;">Borrar firma</button>
                            </div>
                            <canvas id="so-sign" width="640" height="180"></canvas>
                            <div class="so-grid" style="margin-top:16px;">
                                <div><label class="so-lbl">Nombre del médico</label><input type="text" name="medico" class="so-in" value="{{ $medicoNombre }}"></div>
                                <div><label class="so-lbl">Registro / cargo</label><input type="text" name="registro" class="so-in" value="Médico(a) SG-SST" placeholder="Registro médico"></div>
                            </div>
                        </div>
                    </section>

                    {{-- PASO 7 · REVISIÓN --}}
                    <section class="so-panel so-step" data-step="6" style="display:none;">
                        <div class="so-eyebrow">Paso 7 de 7</div>
                        <h2>Revisión del certificado</h2>
                        <p class="sub">Verifica la información y guarda para generar el certificado imprimible.</p>

                        <div class="so-cert" style="margin-top:18px;">
                            <div class="so-cert-h">
                                <div class="lg">HUV</div>
                                <div style="flex:1;">
                                    <div style="font-size:10.5px;letter-spacing:.12em;text-transform:uppercase;opacity:.8;">Hospital Universitario del Valle · Evaristo García E.S.E</div>
                                    <div style="font-size:16px;font-weight:800;">Concepto Médico Ocupacional</div>
                                </div>
                                <div style="text-align:right;font-size:12px;opacity:.9;">{{ \Carbon\Carbon::now()->format('d/m/Y') }}<br><span id="rv-tipo" style="opacity:.75;">—</span></div>
                            </div>
                            <div style="padding:20px;">
                                <div class="so-sec" style="margin-top:0;">Identificación del paciente</div>
                                <div class="so-kv">
                                    <div><div class="k">Nombre</div><div class="v" id="rv-nombre">—</div></div>
                                    <div><div class="k">Identificación</div><div class="v" id="rv-ident">—</div></div>
                                    <div><div class="k">Edad</div><div class="v" id="rv-edad">—</div></div>
                                    <div><div class="k">Género</div><div class="v" id="rv-genero">—</div></div>
                                </div>
                                <div class="so-sec">Datos laborales</div>
                                <div class="so-kv">
                                    <div><div class="k">Cargo</div><div class="v" id="rv-cargo">—</div></div>
                                    <div><div class="k">Servicio</div><div class="v" id="rv-servicio">—</div></div>
                                    <div><div class="k">EPS</div><div class="v" id="rv-eps">—</div></div>
                                    <div><div class="k">ARL</div><div class="v" id="rv-arl">—</div></div>
                                </div>
                                <div class="so-cert-badge">
                                    <div style="font-size:10.5px;color:var(--so-brand);text-transform:uppercase;letter-spacing:.1em;font-weight:700;margin-bottom:4px;">Concepto emitido</div>
                                    <div style="font-size:19px;font-weight:800;color:var(--so-brand);" id="rv-concepto">—</div>
                                </div>
                                <div style="margin-bottom:8px;"><div class="k" style="font-size:10.5px;color:#9297ae;text-transform:uppercase;">Recomendaciones</div><div class="v" id="rv-recom" style="font-size:14px;color:#3a3e56;white-space:pre-wrap;">—</div></div>
                                <div style="margin-bottom:8px;"><div class="k" style="font-size:10.5px;color:#9297ae;text-transform:uppercase;">Restricciones</div><div class="v" id="rv-restr" style="font-size:14px;color:#3a3e56;white-space:pre-wrap;">—</div></div>
                                <div style="border-top:1px solid var(--so-line);margin-top:16px;padding-top:16px;">
                                    <img id="rv-firma" alt="Firma" style="height:64px;object-fit:contain;display:none;">
                                    <div style="width:260px;border-top:1.5px solid var(--so-text);padding-top:6px;margin-top:4px;">
                                        <div style="font-weight:700;" id="rv-medico">—</div>
                                        <div style="font-size:12.5px;color:var(--so-mut);" id="rv-registro">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- NAV --}}
                    <div class="so-panel" style="padding-top:0;">
                        <div class="so-nav">
                            <button type="button" class="so-btn ghost" id="so-prev" style="display:none;"><i class="fas fa-arrow-left"></i> Anterior</button>
                            <div style="flex:1;"></div>
                            <button type="button" class="so-btn prim" id="so-next">Continuar <i class="fas fa-arrow-right"></i></button>
                            <button type="submit" class="so-btn ok" id="so-save" style="display:none;"><i class="fas fa-save"></i> Guardar y generar certificado</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PACIENTE --}}
<div class="modal fade" id="pacienteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
      <div class="modal-header" style="background:var(--so-brand);color:#fff;border:none;">
        <h5 class="modal-title" id="pacienteModalTitle"><i class="fas fa-user-plus mr-2"></i>Crear paciente</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" style="background:#fff;">
        <form id="pacienteForm">
          <input type="hidden" id="pf-id">
          <div style="display:flex;align-items:flex-start;gap:9px;background:#eef0fb;border:1px solid #d6dbf0;border-radius:10px;padding:11px 14px;margin-bottom:16px;font-size:13px;color:#3a3e56;">
            <i class="fas fa-id-badge" style="color:var(--so-brand);margin-top:2px;"></i>
            <span>El paciente se registrará con vinculación <strong style="color:var(--so-brand)">Planta</strong>, ya que solo estos trabajadores pueden ser atendidos en Salud Ocupacional.</span>
          </div>
          <div class="so-grid">
            <div><label class="so-lbl">Nombres <span class="text-danger">*</span></label><input type="text" id="pf-name" class="so-in" required></div>
            <div><label class="so-lbl">Primer apellido</label><input type="text" id="pf-apellido1" class="so-in"></div>
            <div><label class="so-lbl">Segundo apellido</label><input type="text" id="pf-apellido2" class="so-in"></div>
            <div><label class="so-lbl">N.º identificación <span class="text-danger">*</span></label><input type="text" id="pf-identificacion" class="so-in" required></div>
            <div><label class="so-lbl">Género</label>
                <select id="pf-genero" class="so-in"><option value="">—</option><option value="F">Femenino</option><option value="M">Masculino</option><option value="O">Otro</option></select>
            </div>
            <div><label class="so-lbl">Edad</label><input type="number" id="pf-edad" class="so-in" min="0" max="150"></div>
            <div><label class="so-lbl">Fecha de nacimiento</label><input type="date" id="pf-fnacimiento" class="so-in"></div>
            <div><label class="so-lbl">Grupo sanguíneo</label>
                <select id="pf-grupo_sanguineo" class="so-in"><option value="">—</option><option>O+</option><option>O-</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option></select>
            </div>
            <div><label class="so-lbl">Lugar de nacimiento</label><input type="text" id="pf-lugar_nacimiento" class="so-in"></div>
            <div><label class="so-lbl">Teléfono</label><input type="text" id="pf-contacto" class="so-in" maxlength="20"></div>
            <div><label class="so-lbl">Correo electrónico</label><input type="email" id="pf-email" class="so-in"></div>
            <div style="grid-column:1/-1;"><label class="so-lbl">Dirección de residencia</label><input type="text" id="pf-direccionr" class="so-in"></div>
            <div><label class="so-lbl">Estrato</label>
                <select id="pf-estracto" class="so-in"><option value="">—</option>@for($i=1;$i<=6;$i++)<option>{{ $i }}</option>@endfor</select>
            </div>
            <div><label class="so-lbl">Tipo de vivienda</label>
                <select id="pf-tvivienda" class="so-in"><option value="">—</option><option>Propia</option><option>Arrendada</option><option>Familiar</option></select>
            </div>
            <div><label class="so-lbl">Estado civil</label>
                <select id="pf-escivil" class="so-in"><option value="">—</option><option>Soltero(a)</option><option>Casado(a)</option><option>Unión libre</option><option>Separado(a)</option><option>Divorciado(a)</option><option>Viudo(a)</option></select>
            </div>
            <div><label class="so-lbl">N.º de hijos</label><input type="number" id="pf-numero_hijos" class="so-in" min="0" max="50"></div>
            <div><label class="so-lbl">Escolaridad</label>
                <select id="pf-escolaridad" class="so-in"><option value="">—</option><option>Primaria</option><option>Bachillerato</option><option>Técnico</option><option>Tecnólogo</option><option>Universitario</option><option>Posgrado</option></select>
            </div>
            <div><label class="so-lbl">Profesión</label><input type="text" id="pf-profesion" class="so-in"></div>
            <div><label class="so-lbl">Cargo</label><input type="text" id="pf-cargo" list="dl-cargo" class="so-in"></div>
            <div><label class="so-lbl">Servicio</label><input type="text" id="pf-servicio" list="dl-serv" class="so-in"></div>
            <div><label class="so-lbl">EPS</label><input type="text" id="pf-eps" list="dl-eps" class="so-in"></div>
            <div><label class="so-lbl">AFP</label><input type="text" id="pf-afp" list="dl-afp" class="so-in"></div>
            <div><label class="so-lbl">ARL</label><input type="text" id="pf-arl" list="dl-arl" class="so-in"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--so-line);">
        <button type="button" class="so-btn ghost" data-dismiss="modal" style="padding:9px 18px;">Cancelar</button>
        <button type="button" class="so-btn prim" id="pf-save" style="padding:9px 20px;"><i class="fas fa-save"></i> Guardar paciente</button>
      </div>
    </div>
  </div>
</div>

{{-- VISOR DOCUMENTOS --}}
<div class="modal fade" id="docsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
      <div class="modal-header" style="background:var(--so-brand);color:#fff;border:none;">
        <h5 class="modal-title"><i class="fas fa-folder-open mr-2"></i>Documentos de la EPS</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" style="background:#eef0f7;min-height:60vh;" id="docsViewer"></div>
    </div>
  </div>
</div>

{{-- DATALISTS --}}
<datalist id="dl-cargo">
    @foreach($cargos as $c)<option value="{{ $c }}"></option>@endforeach
    <option>Auxiliar de enfermería</option><option>Enfermero(a) jefe</option><option>Médico general</option><option>Médico especialista</option><option>Camillero</option><option>Terapeuta respiratorio</option><option>Bacteriólogo(a)</option><option>Auxiliar administrativo</option><option>Instrumentador(a) quirúrgico</option>
</datalist>
<datalist id="dl-serv">
    @foreach($servicios as $s)<option value="{{ $s }}"></option>@endforeach
</datalist>
<datalist id="dl-eps">
    <option>Nueva EPS</option><option>EPS Sura</option><option>EPS Sanitas</option><option>Salud Total</option><option>Compensar</option><option>Famisanar</option><option>Coosalud</option><option>Emssanar</option><option>Servicio Occidental de Salud (SOS)</option><option>Comfenalco Valle</option><option>Asmet Salud</option>
</datalist>
<datalist id="dl-afp">
    <option>Porvenir</option><option>Protección</option><option>Colfondos</option><option>Skandia</option><option>Colpensiones</option>
</datalist>
<datalist id="dl-arl">
    <option>ARL Sura</option><option>Positiva</option><option>Colmena Seguros</option><option>Seguros Bolívar</option><option>AXA Colpatria</option><option>La Equidad Seguros</option><option>Mapfre</option>
</datalist>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
    'use strict';
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const ROUTES = {
        buscar:  "{{ url('salud-ocupacional/concepto/paciente') }}",
        store:   "{{ route('salud.concepto.paciente.store') }}",
        update:  "{{ url('salud-ocupacional/concepto/paciente') }}",
    };
    const TIPOS = @json($tipos);
    const CONCEPTOS = @json($conceptos);
    const GENERO = { F:'Femenino', M:'Masculino', O:'Otro' };

    let step = 0;
    const totalSteps = 7;
    let currentPatient = null;   // objeto paciente cargado (o null)
    let attachedFiles = [];      // {file, url, isImg, isPdf}

    const $  = (s,ctx=document)=>ctx.querySelector(s);
    const $$ = (s,ctx=document)=>Array.from(ctx.querySelectorAll(s));
    const val = (id)=>{ const el=$('#'+id); return el ? el.value : ''; };
    const setTxt = (id,v)=>{ const el=$('#'+id); if(el) el.textContent = v||'—'; };

    // ─── Navegación de pasos ───
    function showStep(n){
        step = Math.max(0, Math.min(totalSteps-1, n));
        $$('.so-step').forEach(p=> p.style.display = (+p.dataset.step===step)?'block':'none');
        $$('#so-steps button').forEach(b=>{
            const i = +b.dataset.step;
            b.classList.toggle('active', i===step);
            b.classList.toggle('done', i<step);
        });
        $('#so-prev').style.display = step>0 ? 'inline-flex':'none';
        $('#so-next').style.display = step<totalSteps-1 ? 'inline-flex':'none';
        $('#so-save').style.display = step===totalSteps-1 ? 'inline-flex':'none';
        if(step===totalSteps-1) fillReview();
        updateProgress();
        try{ document.querySelector('.content-wrapper').scrollTo({top:0,behavior:'smooth'}); }catch(e){}
    }
    $('#so-next').addEventListener('click', ()=> showStep(step+1));
    $('#so-prev').addEventListener('click', ()=> showStep(step-1));
    $$('#so-steps button').forEach(b=> b.addEventListener('click', ()=> showStep(+b.dataset.step)));

    function updateProgress(){
        const tracked = ['tipo_atencion:radio','f-identificacion','f-cargo','concepto_resultado:radio','medico'];
        let filled = 0;
        // heurística simple de progreso
        if($('input[name="tipo_atencion"]:checked')) filled++;
        if(val('f-identificacion').trim()) filled++;
        if(val('f-cargo').trim()) filled++;
        if($('input[name="concepto_resultado"]:checked')) filled++;
        if($('[name="medico"]') && $('[name="medico"]').value.trim()) filled++;
        // el progreso también avanza con el paso actual
        const byStep = Math.round((step/(totalSteps-1))*100);
        const byData = Math.round((filled/5)*100);
        const pct = Math.max(byStep, byData);
        $('#so-bar').style.width = pct+'%';
        $('#so-pct').textContent = pct+'%';
    }
    document.addEventListener('input', updateProgress);
    document.addEventListener('change', updateProgress);

    // ─── Paso 2: búsqueda / auto-completado ───
    const identInput = $('#f-identificacion');
    let searchTimer = null;
    identInput.addEventListener('input', ()=>{
        clearTimeout(searchTimer);
        currentPatient = null; $('#f-user_id').value=''; clearPatientFields();
        setPill('idle','Escribe una identificación para buscar');
        const v = identInput.value.trim();
        if(v.length >= 4){ searchTimer = setTimeout(()=>buscarPaciente(v), 450); }
    });
    $('#so-ident-search').addEventListener('click', ()=>{ const v=identInput.value.trim(); if(v) buscarPaciente(v); });

    function setPill(kind, text){
        const el = $('#so-ident-pill');
        el.className = 'so-pill '+kind;
        let icon = kind==='found' ? '<i class="fas fa-check-circle"></i>'
                 : (kind==='bad' ? '<i class="fas fa-ban"></i>'
                 : (kind==='new' ? '<i class="fas fa-exclamation-circle"></i>'
                 : '<i class="fas fa-circle" style="font-size:7px;"></i>'));
        el.innerHTML = icon+' '+text;
        // icono del botón de acción
        $('#so-ident-icon').className = (kind==='found') ? 'fas fa-user-edit' : 'fas fa-user-plus';
    }

    function buscarPaciente(ident){
        setPill('idle','Buscando…');
        fetch(ROUTES.buscar+'/'+encodeURIComponent(ident), {headers:{'Accept':'application/json'}})
            .then(r=>r.json())
            .then(res=>{
                if(res.found && res.elegible){
                    currentPatient = res.paciente;
                    fillPatientFields(res.paciente);
                    setPill('found','Paciente encontrado: '+res.paciente.nombre_completo);
                    renderHistorial(res.historial||[]);
                } else if(res.found && !res.elegible){
                    // Existe pero su vinculación NO es Planta → no puede ser atendido
                    currentPatient = null; $('#f-user_id').value=''; clearPatientFields();
                    setPill('bad', res.message || 'El trabajador no es de vinculación Planta y no puede ser atendido.');
                    $('#so-hist-card').style.display='none';
                } else {
                    currentPatient = null; $('#f-user_id').value=''; clearPatientFields();
                    setPill('new','No existe. Usa el botón + para crearlo');
                    $('#so-hist-card').style.display='none';
                }
            })
            .catch(()=> setPill('new','No se pudo consultar. Intenta de nuevo'));
    }

    function fillPatientFields(p){
        $('#f-user_id').value = p.id || '';
        $('#f-paciente_nombre').value = p.nombre_completo || '';
        $('#p-nombre').value = p.nombre_completo || '';
        $('#p-vinculacion').value = p.vinculacion || 'Planta';
        $('#p-edad').value = p.edad || '';
        $('#p-genero').value = p.genero || '';
        $('#p-genero-txt').value = GENERO[p.genero] || p.genero || '';
        $('#p-grupo').value = p.grupo_sanguineo || '';
        $('#p-fnac').value = p.fnacimiento || '';
        $('#p-lugarnac').value = p.lugar_nacimiento || '';
        $('#p-contacto').value = p.contacto || '';
        $('#p-correo').value = p.email || '';
        $('#p-direccion').value = p.direccionr || '';
        $('#p-estrato').value = p.estracto || '';
        $('#p-vivienda').value = p.tvivienda || '';
        $('#p-escivil').value = p.escivil || '';
        $('#p-hijos').value = (p.numero_hijos!=null?p.numero_hijos:'');
        $('#p-escolaridad').value = p.escolaridad || '';
        $('#p-profesion').value = p.profesion || '';
        // Prefill laborales (paso 3)
        if(p.cargo) $('#f-cargo').value = p.cargo;
        if(p.servicio) $('#f-servicio').value = p.servicio;
        if(p.eps) $('#f-eps').value = p.eps;
        if(p.afp) $('#f-afp').value = p.afp;
        if(p.arl) $('#f-arl').value = p.arl;
        renderPatientCard(p);
        updateProgress();
    }
    function clearPatientFields(){
        renderPatientCard(null);
        ['p-nombre','p-vinculacion','p-edad','p-genero','p-genero-txt','p-grupo','p-fnac','p-lugarnac','p-contacto','p-correo','p-direccion','p-estrato','p-vivienda','p-escivil','p-hijos','p-escolaridad','p-profesion','f-paciente_nombre'].forEach(id=>{ const el=$('#'+id); if(el) el.value=''; });
    }

    function renderHistorial(list){
        const card = $('#so-hist-card'), box = $('#so-hist-list');
        if(!list.length){ card.style.display='none'; return; }
        card.style.display='block';
        $('#so-hist-count').textContent = list.length;
        box.innerHTML = list.map(h=>`<a href="${h.url}" target="_blank"><span class="c">${h.concepto}</span><span class="d">${h.fecha||''} · ${h.tipo||''}</span></a>`).join('');
    }

    // Ilustra los datos del paciente seleccionado (tarjeta lateral persistente)
    function initials(name){
        const p = (name||'').trim().split(/\s+/);
        return ((p[0]||'')[0]||'') + ((p[1]||'')[0]||'');
    }
    function renderPatientCard(p){
        const card = $('#so-patient-card');
        if(!p){ card.style.display='none'; return; }
        card.style.display='block';
        $('#pc-av').textContent = (initials(p.nombre_completo)||'PA').toUpperCase();
        $('#pc-nombre').textContent = p.nombre_completo || '—';
        $('#pc-ident').textContent = p.identificacion || '—';
        const chips = [];
        if(p.edad) chips.push((p.edad)+' años');
        if(p.genero) chips.push(GENERO[p.genero] || p.genero);
        if(p.grupo_sanguineo) chips.push(p.grupo_sanguineo);
        chips.push('<span class="so-pchip vinc">'+(p.vinculacion || 'Planta')+'</span>');
        $('#pc-chips').innerHTML = chips.map(c=> c.startsWith('<span') ? c : `<span class="so-pchip">${c}</span>`).join('');
        setTxt('pc-vinc', p.vinculacion || 'Planta');
        setTxt('pc-eps', p.eps);
        setTxt('pc-tel', p.contacto);
        setTxt('pc-cargo', p.cargo);
        setTxt('pc-serv', p.servicio);
    }

    // ─── Modal paciente (crear / editar) ───
    const PF = ['name','apellido1','apellido2','identificacion','genero','edad','fnacimiento','grupo_sanguineo','lugar_nacimiento','contacto','email','direccionr','estracto','tvivienda','escivil','numero_hijos','escolaridad','profesion','cargo','servicio','eps','afp','arl'];

    $('#so-ident-action').addEventListener('click', ()=>{
        if(currentPatient){ openPatientModal('edit', currentPatient); }
        else { openPatientModal('create', { identificacion: identInput.value.trim() }); }
    });

    function openPatientModal(mode, data){
        $('#pf-id').value = (mode==='edit' && data) ? (data.id||'') : '';
        PF.forEach(k=>{ const el=$('#pf-'+k); if(el) el.value = (data && data[k]!=null) ? data[k] : ''; });
        $('#pacienteModalTitle').innerHTML = (mode==='edit')
            ? '<i class="fas fa-user-edit mr-2"></i>Editar paciente'
            : '<i class="fas fa-user-plus mr-2"></i>Crear paciente';
        $('#pf-save').innerHTML = '<i class="fas fa-save"></i> '+(mode==='edit'?'Actualizar':'Guardar')+' paciente';
        $('#pacienteForm').dataset.mode = mode;
        window.jQuery && window.jQuery('#pacienteModal').modal('show');
    }

    $('#pf-save').addEventListener('click', ()=>{
        const mode = $('#pacienteForm').dataset.mode || 'create';
        const id = $('#pf-id').value;
        if(!$('#pf-name').value.trim() || !$('#pf-identificacion').value.trim()){
            Swal.fire({icon:'warning',title:'Campos requeridos',text:'Nombre e identificación son obligatorios.'});
            return;
        }
        const payload = {};
        PF.forEach(k=>{ payload[k] = $('#pf-'+k) ? $('#pf-'+k).value : ''; });

        const url = mode==='edit' ? (ROUTES.update+'/'+id) : ROUTES.store;
        const method = mode==='edit' ? 'PUT' : 'POST';

        $('#pf-save').disabled = true;
        fetch(url, {
            method,
            headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'},
            body: JSON.stringify(payload)
        })
        .then(async r=>{ const j = await r.json().catch(()=>({})); return {ok:r.ok, j}; })
        .then(({ok,j})=>{
            $('#pf-save').disabled = false;
            if(ok && j.ok){
                window.jQuery && window.jQuery('#pacienteModal').modal('hide');
                currentPatient = j.paciente;
                identInput.value = j.paciente.identificacion || identInput.value;
                fillPatientFields(j.paciente);
                setPill('found','Paciente '+(mode==='edit'?'actualizado':'creado')+': '+j.paciente.nombre_completo);
                buscarPaciente(j.paciente.identificacion);
                Swal.fire({icon:'success',title:'Listo',text:j.message||'Guardado',timer:1600,showConfirmButton:false});
            } else {
                const msg = j.message || (j.errors ? Object.values(j.errors).flat().join(' · ') : 'No se pudo guardar el paciente.');
                Swal.fire({icon:'error',title:'Error',text:msg});
            }
        })
        .catch(()=>{ $('#pf-save').disabled=false; Swal.fire({icon:'error',title:'Error',text:'Fallo de conexión.'}); });
    });

    // ─── Adjuntos ───
    $('#so-attach-btn').addEventListener('click', ()=> $('#f-docs').click());
    $('#f-docs').addEventListener('change', ()=>{
        const list = $('#f-docs').files;
        attachedFiles = [];
        for(const f of list){
            const isImg = f.type.startsWith('image/');
            const isPdf = f.type==='application/pdf';
            attachedFiles.push({file:f, url:URL.createObjectURL(f), isImg, isPdf, name:f.name, size:f.size});
        }
        renderDocs();
    });
    function fmtSize(b){ if(b<1024)return b+' B'; if(b<1048576)return (b/1024).toFixed(0)+' KB'; return (b/1048576).toFixed(1)+' MB'; }
    function renderDocs(){
        const box = $('#so-doc-list');
        box.innerHTML = attachedFiles.map((a,i)=>`
            <div class="so-doc">
                <span class="ic"><i class="fas fa-file-${a.isPdf?'pdf':(a.isImg?'image':'alt')}"></i></span>
                <div style="flex:1;min-width:0;"><div class="nm">${a.name}</div><div class="mt">${fmtSize(a.size)}</div></div>
                <button type="button" class="rm" data-i="${i}">Quitar</button>
            </div>`).join('');
        $$('.so-doc .rm', box).forEach(btn=> btn.addEventListener('click', ()=> removeDoc(+btn.dataset.i)));
        // Botón "Ver documentos" en paso 4
        const vd = $('#so-view-docs');
        if(attachedFiles.length){ vd.style.display='inline-flex'; $('#so-view-docs-n').textContent = attachedFiles.length; }
        else vd.style.display='none';
    }
    function removeDoc(i){
        // Rebuild the FileList minus i using DataTransfer
        const dt = new DataTransfer();
        attachedFiles.forEach((a,idx)=>{ if(idx!==i) dt.items.add(a.file); });
        $('#f-docs').files = dt.files;
        attachedFiles.splice(i,1);
        renderDocs();
    }
    $('#so-view-docs').addEventListener('click', ()=>{
        const v = $('#docsViewer');
        v.innerHTML = attachedFiles.map(a=>{
            if(a.isImg) return `<div style="margin-bottom:14px;"><div style="font-weight:700;margin-bottom:6px;">${a.name}</div><img src="${a.url}" style="max-width:100%;border-radius:8px;"></div>`;
            if(a.isPdf) return `<div style="margin-bottom:14px;"><div style="font-weight:700;margin-bottom:6px;">${a.name}</div><iframe src="${a.url}" style="width:100%;height:70vh;border:none;border-radius:8px;background:#fff;"></iframe></div>`;
            return `<div style="margin-bottom:14px;">${a.name} — <a href="${a.url}" target="_blank">abrir</a></div>`;
        }).join('');
        window.jQuery && window.jQuery('#docsModal').modal('show');
    });

    // ─── IMC automático ───
    function calcImc(){
        const peso = parseFloat($('#sv-peso').value.replace(',','.'));
        const talla = parseFloat($('#sv-talla').value.replace(',','.'));
        if(peso>0 && talla>0){ const m = talla/100; $('#sv-imc').value = (peso/(m*m)).toFixed(1); }
    }
    $('#sv-peso').addEventListener('input', calcImc);
    $('#sv-talla').addEventListener('input', calcImc);

    // ─── Firma ───
    const canvas = $('#so-sign'); const ctx = canvas.getContext('2d');
    let drawing=false, px=0, py=0;
    function pt(e){ const r=canvas.getBoundingClientRect(); return {x:(e.clientX-r.left)*(canvas.width/r.width), y:(e.clientY-r.top)*(canvas.height/r.height)}; }
    canvas.addEventListener('pointerdown', e=>{ e.preventDefault(); drawing=true; const p=pt(e); px=p.x; py=p.y; try{canvas.setPointerCapture(e.pointerId);}catch(_){} });
    canvas.addEventListener('pointermove', e=>{ if(!drawing)return; const p=pt(e); ctx.strokeStyle='#1a1d2e'; ctx.lineWidth=2.4; ctx.lineCap='round'; ctx.lineJoin='round'; ctx.beginPath(); ctx.moveTo(px,py); ctx.lineTo(p.x,p.y); ctx.stroke(); px=p.x; py=p.y; });
    canvas.addEventListener('pointerup', ()=>{ if(!drawing)return; drawing=false; try{ $('#f-firma').value = canvas.toDataURL(); }catch(_){} });
    $('#so-sign-clear').addEventListener('click', ()=>{ ctx.clearRect(0,0,canvas.width,canvas.height); $('#f-firma').value=''; });

    // ─── Revisión ───
    function fillReview(){
        const tipoR = $('input[name="tipo_atencion"]:checked');
        setTxt('rv-tipo', tipoR ? (TIPOS[tipoR.value]||'') : '—');
        setTxt('rv-nombre', $('#f-paciente_nombre').value || $('#p-nombre').value);
        setTxt('rv-ident', identInput.value);
        setTxt('rv-edad', $('#p-edad').value);
        setTxt('rv-genero', $('#p-genero-txt').value);
        setTxt('rv-cargo', $('#f-cargo').value);
        setTxt('rv-servicio', $('#f-servicio').value);
        setTxt('rv-eps', $('#f-eps').value);
        setTxt('rv-arl', $('#f-arl').value);
        const cc = $('input[name="concepto_resultado"]:checked');
        setTxt('rv-concepto', cc ? (CONCEPTOS[cc.value]||'') : '—');
        setTxt('rv-recom', $('[name="recomendaciones"]').value);
        setTxt('rv-restr', $('[name="restricciones"]').value);
        setTxt('rv-medico', $('[name="medico"]').value);
        setTxt('rv-registro', $('[name="registro"]').value);
        const firma = $('#f-firma').value;
        const img = $('#rv-firma');
        if(firma && firma.length>200){ img.src=firma; img.style.display='block'; } else img.style.display='none';
    }

    // ─── Validación al guardar ───
    $('#so-form').addEventListener('submit', function(e){
        if(!$('#f-user_id').value){
            e.preventDefault();
            Swal.fire({icon:'warning',title:'Paciente no válido',text:'Debes seleccionar un paciente de vinculación Planta. Búscalo por identificación o créalo con el botón +.'});
            showStep(1);
            return;
        }
        if(!$('input[name="concepto_resultado"]:checked')){
            e.preventDefault();
            Swal.fire({icon:'warning',title:'Falta el concepto',text:'Selecciona el concepto médico emitido.'});
            showStep(4);
            return;
        }
    });

    // init
    showStep(0);
})();
</script>
@endpush
