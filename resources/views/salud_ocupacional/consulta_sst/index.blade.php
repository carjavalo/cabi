@extends('layouts.app')

@section('title', 'Consulta SST')
@section('header', 'Salud Ocupacional · Consulta SST')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    :root{
        --so-brand:#2e3a75; --so-brand-d:#232c5c; --so-brand-l:#4657a8;
        --so-bg:#eef0fb; --so-soft:#f4f6fb; --so-line:#e1e5f2; --so-line2:#d5daea;
        --so-text:#1f2440; --so-mut:#7c82a0; --so-ok:#2e9e6b; --so-warn:#c98a1e; --so-bad:#c4453b; --so-info:#2f6fb0;
    }
    .cs-wrap{ color:var(--so-text); }
    /* Encabezado institucional (igual a Agenda y Concepto Médico) */
    .so-hero{ background:linear-gradient(120deg,var(--so-brand),var(--so-brand-l));color:#fff;border-radius:16px;padding:18px 22px;margin-bottom:18px;
        display:flex;align-items:center;gap:16px;flex-wrap:wrap;box-shadow:0 14px 30px -18px rgba(46,58,117,.75);position:relative;overflow:hidden; }
    .so-hero::after{ content:"";position:absolute;right:-60px;top:-80px;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.07); }
    .so-hero .badge-huv{ width:52px;height:52px;border-radius:13px;background:rgba(255,255,255,.16);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:17px;flex-shrink:0; }
    .so-hero small{ text-transform:uppercase;letter-spacing:.14em;opacity:.85;font-size:11px; }
    .so-hero h4{ margin:2px 0 0;font-weight:800;letter-spacing:-.01em; }
    .so-hero p{ margin:2px 0 0;opacity:.85;font-size:13.5px; }
    .so-hero .acc{ margin-left:auto;display:flex;gap:8px;position:relative;z-index:1; }
    .so-hero .acc a{ background:rgba(255,255,255,.16);color:#fff;border-radius:11px;padding:9px 14px;font-weight:700;font-size:13.5px;text-decoration:none;display:inline-flex;align-items:center;gap:8px; }
    .so-hero .acc a:hover{ background:rgba(255,255,255,.26); }

    .so-card{ background:#fff;border:1px solid var(--so-line);border-radius:16px;box-shadow:0 1px 2px rgba(46,58,117,.05),0 16px 34px -26px rgba(46,58,117,.4); }
    .so-lbl{ display:block;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--so-brand);margin-bottom:5px; }
    .so-in{ width:100%;padding:9px 12px;border:1px solid var(--so-line2);border-radius:10px;font-size:14px;background:#fff;outline:none;transition:border .15s,box-shadow .15s;color:var(--so-text); }
    .so-in:focus{ border-color:var(--so-brand-l);box-shadow:0 0 0 3px rgba(46,58,117,.12); }
    textarea.so-in{ resize:vertical;min-height:74px;line-height:1.45; }
    .so-btn{ display:inline-flex;align-items:center;justify-content:center;gap:7px;height:40px;padding:0 16px;border-radius:10px;font-size:13.5px;font-weight:700;cursor:pointer;border:none;white-space:nowrap;text-decoration:none; }
    .so-btn.prim{ background:var(--so-brand);color:#fff;box-shadow:0 10px 22px -12px rgba(46,58,117,.8); }
    .so-btn.prim:hover{ background:var(--so-brand-d);color:#fff; }
    .so-btn.ghost{ background:#fff;color:var(--so-brand);border:1.5px solid var(--so-line2); }
    .so-btn.ghost:hover{ background:var(--so-bg); }
    .so-btn:disabled{ opacity:.45;cursor:not-allowed;box-shadow:none; }

    /* Indicadores (también son filtros rápidos) */
    .cs-kpis{ display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-bottom:16px; }
    .cs-kpi{ position:relative;text-align:left;background:#fff;border:1.5px solid var(--so-line);border-radius:14px;padding:12px 14px 12px 16px;cursor:pointer;transition:transform .15s,box-shadow .15s,border-color .15s;overflow:hidden; }
    .cs-kpi::before{ content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--c); }
    .cs-kpi:hover{ transform:translateY(-2px);box-shadow:0 12px 24px -16px rgba(46,58,117,.55); }
    .cs-kpi.on{ border-color:var(--c);background:color-mix(in srgb,var(--c) 6%,#fff);box-shadow:0 0 0 3px color-mix(in srgb,var(--c) 18%,transparent); }
    .cs-kpi:focus{ outline:none; } .cs-kpi:focus-visible{ outline:2px solid var(--c);outline-offset:2px; }
    .cs-kpi .k{ font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;color:var(--so-mut);font-weight:700;display:flex;justify-content:space-between;align-items:center; }
    .cs-kpi .k i{ color:var(--c);font-size:14px; }
    .cs-kpi .v{ font-size:26px;font-weight:800;margin-top:2px;font-variant-numeric:tabular-nums;color:var(--so-text); }
    .cs-kpi .bar{ height:5px;border-radius:9px;background:var(--so-soft);margin-top:6px;overflow:hidden; }
    .cs-kpi .bar span{ display:block;height:100%;background:var(--c);border-radius:9px;transition:width .5s; }

    /* Filtros */
    .cs-filtros{ padding:16px;margin-bottom:16px; }
    .cs-frow{ display:grid;grid-template-columns:minmax(260px,2.2fr) repeat(2,minmax(150px,1fr)) repeat(2,minmax(150px,1fr));gap:12px;align-items:end; }
    .cs-search{ position:relative; }
    .cs-search > i{ position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--so-mut); }
    .cs-search .so-in{ padding-left:38px;padding-right:44px;height:42px; }
    .cs-search kbd{ position:absolute;right:10px;top:50%;transform:translateY(-50%);background:var(--so-soft);color:var(--so-mut);border:1px solid var(--so-line2);border-radius:6px;font-size:11px;padding:1px 7px;box-shadow:none; }
    .cs-filtros input[type=date].so-in, .cs-filtros select.so-in{ height:42px; }
    .cs-rapidos{ display:flex;flex-wrap:wrap;gap:6px;align-items:center;margin-top:12px; }
    .cs-rapidos .t{ font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--so-mut);margin-right:4px; }
    .cs-chip{ border:1.5px solid var(--so-line2);background:#fff;color:#4a4f66;border-radius:99px;padding:4px 12px;font-size:12.5px;font-weight:600;cursor:pointer; }
    .cs-chip:hover{ border-color:var(--so-brand-l);color:var(--so-brand); }
    .cs-chip.on{ background:var(--so-brand);border-color:var(--so-brand);color:#fff; }
    .cs-chip.limpiar{ border-style:dashed;margin-left:auto; }

    /* Tabla */
    .cs-bar{ display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:10px;padding:12px 16px;border-bottom:1px solid var(--so-line); }
    .cs-bar .info{ font-size:13px;color:var(--so-mut); }
    .cs-bar .info strong{ color:var(--so-text); }
    .cs-sel{ display:none;align-items:center;gap:10px;background:var(--so-bg);border:1px solid #d6dbf0;border-radius:11px;padding:5px 6px 5px 12px;font-size:13px; }
    .cs-sel.on{ display:flex;animation:csIn .2s ease; }
    .cs-sel .so-btn{ height:32px;padding:0 12px;font-size:12.5px; }
    .so-btn.xls{ height:34px;padding:0 13px;font-size:12.5px;background:#1d6f42;color:#fff;box-shadow:0 8px 18px -12px rgba(29,111,66,.9); }
    .so-btn.xls:hover{ background:#175935; }
    .so-btn.xls.cargando{ opacity:.7;pointer-events:none; }
    .cs-exp .dropdown-menu{ border:1px solid var(--so-line);border-radius:12px;box-shadow:0 16px 34px -18px rgba(20,25,60,.45);padding:6px;min-width:290px; }
    .cs-exp .dropdown-header{ font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:var(--so-mut);padding:6px 10px; }
    .cs-exp .dropdown-item{ display:flex;gap:11px;align-items:flex-start;border-radius:9px;padding:8px 10px;font-weight:700;font-size:13px;color:var(--so-text);white-space:normal; }
    .cs-exp .dropdown-item i{ width:18px;margin-top:2px;color:#1d6f42; }
    .cs-exp .dropdown-item small{ display:block;font-weight:400;color:var(--so-mut);font-size:11.5px; }
    .cs-exp .dropdown-item:hover{ background:#eef7f1; }
    .cs-table-wrap{ overflow-x:auto; }
    table.cs-table{ width:100%;border-collapse:separate;border-spacing:0;font-size:13.5px; }
    .cs-table thead th{ background:var(--so-soft);color:var(--so-brand);font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;padding:11px 12px;white-space:nowrap;border-bottom:1.5px solid var(--so-line);user-select:none; }
    .cs-table thead th[data-orden]{ cursor:pointer; }
    .cs-table thead th[data-orden]:hover{ color:var(--so-brand-l); }
    .cs-table thead th .s{ opacity:.35;margin-left:4px; }
    .cs-table thead th.on .s{ opacity:1; }
    .cs-table td{ padding:10px 12px;border-bottom:1px solid var(--so-line);vertical-align:middle; }
    .cs-table tbody tr{ cursor:pointer;transition:background .12s; }
    .cs-table tbody tr:hover{ background:#f8f9fe; }
    .cs-table tbody tr.sel{ background:var(--so-bg);box-shadow:inset 3px 0 0 var(--so-brand); }
    .cs-table tbody tr.anulado td{ color:#a3a8bd; }
    .cs-table tbody tr.anulado .cs-pac .n{ text-decoration:line-through; }
    .cs-radio{ width:18px;height:18px;border-radius:50%;border:2px solid var(--so-line2);display:inline-block;position:relative;vertical-align:middle; }
    tr.sel .cs-radio{ border-color:var(--so-brand); }
    tr.sel .cs-radio::after{ content:"";position:absolute;inset:3px;border-radius:50%;background:var(--so-brand); }
    .cs-num{ font-family:'Courier New',monospace;font-weight:700;color:var(--so-brand);white-space:nowrap; }
    .cs-fecha{ white-space:nowrap;font-variant-numeric:tabular-nums; }
    .cs-fecha small{ display:block;color:var(--so-mut);font-size:11.5px; }
    .cs-pac{ display:flex;align-items:center;gap:10px;min-width:220px; }
    .cs-av{ width:34px;height:34px;border-radius:50%;color:#fff;font-size:12px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .cs-pac .n{ font-weight:700;line-height:1.25; }
    .cs-pac .s{ font-size:11.5px;color:var(--so-mut); }
    .cs-tipo{ display:inline-block;font-size:11.5px;font-weight:700;padding:3px 9px;border-radius:99px;background:var(--so-bg);color:var(--so-brand);white-space:nowrap; }
    .cs-enf{ display:block;font-size:11px;color:var(--so-mut);margin-top:3px;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .cs-pill{ display:inline-flex;align-items:flex-start;gap:6px;font-size:12px;font-weight:700;padding:4px 10px;border-radius:9px;max-width:260px;line-height:1.3; }
    .cs-pill i{ margin-top:2px; }
    .cs-pill.ok{ background:#eaf7f0;color:var(--so-ok); } .cs-pill.warn{ background:#fbf3e4;color:#92611a; }
    .cs-pill.bad{ background:#fbebe9;color:var(--so-bad); } .cs-pill.none{ background:var(--so-soft);color:var(--so-mut); }
    .cs-pill.anul{ background:#f1f1f4;color:#8a8fa3; }
    .cs-mini{ display:flex;gap:6px;margin-top:4px;flex-wrap:wrap; }
    .cs-mini span{ font-size:10.5px;font-weight:700;color:var(--so-mut);background:var(--so-soft);border-radius:6px;padding:1px 6px;white-space:nowrap; }
    .cs-mini span.vig{ color:#92611a;background:#fbf3e4; }
    .cs-med{ font-size:12.5px;color:#4a4f66;max-width:170px; }
    .cs-acts{ display:flex;gap:4px;justify-content:flex-end; }
    .cs-act{ width:32px;height:32px;border-radius:9px;border:none;background:transparent;color:var(--so-brand);display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .12s,color .12s,transform .12s; }
    .cs-act:hover{ transform:translateY(-1px); }
    .cs-act.ver:hover{ background:#e4effa;color:var(--so-info); }
    .cs-act.edi:hover{ background:#fbf3e4;color:var(--so-warn); }
    .cs-act.imp:hover{ background:var(--so-bg);color:var(--so-brand); }
    .cs-act.eli:hover{ background:#fbebe9;color:var(--so-bad); }
    .cs-act:disabled{ opacity:.3;cursor:not-allowed;transform:none;background:none; }
    mark.cs-hl{ background:#fff1b8;color:inherit;padding:0 1px;border-radius:3px; }
    .cs-foot{ display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:10px;padding:12px 16px;background:var(--so-soft);border-radius:0 0 16px 16px; }
    .cs-foot select{ width:auto;height:34px;padding:0 8px; }
    .cs-pag{ display:flex;gap:4px;align-items:center; }
    .cs-pag button{ min-width:34px;height:34px;border-radius:9px;border:1.5px solid var(--so-line2);background:#fff;color:var(--so-brand);font-weight:700;font-size:13px;cursor:pointer; }
    .cs-pag button.on{ background:var(--so-brand);border-color:var(--so-brand);color:#fff; }
    .cs-pag button:disabled{ opacity:.4;cursor:not-allowed; }
    .cs-pag .dots{ color:var(--so-mut);padding:0 4px; }
    .cs-vacio{ padding:46px 20px;text-align:center;color:var(--so-mut); }
    .cs-vacio i{ font-size:38px;color:#c8cde2;margin-bottom:10px;display:block; }
    .cs-skel td{ padding:14px 12px; }
    .cs-skel span{ display:block;height:12px;border-radius:6px;background:linear-gradient(90deg,#eef0f6 25%,#f7f8fc 50%,#eef0f6 75%);background-size:200% 100%;animation:csSk 1.2s infinite; }
    @keyframes csSk{ from{ background-position:200% 0; } to{ background-position:-200% 0; } }
    @keyframes csIn{ from{ opacity:0;transform:translateY(-4px); } to{ opacity:1;transform:none; } }

    /* Panel lateral "Ver" */
    .cs-ov{ position:fixed;inset:0;background:rgba(20,25,55,.45);backdrop-filter:blur(2px);z-index:1060;opacity:0;pointer-events:none;transition:opacity .2s; }
    .cs-ov.on{ opacity:1;pointer-events:auto; }
    .cs-drawer{ position:fixed;top:0;right:0;bottom:0;width:min(620px,100%);background:#f7f8fc;z-index:1061;transform:translateX(100%);transition:transform .28s cubic-bezier(.2,.8,.2,1);display:flex;flex-direction:column;box-shadow:-20px 0 40px -20px rgba(20,25,55,.4); }
    .cs-drawer.on{ transform:none; }
    .cs-dh{ background:linear-gradient(120deg,var(--so-brand),var(--so-brand-l));color:#fff;padding:18px 20px; }
    .cs-dh .top{ display:flex;justify-content:space-between;align-items:center;gap:10px; }
    .cs-dh .num{ font-family:'Courier New',monospace;font-size:12px;letter-spacing:.12em;opacity:.85; }
    .cs-dh h5{ margin:6px 0 2px;font-weight:800;font-size:19px; }
    .cs-dh .sub{ font-size:13px;opacity:.85; }
    .cs-dh .x{ background:rgba(255,255,255,.16);border:none;color:#fff;width:34px;height:34px;border-radius:10px;cursor:pointer; }
    .cs-dh .acc{ display:flex;gap:8px;margin-top:14px;flex-wrap:wrap; }
    .cs-dh .acc button, .cs-dh .acc a{ background:rgba(255,255,255,.16);border:none;color:#fff;border-radius:9px;padding:7px 12px;font-size:12.5px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;gap:7px;align-items:center; }
    .cs-dh .acc button:hover, .cs-dh .acc a:hover{ background:rgba(255,255,255,.28); }
    .cs-db{ flex:1;overflow-y:auto;padding:16px 18px 30px; }
    .cs-box{ background:#fff;border:1px solid var(--so-line);border-radius:13px;padding:14px 16px;margin-bottom:12px; }
    .cs-box h6{ font-size:11px;letter-spacing:.09em;text-transform:uppercase;color:var(--so-brand);font-weight:800;margin:0 0 10px;display:flex;align-items:center;gap:7px; }
    .cs-kv{ display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px 18px; }
    .cs-kv .k{ font-size:10.5px;color:#9297ae;text-transform:uppercase;letter-spacing:.05em; }
    .cs-kv .v{ font-size:14px;font-weight:600;color:var(--so-text);overflow-wrap:anywhere; }
    .cs-txt{ font-size:13.5px;color:#3a3e56;line-height:1.5;white-space:pre-wrap;overflow-wrap:anywhere; }
    .cs-txt.vac{ color:#a3a8bd;font-style:italic; }
    .cs-big{ display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;font-weight:800;font-size:15px; }
    .cs-big.ok{ background:#eaf7f0;color:var(--so-ok); } .cs-big.warn{ background:#fbf3e4;color:#92611a; } .cs-big.bad{ background:#fbebe9;color:var(--so-bad); } .cs-big.none{ background:var(--so-soft);color:var(--so-mut); }
    .cs-big i{ font-size:22px; }
    .cs-anul{ display:flex;gap:10px;background:#fbebe9;border:1px solid #f1c7c2;color:#8c2f27;border-radius:12px;padding:12px 14px;margin-bottom:12px;font-size:13.5px; }
    .cs-tl{ position:relative;padding-left:18px; }
    .cs-tl::before{ content:"";position:absolute;left:5px;top:4px;bottom:4px;width:2px;background:var(--so-line); }
    .cs-tl .it{ position:relative;margin-bottom:12px; }
    .cs-tl .it::before{ content:"";position:absolute;left:-17px;top:4px;width:10px;height:10px;border-radius:50%;background:var(--so-warn);box-shadow:0 0 0 3px #fbf3e4; }
    .cs-tl .h{ font-size:12px;color:var(--so-mut); } .cs-tl .h strong{ color:var(--so-text); }
    .cs-tl .m{ font-size:13px;margin:3px 0 5px;font-style:italic;color:#4a4f66; }
    .cs-dif{ font-size:12.5px;border:1px solid var(--so-line);border-radius:9px;overflow:hidden; }
    .cs-dif div{ display:grid;grid-template-columns:130px 1fr;border-bottom:1px solid var(--so-line); }
    .cs-dif div:last-child{ border-bottom:none; }
    .cs-dif b{ background:var(--so-soft);padding:6px 8px;font-size:11px;color:var(--so-brand); }
    .cs-dif span{ padding:6px 8px;overflow-wrap:anywhere; }
    .cs-dif del{ color:var(--so-bad);background:#fdf0ee;text-decoration:line-through; }
    .cs-dif ins{ color:var(--so-ok);background:#eef9f3;text-decoration:none; }
    .cs-docs a{ display:flex;align-items:center;gap:9px;padding:8px 10px;border-radius:9px;color:var(--so-brand);text-decoration:none;font-size:13.5px; }
    .cs-docs a:hover{ background:var(--so-bg); }

    /* Modal de corrección */
    .cs-modal .modal-content{ border:none;border-radius:16px;overflow:hidden; }
    .cs-modal .modal-header{ background:linear-gradient(120deg,var(--so-brand),var(--so-brand-l));color:#fff;border:none; }
    .cs-modal .modal-header .close{ color:#fff;opacity:.9;text-shadow:none; }
    .cs-modal .modal-body{ background:#f7f8fc; }
    .cs-ops{ display:grid;gap:8px; }
    .cs-op{ display:flex;gap:10px;align-items:flex-start;border:1.5px solid var(--so-line2);border-radius:11px;padding:10px 12px;cursor:pointer;background:#fff;margin:0;font-weight:600;font-size:13.5px; }
    .cs-op input{ margin-top:3px;accent-color:var(--so-brand); }
    .cs-op small{ display:block;font-weight:400;color:var(--so-mut); }
    .cs-op.ok:has(input:checked){ border-color:var(--so-ok);background:#f2fbf6; }
    .cs-op.warn:has(input:checked){ border-color:var(--so-warn);background:#fefaf1; }
    .cs-op.bad:has(input:checked){ border-color:var(--so-bad);background:#fdf3f2; }
    .cs-aviso{ display:flex;gap:10px;background:#fbf3e4;border:1px solid #f0dcb4;color:#7a5216;border-radius:11px;padding:10px 13px;font-size:13px;margin-bottom:14px; }
    .cs-g2{ display:grid;grid-template-columns:1fr 1fr;gap:14px; }
    .cs-modal .sec{ background:#fff;border:1px solid var(--so-line);border-radius:13px;padding:14px;margin-bottom:12px; }

    @media(max-width:1200px){ .cs-frow{ grid-template-columns:1fr 1fr 1fr; } .cs-frow .cs-search{ grid-column:1 / -1; } }
    @media(max-width:768px){
        .cs-kpis{ grid-template-columns:repeat(2,1fr); }
        .cs-frow{ grid-template-columns:1fr 1fr; }
        .cs-g2{ grid-template-columns:1fr; }
        .so-hero .acc{ margin-left:0; }
    }
</style>
@endpush

@section('content')
<div class="cs-wrap">

    <div class="so-hero">
        <div class="badge-huv">HUV</div>
        <div>
            <small>Hospital Universitario del Valle · Evaristo García E.S.E</small>
            <h4>Consulta SST · Conceptos médicos ocupacionales</h4>
            <p>Busque, revise, corrija, anule e imprima los conceptos realizados.</p>
        </div>
        <div class="acc">
            <a href="{{ route('salud.concepto.index') }}" data-permiso="salud.concepto.index"><i class="fas fa-plus"></i> Nuevo concepto</a>
        </div>
    </div>

    @if($migracionPendiente)
    <div class="alert alert-warning" style="border-radius:12px;">
        <strong><i class="fas fa-database"></i> Configuración pendiente en el servidor:</strong>
        faltan las columnas de anulación y la tabla de auditoría de correcciones. Ejecute <code>php&nbsp;artisan&nbsp;migrate&nbsp;--force</code> en el hosting.
    </div>
    @endif

    {{-- Indicadores del rango filtrado: también funcionan como filtros rápidos --}}
    <div class="cs-kpis">
        <button type="button" class="cs-kpi on" data-kpi="" style="--c:var(--so-brand)">
            <div class="k">Conceptos vigentes <i class="fas fa-file-medical"></i></div>
            <div class="v" id="kVig">—</div>
            <div class="bar"><span style="width:100%"></span></div>
        </button>
        <button type="button" class="cs-kpi" data-kpi="ok" style="--c:var(--so-ok)">
            <div class="k">Aptos <i class="fas fa-check-circle"></i></div>
            <div class="v" id="kOk">—</div>
            <div class="bar"><span id="bOk" style="width:0"></span></div>
        </button>
        <button type="button" class="cs-kpi" data-kpi="warn" style="--c:var(--so-warn)">
            <div class="k">Con restricciones <i class="fas fa-exclamation-triangle"></i></div>
            <div class="v" id="kWarn">—</div>
            <div class="bar"><span id="bWarn" style="width:0"></span></div>
        </button>
        <button type="button" class="cs-kpi" data-kpi="bad" style="--c:var(--so-bad)">
            <div class="k">No aptos <i class="fas fa-times-circle"></i></div>
            <div class="v" id="kBad">—</div>
            <div class="bar"><span id="bBad" style="width:0"></span></div>
        </button>
        <button type="button" class="cs-kpi" data-kpi="anulados" style="--c:#8a8fa3">
            <div class="k">Anulados <i class="fas fa-ban"></i></div>
            <div class="v" id="kAnu">—</div>
            <div class="bar"><span id="bAnu" style="width:0"></span></div>
        </button>
    </div>

    {{-- Filtros --}}
    <div class="so-card cs-filtros">
        <div class="cs-frow">
            <div class="cs-search">
                <label class="so-lbl" for="fQ">Buscar</label>
                <i class="fas fa-search" style="top:auto;bottom:14px;transform:none;"></i>
                <input type="search" id="fQ" class="so-in" autocomplete="off"
                       placeholder="Nombre, número de identificación o palabras clave (diagnóstico, cargo, recomendaciones…)">
                <kbd style="top:auto;bottom:11px;transform:none;" title="Atajo de teclado">/</kbd>
            </div>
            <div>
                <label class="so-lbl" for="fDesde">Fecha inicial</label>
                <input type="date" id="fDesde" class="so-in">
            </div>
            <div>
                <label class="so-lbl" for="fHasta">Fecha final</label>
                <input type="date" id="fHasta" class="so-in">
            </div>
            <div>
                <label class="so-lbl" for="fTipo">Tipo de evaluación</label>
                <select id="fTipo" class="so-in">
                    <option value="">Todos</option>
                    @foreach($tipos as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="so-lbl" for="fEstado">Estado</label>
                <select id="fEstado" class="so-in">
                    <option value="vigentes">Vigentes</option>
                    <option value="anulados">Anulados</option>
                    <option value="todos">Todos</option>
                </select>
            </div>
        </div>
        <div class="cs-rapidos">
            <span class="t"><i class="far fa-clock"></i> Periodo</span>
            <button type="button" class="cs-chip" data-rango="hoy">Hoy</button>
            <button type="button" class="cs-chip" data-rango="7">Últimos 7 días</button>
            <button type="button" class="cs-chip" data-rango="30">Últimos 30 días</button>
            <button type="button" class="cs-chip" data-rango="mes">Este mes</button>
            <button type="button" class="cs-chip" data-rango="anio">Este año</button>
            <button type="button" class="cs-chip on" data-rango="todo">Todo</button>
            <button type="button" class="cs-chip limpiar" id="btnLimpiar"><i class="fas fa-eraser"></i> Limpiar filtros</button>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="so-card">
        <div class="cs-bar">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <div class="info" id="info">Cargando conceptos…</div>
                <div class="dropdown cs-exp" data-permiso="salud.consulta-sst.exportar">
                    <button type="button" class="so-btn xls" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Exporta con los filtros aplicados">
                        <i class="fas fa-file-excel"></i> Exportar a Excel <i class="fas fa-caret-down" style="opacity:.8"></i>
                    </button>
                    <div class="dropdown-menu">
                        <h6 class="dropdown-header">Se exporta con los filtros aplicados</h6>
                        <button type="button" class="dropdown-item" data-exportar="todo"><i class="fas fa-layer-group"></i><span>Información y estadísticas<small>Listado de conceptos + indicadores y distribuciones</small></span></button>
                        <button type="button" class="dropdown-item" data-exportar="estadisticas"><i class="fas fa-chart-pie"></i><span>Solo estadísticas<small>Indicadores y distribuciones con barras</small></span></button>
                        <button type="button" class="dropdown-item" data-exportar="listado"><i class="fas fa-table"></i><span>Solo información<small>Listado detallado de los conceptos</small></span></button>
                    </div>
                </div>
            </div>
            <div class="cs-sel" id="barSel">
                <span><i class="fas fa-check-circle" style="color:var(--so-brand)"></i> Seleccionado: <strong id="selTxt"></strong></span>
                <button type="button" class="so-btn ghost" id="selVer" data-permiso="salud.consulta-sst.show"><i class="fas fa-eye"></i> Ver</button>
                <button type="button" class="so-btn prim" id="selImp" data-permiso="salud.consulta-sst.imprimir"><i class="fas fa-print"></i> Imprimir seleccionado</button>
            </div>
        </div>
        <div class="cs-table-wrap">
            <table class="cs-table">
                <thead>
                    <tr>
                        <th style="width:34px;"></th>
                        <th data-orden="id">N.º <i class="fas fa-sort s"></i></th>
                        <th data-orden="fecha" class="on">Fecha <i class="fas fa-sort-down s"></i></th>
                        <th data-orden="paciente">Paciente <i class="fas fa-sort s"></i></th>
                        <th data-orden="tipo">Evaluación <i class="fas fa-sort s"></i></th>
                        <th data-orden="concepto">Concepto emitido <i class="fas fa-sort s"></i></th>
                        <th data-orden="medico">Médico <i class="fas fa-sort s"></i></th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody"></tbody>
            </table>
        </div>
        <div class="cs-foot">
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--so-mut);">
                Mostrar
                <select id="porPagina" class="so-in">
                    <option>10</option><option>25</option><option>50</option><option>100</option>
                </select>
                por página
            </div>
            <div class="cs-pag" id="pag"></div>
        </div>
    </div>
</div>

{{-- Panel lateral: ver concepto --}}
<div class="cs-ov" id="ov"></div>
<aside class="cs-drawer" id="drawer" aria-hidden="true">
    <div class="cs-dh">
        <div class="top">
            <span class="num" id="dNum"></span>
            <button type="button" class="x" id="dCerrar" title="Cerrar (Esc)"><i class="fas fa-times"></i></button>
        </div>
        <h5 id="dNombre"></h5>
        <div class="sub" id="dSub"></div>
        <div class="acc" id="dAcc"></div>
    </div>
    <div class="cs-db" id="dBody"></div>
</aside>

{{-- Modal: corregir concepto --}}
<div class="modal fade cs-modal" id="mEditar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <form class="modal-content" id="formEditar" autocomplete="off">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" style="font-weight:800;"><i class="fas fa-pen"></i> Corregir concepto <span id="eNum"></span></h5>
                    <div style="font-size:13px;opacity:.85;" id="ePac"></div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="cs-aviso">
                    <i class="fas fa-shield-alt" style="margin-top:2px;"></i>
                    <div>Solo se corrigen los datos del concepto. La historia clínica de la consulta no se modifica.
                        Cada corrección queda registrada con su motivo, el usuario, la fecha y el valor anterior.</div>
                </div>

                <div class="sec">
                    <div class="cs-g2">
                        <div>
                            <label class="so-lbl" for="eTipo">Tipo de evaluación</label>
                            <select id="eTipo" name="tipo_atencion" class="so-in" required>
                                @foreach($tipos as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="so-lbl">Énfasis de la consulta</label>
                            <div class="so-in" id="eEnfasis" style="background:var(--so-soft);min-height:42px;"></div>
                        </div>
                    </div>
                    <label class="so-lbl" style="margin-top:14px;">Concepto emitido</label>
                    <div class="cs-ops" id="eOps"></div>
                    <div id="eEnfOps"></div>
                </div>

                <div class="sec">
                    <label class="so-lbl" for="eRec">Recomendaciones médicas y compromisos</label>
                    <textarea id="eRec" name="recomendaciones" class="so-in" rows="3"></textarea>
                    <div class="cs-g2" style="margin-top:12px;">
                        <div>
                            <label class="so-lbl" for="eRes">Restricciones médicas ocupacionales</label>
                            <textarea id="eRes" name="restricciones" class="so-in" rows="3"></textarea>
                        </div>
                        <div>
                            <label class="so-lbl" for="eTemp">Temporalidad de las restricciones</label>
                            <select id="eTemp" name="restricciones_temporalidad" class="so-in">
                                <option value="">Sin temporalidad</option>
                                @foreach($temporalidades as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <label class="so-lbl" for="eSst" style="margin-top:12px;">Recomendaciones para el área de SST</label>
                    <textarea id="eSst" name="sst" class="so-in" rows="3"></textarea>
                    <label class="so-lbl" for="eObs" style="margin-top:12px;">Observaciones del concepto</label>
                    <textarea id="eObs" name="observaciones_concepto" class="so-in" rows="2"></textarea>
                </div>

                <div class="sec" style="border-color:#f0dcb4;">
                    <label class="so-lbl" for="eMotivo" style="color:#92611a;">Motivo de la corrección <span style="color:var(--so-bad)">*</span></label>
                    <textarea id="eMotivo" name="motivo" class="so-in" rows="2" required minlength="10" maxlength="500"
                              placeholder="Ej.: Se corrige la temporalidad de las restricciones según valoración del especialista."></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border:none;">
                <button type="button" class="so-btn ghost" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="so-btn prim" id="eGuardar"><i class="fas fa-save"></i> Guardar corrección</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    'use strict';
    var URL_DATOS = @json(route('salud.consulta-sst.data'));
    var URL_BASE = @json(url('salud-ocupacional/consulta-sst'));
    var URL_EXPORTAR = @json(route('salud.consulta-sst.exportar'));
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;
    var TIPOS = @json($tipos);
    var CONCEPTOS = @json($conceptos);
    var GRUPOS = @json($conceptosGrupos);
    var ENF_CONCEPTOS = @json($conceptosEnfasis);
    var ENFASIS = @json($enfasis);
    var TEMPORALIDADES = @json($temporalidades);
    var CAMPOS = { tipo_atencion: 'Tipo de evaluación', concepto_resultado: 'Concepto emitido', concepto_enfasis: 'Concepto por énfasis',
        observaciones_concepto: 'Observaciones', recomendaciones: 'Recomendaciones', restricciones: 'Restricciones',
        restricciones_temporalidad: 'Temporalidad', sst: 'Recomendaciones SST' };
    var ICON = { ok: 'fa-check-circle', warn: 'fa-exclamation-triangle', bad: 'fa-times-circle', none: 'fa-minus-circle', anul: 'fa-ban' };
    var COLORES = ['#2e3a75', '#4657a8', '#2f6fb0', '#6d4bb8', '#2e9e6b', '#c98a1e', '#b0487a', '#1f8a8a'];

    var S = { q: '', desde: '', hasta: '', tipo: '', estado: 'vigentes', resultado: '', orden: 'fecha', dir: 'desc', pagina: 1, por_pagina: 10 };
    var filas = [], seleccion = null, peticion = 0, actual = null;
    try { S.por_pagina = parseInt(localStorage.getItem('cs.porPagina'), 10) || 10; } catch (e) {}

    var $ = function (s, c) { return (c || document).querySelector(s); };
    var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
    var esc = function (v) { return String(v == null ? '' : v).replace(/[&<>"']/g, function (m) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]; }); };
    var vacio = function (v) { return v == null || String(v).trim() === ''; };
    var toast = function (icon, title) { Swal.fire({ icon: icon, title: title, timer: 2200, showConfirmButton: false, toast: true, position: 'top-end' }); };
    var norm = function (t) { return String(t).normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase(); };

    /** Resalta en el texto las palabras buscadas (sin distinguir tildes ni mayúsculas). */
    function hl(texto) {
        var t = esc(texto);
        var palabras = S.q.trim().split(/\s+/).filter(function (p) { return p.length > 1; });
        if (!palabras.length) return t;
        var plano = norm(t), marcas = [];
        palabras.forEach(function (p) {
            var np = norm(esc(p)), i = plano.indexOf(np);
            while (np && i !== -1) { marcas.push([i, i + np.length]); i = plano.indexOf(np, i + np.length); }
        });
        if (!marcas.length) return t;
        marcas.sort(function (a, b) { return a[0] - b[0]; });
        var out = '', pos = 0;
        marcas.forEach(function (m) {
            if (m[0] < pos) return;
            out += t.slice(pos, m[0]) + '<mark class="cs-hl">' + t.slice(m[0], m[1]) + '</mark>';
            pos = m[1];
        });
        return out + t.slice(pos);
    }

    function iniciales(n) {
        var p = String(n || '').trim().split(/\s+/);
        return ((p[0] || '')[0] || '?').toUpperCase() + ((p[1] || '')[0] || '').toUpperCase();
    }
    function color(n) { var h = 0; String(n).split('').forEach(function (c) { h = (h * 31 + c.charCodeAt(0)) >>> 0; }); return COLORES[h % COLORES.length]; }

    // ─── Carga de datos ───
    function cargar() {
        var id = ++peticion;
        esqueleto();
        var params = new URLSearchParams();
        Object.keys(S).forEach(function (k) { if (S[k] !== '' && S[k] != null) params.set(k, S[k]); });
        fetch(URL_DATOS + '?' + params.toString(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json().then(function (j) { if (!r.ok) throw new Error(j.message || 'Error al consultar'); return j; }); })
            .then(function (j) {
                if (id !== peticion) return;
                filas = j.items; S.pagina = j.pagina;
                pintarTabla(); pintarPaginacion(j.paginas); pintarResumen(j.resumen);
                $('#info').innerHTML = j.total
                    ? 'Mostrando <strong>' + ((j.pagina - 1) * S.por_pagina + 1) + '–' + ((j.pagina - 1) * S.por_pagina + j.items.length) + '</strong> de <strong>' + j.total.toLocaleString('es-CO') + '</strong> conceptos'
                    : 'Sin resultados';
            })
            .catch(function (e) {
                if (id !== peticion) return;
                $('#tbody').innerHTML = '<tr><td colspan="8"><div class="cs-vacio"><i class="fas fa-plug"></i>' + esc(e.message) + '</div></td></tr>';
            });
    }

    function esqueleto() {
        if (filas.length) { $('#tbody').style.opacity = .55; return; }
        var h = '';
        for (var i = 0; i < 6; i++) h += '<tr class="cs-skel">' + '<td><span></span></td>'.repeat(8) + '</tr>';
        $('#tbody').innerHTML = h;
    }

    function pintarTabla() {
        var tb = $('#tbody');
        tb.style.opacity = 1;
        if (!filas.length) {
            tb.innerHTML = '<tr><td colspan="8"><div class="cs-vacio"><i class="fas fa-folder-open"></i>'
                + '<strong>No se encontraron conceptos</strong><br>Ajuste la búsqueda o el rango de fechas.</div></td></tr>';
            return;
        }
        tb.innerHTML = filas.map(function (f) {
            var clase = f.anulado ? 'anul' : (f.clase || 'none');
            var mini = [];
            f.conceptos_enfasis.forEach(function (e) { mini.push('<span title="' + esc(e.titulo) + '">' + esc(e.etiqueta) + '</span>'); });
            if (f.restricciones_hasta) mini.push('<span class="vig"><i class="far fa-clock"></i> Restricciones hasta ' + esc(f.restricciones_hasta) + '</span>');
            if (f.restricciones_indefinidas) mini.push('<span class="vig"><i class="fas fa-infinity"></i> Restricciones indefinidas</span>');
            if (f.correcciones) mini.push('<span title="Corregido"><i class="fas fa-history"></i> ' + f.correcciones + ' corrección' + (f.correcciones > 1 ? 'es' : '') + '</span>');
            if (f.documentos) mini.push('<span><i class="fas fa-paperclip"></i> ' + f.documentos + '</span>');
            return '<tr data-id="' + f.id + '" class="' + (f.anulado ? 'anulado ' : '') + (seleccion === f.id ? 'sel' : '') + '">'
                + '<td><span class="cs-radio" title="Seleccionar"></span></td>'
                + '<td class="cs-num">' + hl(f.numero) + '</td>'
                + '<td class="cs-fecha">' + esc(f.fecha || '—') + (f.hora ? '<small>' + esc(f.hora) + '</small>' : '') + '</td>'
                + '<td><div class="cs-pac"><div class="cs-av" style="background:' + color(f.paciente) + '">' + esc(iniciales(f.paciente)) + '</div>'
                +   '<div><div class="n">' + hl(f.paciente) + '</div><div class="s"><i class="far fa-id-card"></i> ' + hl(f.identificacion || '—')
                +   (f.cargo ? ' · ' + hl(f.cargo) : '') + '</div></div></div></td>'
                + '<td><span class="cs-tipo">' + esc(f.tipo) + '</span>' + (f.enfasis ? '<span class="cs-enf" title="' + esc(f.enfasis) + '">' + esc(f.enfasis) + '</span>' : '') + '</td>'
                + '<td><span class="cs-pill ' + clase + '"><i class="fas ' + ICON[clase] + '"></i><span>' + (f.anulado ? 'Anulado · ' : '') + hl(f.concepto) + '</span></span>'
                +   (mini.length ? '<div class="cs-mini">' + mini.join('') + '</div>' : '') + '</td>'
                + '<td class="cs-med">' + hl(f.medico || '—') + '</td>'
                + '<td><div class="cs-acts">'
                +   '<button type="button" class="cs-act ver" data-acc="ver" title="Ver" data-permiso="salud.consulta-sst.show"><i class="fas fa-eye"></i></button>'
                +   '<button type="button" class="cs-act edi" data-acc="editar" title="' + (f.anulado ? 'Anulado: no se puede editar' : 'Editar (corregir)') + '" data-permiso="salud.consulta-sst.show salud.consulta-sst.corregir"' + (f.anulado ? ' disabled' : '') + '><i class="fas fa-pen"></i></button>'
                +   '<button type="button" class="cs-act imp" data-acc="imprimir" title="Imprimir certificado" data-permiso="salud.consulta-sst.imprimir"><i class="fas fa-print"></i></button>'
                +   '<button type="button" class="cs-act eli" data-acc="eliminar" title="' + (f.anulado ? 'Ya está anulado' : 'Eliminar (anular)') + '" data-permiso="salud.consulta-sst.anular"' + (f.anulado ? ' disabled' : '') + '><i class="fas fa-trash-alt"></i></button>'
                + '</div></td></tr>';
        }).join('');
        actualizarSeleccion();
    }

    function pintarPaginacion(total) {
        var p = S.pagina, h = '';
        var btn = function (n, txt, dis, on) { return '<button type="button" data-pag="' + n + '"' + (dis ? ' disabled' : '') + (on ? ' class="on"' : '') + '>' + txt + '</button>'; };
        h += btn(p - 1, '<i class="fas fa-chevron-left"></i>', p <= 1);
        var paginas = [];
        for (var i = 1; i <= total; i++) if (i === 1 || i === total || Math.abs(i - p) <= 1) paginas.push(i);
        var prev = 0;
        paginas.forEach(function (n) { if (n - prev > 1) h += '<span class="dots">…</span>'; h += btn(n, n, false, n === p); prev = n; });
        h += btn(p + 1, '<i class="fas fa-chevron-right"></i>', p >= total);
        $('#pag').innerHTML = h;
    }

    function pintarResumen(r) {
        if (!r) return;
        var n = function (v) { return (v || 0).toLocaleString('es-CO'); };
        var pct = function (v) { return r.vigentes ? Math.round((v / r.vigentes) * 100) + '%' : '0'; };
        $('#kVig').textContent = n(r.vigentes);
        $('#kOk').textContent = n(r.ok); $('#bOk').style.width = pct(r.ok);
        $('#kWarn').textContent = n(r.warn); $('#bWarn').style.width = pct(r.warn);
        $('#kBad').textContent = n(r.bad); $('#bBad').style.width = pct(r.bad);
        $('#kAnu').textContent = n(r.anulados);
        $('#bAnu').style.width = (r.vigentes + r.anulados) ? Math.round((r.anulados / (r.vigentes + r.anulados)) * 100) + '%' : '0';
    }

    function marcarKpi() {
        var k = S.estado === 'anulados' ? 'anulados' : S.resultado;
        $$('.cs-kpi').forEach(function (b) { b.classList.toggle('on', b.dataset.kpi === k); });
    }

    // ─── Selección de un concepto (para imprimir / ver) ───
    function actualizarSeleccion() {
        var f = filas.find(function (x) { return x.id === seleccion; });
        $$('#tbody tr[data-id]').forEach(function (tr) { tr.classList.toggle('sel', +tr.dataset.id === seleccion); });
        $('#barSel').classList.toggle('on', !!f);
        if (f) $('#selTxt').textContent = 'N.º ' + f.numero + ' · ' + f.paciente;
    }

    // ─── Filtros ───
    var tBuscar;
    $('#fQ').addEventListener('input', function () {
        clearTimeout(tBuscar);
        tBuscar = setTimeout(function () { S.q = $('#fQ').value.trim(); S.pagina = 1; cargar(); }, 320);
    });
    function fechaIso(d) { return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); }
    function marcarRango(r) { $$('[data-rango]').forEach(function (b) { b.classList.toggle('on', b.dataset.rango === r); }); }
    $$('[data-rango]').forEach(function (b) {
        b.addEventListener('click', function () {
            var hoy = new Date(), d = '', h = fechaIso(hoy), r = b.dataset.rango;
            if (r === 'hoy') d = h;
            else if (r === '7' || r === '30') { var x = new Date(); x.setDate(x.getDate() - (+r - 1)); d = fechaIso(x); }
            else if (r === 'mes') d = fechaIso(new Date(hoy.getFullYear(), hoy.getMonth(), 1));
            else if (r === 'anio') d = hoy.getFullYear() + '-01-01';
            else h = '';
            $('#fDesde').value = S.desde = d; $('#fHasta').value = S.hasta = h;
            marcarRango(r); S.pagina = 1; cargar();
        });
    });
    ['#fDesde', '#fHasta'].forEach(function (sel) {
        $(sel).addEventListener('change', function () {
            S.desde = $('#fDesde').value; S.hasta = $('#fHasta').value;
            if (S.desde && S.hasta && S.desde > S.hasta) {
                Swal.fire({ icon: 'warning', title: 'Rango de fechas inválido', text: 'La fecha inicial no puede ser posterior a la fecha final.' });
                $(sel).value = ''; S[sel === '#fDesde' ? 'desde' : 'hasta'] = '';
            }
            marcarRango(S.desde || S.hasta ? '' : 'todo'); S.pagina = 1; cargar();
        });
    });
    $('#fTipo').addEventListener('change', function () { S.tipo = this.value; S.pagina = 1; cargar(); });
    $('#fEstado').addEventListener('change', function () { S.estado = this.value; if (S.estado === 'anulados') S.resultado = ''; marcarKpi(); S.pagina = 1; cargar(); });
    $$('.cs-kpi').forEach(function (b) {
        b.addEventListener('click', function () {
            var k = b.dataset.kpi;
            if (k === 'anulados') { S.estado = 'anulados'; S.resultado = ''; }
            else { S.estado = 'vigentes'; S.resultado = k; }
            $('#fEstado').value = S.estado; marcarKpi(); S.pagina = 1; cargar();
        });
    });
    $('#btnLimpiar').addEventListener('click', function () {
        $('#fQ').value = $('#fDesde').value = $('#fHasta').value = $('#fTipo').value = '';
        $('#fEstado').value = 'vigentes';
        S.q = S.desde = S.hasta = S.tipo = S.resultado = ''; S.estado = 'vigentes'; S.pagina = 1;
        marcarRango('todo'); marcarKpi(); cargar();
    });
    $('#porPagina').value = String(S.por_pagina);
    $('#porPagina').addEventListener('change', function () {
        S.por_pagina = +this.value; S.pagina = 1;
        try { localStorage.setItem('cs.porPagina', this.value); } catch (e) {}
        cargar();
    });
    $('#pag').addEventListener('click', function (e) {
        var b = e.target.closest('button[data-pag]');
        if (b && !b.disabled) { S.pagina = +b.dataset.pag; cargar(); }
    });
    $$('th[data-orden]').forEach(function (th) {
        th.addEventListener('click', function () {
            var o = th.dataset.orden;
            S.dir = S.orden === o && S.dir === 'desc' ? 'asc' : 'desc';
            S.orden = o;
            $$('th[data-orden]').forEach(function (x) {
                x.classList.toggle('on', x === th);
                x.querySelector('.s').className = 'fas s ' + (x === th ? (S.dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort');
            });
            cargar();
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === '/' && !/input|textarea|select/i.test(document.activeElement.tagName)) { e.preventDefault(); $('#fQ').focus(); }
        if (e.key === 'Escape') cerrarPanel();
    });

    // ─── Acciones de la tabla ───
    $('#tbody').addEventListener('click', function (e) {
        var tr = e.target.closest('tr[data-id]');
        if (!tr) return;
        var id = +tr.dataset.id, b = e.target.closest('button[data-acc]');
        if (!b) { seleccion = seleccion === id ? null : id; actualizarSeleccion(); return; }
        if (b.disabled) return;
        seleccion = id; actualizarSeleccion();
        ({ ver: ver, editar: editar, imprimir: imprimir, eliminar: anular })[b.dataset.acc](id);
    });
    $('#tbody').addEventListener('dblclick', function (e) {
        var tr = e.target.closest('tr[data-id]');
        if (tr && !e.target.closest('button') && CABI.puede('salud.consulta-sst.show')) ver(+tr.dataset.id);
    });
    $('#selImp').addEventListener('click', function () { if (seleccion) imprimir(seleccion); });

    // ─── Exportar a Excel (con los mismos filtros de la tabla) ───
    $$('[data-exportar]').forEach(function (b) {
        b.addEventListener('click', function () {
            var params = new URLSearchParams({ contenido: b.dataset.exportar });
            ['q', 'desde', 'hasta', 'tipo', 'estado', 'resultado', 'orden', 'dir'].forEach(function (k) { if (S[k]) params.set(k, S[k]); });
            var boton = $('.cs-exp .so-btn');
            boton.classList.add('cargando');
            boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando Excel…';
            fetch(URL_EXPORTAR + '?' + params.toString(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) {
                    var tipo = r.headers.get('Content-Type') || '';
                    if (!r.ok || tipo.indexOf('spreadsheetml') === -1) {
                        return r.json().catch(function () { return {}; }).then(function (j) {
                            throw new Error(j.message || 'El servidor no pudo generar el archivo (código ' + r.status + ').');
                        });
                    }
                    var nombre = ((r.headers.get('Content-Disposition') || '').match(/filename="?([^";]+)"?/) || [])[1] || 'consulta_sst.xlsx';
                    return r.blob().then(function (b) {
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(b);
                        a.download = nombre;
                        document.body.appendChild(a); a.click(); a.remove();
                        setTimeout(function () { URL.revokeObjectURL(a.href); }, 4000);
                        toast('success', 'Archivo de Excel descargado');
                    });
                })
                .catch(function (err) { Swal.fire({ icon: 'error', title: 'No se pudo exportar', text: err.message }); })
                .finally(function () {
                    boton.classList.remove('cargando');
                    boton.innerHTML = '<i class="fas fa-file-excel"></i> Exportar a Excel <i class="fas fa-caret-down" style="opacity:.8"></i>';
                });
        });
    });
    $('#selVer').addEventListener('click', function () { if (seleccion) ver(seleccion); });

    function fila(id) { return filas.find(function (f) { return f.id === id; }); }

    function obtener(id) {
        return fetch(URL_BASE + '/' + id, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json().then(function (j) { if (!r.ok) throw new Error(j.message || 'No fue posible cargar el concepto.'); return j; }); });
    }

    // Imprime el certificado desde un marco oculto (sin el diseño del aplicativo)
    function imprimir(id) {
        var f = fila(id);
        if (f && f.anulado) {
            Swal.fire({ icon: 'warning', title: 'Concepto anulado', text: 'El certificado se imprimirá con la marca de agua ANULADO.', showCancelButton: true,
                confirmButtonText: 'Imprimir de todas formas', cancelButtonText: 'Cancelar', confirmButtonColor: '#2e3a75' })
                .then(function (r) { if (r.isConfirmed) imprimirMarco(URL_BASE + '/' + id + '/imprimir'); });
            return;
        }
        imprimirMarco(URL_BASE + '/' + id + '/imprimir');
    }
    function imprimirMarco(url) {
        var previo = document.getElementById('marcoCertificado');
        if (previo) previo.remove();
        var marco = document.createElement('iframe');
        marco.id = 'marcoCertificado';
        marco.setAttribute('aria-hidden', 'true');
        marco.setAttribute('tabindex', '-1');
        marco.style.cssText = 'position:fixed;left:-10000px;top:0;width:816px;height:1056px;border:0;';
        toast('info', 'Preparando el certificado…');
        marco.onload = function () {
            var titulo = document.title;
            try {
                if (!marco.contentDocument || !marco.contentDocument.querySelector('.doc')) throw new Error('sin documento');
                document.title = marco.contentDocument.title || titulo;
                marco.contentWindow.focus();
                marco.contentWindow.print();
            } catch (e) {
                window.open(url + '?imprimir=1', '_blank');
            }
            setTimeout(function () { document.title = titulo; }, 500);
        };
        marco.src = url;
        document.body.appendChild(marco);
    }

    // ─── Ver (panel lateral) ───
    function abrirPanel() { $('#ov').classList.add('on'); $('#drawer').classList.add('on'); $('#drawer').setAttribute('aria-hidden', 'false'); }
    function cerrarPanel() { $('#ov').classList.remove('on'); $('#drawer').classList.remove('on'); $('#drawer').setAttribute('aria-hidden', 'true'); }
    $('#ov').addEventListener('click', cerrarPanel);
    $('#dCerrar').addEventListener('click', cerrarPanel);

    function txt(v) { return vacio(v) ? '<div class="cs-txt vac">No registra.</div>' : '<div class="cs-txt">' + esc(v) + '</div>'; }
    function valorCampo(campo, v) {
        if (vacio(v) || (typeof v === 'object' && !Object.keys(v).length)) return '—';
        if (campo === 'tipo_atencion') return TIPOS[v] || v;
        if (campo === 'concepto_resultado') return CONCEPTOS[v] || v;
        if (campo === 'restricciones_temporalidad') return TEMPORALIDADES[v] || v;
        if (campo === 'concepto_enfasis') return Object.keys(v).map(function (k) {
            var c = ENF_CONCEPTOS[k]; return c ? c.titulo + ': ' + ((c.opciones[v[k]] || [v[k]])[0]) : k;
        }).join(' · ');
        return String(v);
    }

    function ver(id) {
        abrirPanel();
        $('#dNum').textContent = 'Cargando…'; $('#dNombre').textContent = ''; $('#dSub').textContent = ''; $('#dAcc').innerHTML = '';
        $('#dBody').innerHTML = '<div class="cs-vacio"><i class="fas fa-spinner fa-spin"></i>Cargando concepto…</div>';
        obtener(id).then(function (d) {
            actual = d;
            var f = d.fila, c = d.concepto, clase = f.anulado ? 'none' : (f.clase || 'none');
            $('#dNum').textContent = 'CERTIFICADO N.º ' + f.numero;
            $('#dNombre').textContent = d.paciente.nombre || '—';
            $('#dSub').textContent = 'Id. ' + (d.paciente.identificacion || '—') + ' · ' + f.tipo + ' · ' + (f.fecha || '') + (f.hora ? ' ' + f.hora : '');
            $('#dAcc').innerHTML =
                (f.anulado ? '' : '<button type="button" data-d="editar" data-permiso="salud.consulta-sst.corregir"><i class="fas fa-pen"></i> Editar</button>')
                + '<button type="button" data-d="imprimir" data-permiso="salud.consulta-sst.imprimir"><i class="fas fa-print"></i> Imprimir</button>'
                + '<a href="' + esc(f.url_completa) + '" data-permiso="salud.concepto.show"><i class="fas fa-external-link-alt"></i> Ver completo</a>'
                + (f.anulado ? '' : '<button type="button" data-d="eliminar" data-permiso="salud.consulta-sst.anular"><i class="fas fa-trash-alt"></i> Eliminar</button>');

            var h = '';
            if (d.anulacion) {
                h += '<div class="cs-anul"><i class="fas fa-ban" style="margin-top:3px;"></i><div><strong>Concepto anulado</strong> el ' + esc(d.anulacion.fecha)
                    + ' por ' + esc(d.anulacion.usuario) + '.<br><em>' + esc(d.anulacion.motivo) + '</em></div></div>';
            }
            h += '<div class="cs-box"><h6><i class="fas fa-stethoscope"></i> Concepto emitido</h6>'
                + '<div class="cs-big ' + clase + '"><i class="fas ' + ICON[clase] + '"></i><span>' + esc(f.concepto) + '</span></div>';
            f.conceptos_enfasis.forEach(function (e) {
                h += '<div style="margin-top:8px;font-size:13.5px;"><span class="cs-pill ' + e.clase + '"><i class="fas ' + ICON[e.clase] + '"></i><span>' + esc(e.titulo) + ': ' + esc(e.etiqueta) + '</span></span></div>';
            });
            if (f.enfasis) h += '<div style="margin-top:10px;font-size:12.5px;color:var(--so-mut);"><strong>Énfasis:</strong> ' + esc(f.enfasis) + '</div>';
            h += '</div>';

            h += '<div class="cs-box"><h6><i class="fas fa-user"></i> Paciente y datos laborales</h6><div class="cs-kv">'
                + '<div><div class="k">Edad</div><div class="v">' + esc(d.paciente.edad || '—') + '</div></div>'
                + '<div><div class="k">Género</div><div class="v">' + esc(d.paciente.genero || '—') + '</div></div>';
            Object.keys(d.laborales).forEach(function (k) { h += '<div><div class="k">' + esc(k) + '</div><div class="v">' + esc(d.laborales[k] || '—') + '</div></div>'; });
            h += '</div></div>';

            h += '<div class="cs-box"><h6><i class="fas fa-notes-medical"></i> Recomendaciones médicas y compromisos</h6>' + txt(c.recomendaciones) + '</div>';
            h += '<div class="cs-box"><h6><i class="fas fa-hand-paper"></i> Restricciones médicas ocupacionales</h6>' + txt(c.restricciones)
                + (c.temporalidad_label ? '<div style="margin-top:8px;font-size:13px;"><span class="cs-pill warn"><i class="far fa-clock"></i><span>' + esc(c.temporalidad_label)
                + (c.restricciones_hasta ? ' · vigentes hasta el ' + esc(c.restricciones_hasta) : '') + '</span></span></div>' : '') + '</div>';
            h += '<div class="cs-box"><h6><i class="fas fa-hard-hat"></i> Recomendaciones para el área de SST</h6>' + txt(c.sst) + '</div>';
            if (!vacio(c.observaciones_concepto)) h += '<div class="cs-box"><h6><i class="far fa-comment-dots"></i> Observaciones del concepto</h6>' + txt(c.observaciones_concepto) + '</div>';
            if (!vacio(c.diagnostico)) h += '<div class="cs-box"><h6><i class="fas fa-file-prescription"></i> Diagnóstico</h6>' + txt(c.diagnostico) + '</div>';

            h += '<div class="cs-box"><h6><i class="fas fa-user-md"></i> Médico</h6><div class="cs-kv">'
                + '<div><div class="k">Médico</div><div class="v">' + esc(d.medico.nombre || '—') + '</div></div>'
                + '<div><div class="k">Registro</div><div class="v">' + esc(d.medico.registro || '—') + '</div></div>'
                + '<div><div class="k">Registrado por</div><div class="v">' + esc(d.medico.registrado || '—') + '</div></div>'
                + '<div><div class="k">Fecha de registro</div><div class="v">' + esc(d.medico.creado || '—') + '</div></div></div></div>';

            if (d.documentos.length) {
                h += '<div class="cs-box cs-docs"><h6><i class="fas fa-paperclip"></i> Documentos adjuntos</h6>'
                    + d.documentos.map(function (x) {
                        return '<a href="' + esc(x.url) + '" target="_blank" rel="noopener" data-permiso="salud.concepto.documento"><i class="far fa-file-alt"></i> ' + esc(x.nombre)
                            + (x.tamano ? ' <small style="color:var(--so-mut)">· ' + esc(x.tamano) + '</small>' : '') + '</a>';
                    }).join('') + '</div>';
            }

            if (d.correcciones.length) {
                h += '<div class="cs-box"><h6><i class="fas fa-history"></i> Historial de correcciones</h6><div class="cs-tl">'
                    + d.correcciones.map(function (k) {
                        return '<div class="it"><div class="h"><strong>' + esc(k.usuario) + '</strong> · ' + esc(k.fecha) + '</div>'
                            + '<div class="m">“' + esc(k.motivo) + '”</div><div class="cs-dif">'
                            + Object.keys(k.cambios).map(function (campo) {
                                var ch = k.cambios[campo];
                                return '<div><b>' + esc(CAMPOS[campo] || campo) + '</b><span><del>' + esc(valorCampo(campo, ch.antes)) + '</del><br><ins>' + esc(valorCampo(campo, ch.despues)) + '</ins></span></div>';
                            }).join('') + '</div></div>';
                    }).join('') + '</div></div>';
            }
            $('#dBody').innerHTML = h;
        }).catch(function (e) {
            $('#dBody').innerHTML = '<div class="cs-vacio"><i class="fas fa-exclamation-circle"></i>' + esc(e.message) + '</div>';
        });
    }
    $('#dAcc').addEventListener('click', function (e) {
        var b = e.target.closest('button[data-d]');
        if (!b || !actual) return;
        var id = actual.fila.id;
        if (b.dataset.d === 'imprimir') imprimir(id);
        if (b.dataset.d === 'editar') { cerrarPanel(); editar(id); }
        if (b.dataset.d === 'eliminar') { cerrarPanel(); anular(id); }
    });

    // ─── Editar (corregir) ───
    var editando = null;
    function pintarOpciones(tipo, seleccionado) {
        var grupo = (tipo === 'egreso' || tipo === 'brigada') ? tipo : 'general';
        var ops = GRUPOS[grupo];
        $('#eOps').innerHTML = Object.keys(ops).map(function (k) {
            var o = ops[k];
            return '<label class="cs-op ' + o[2] + '"><input type="radio" name="concepto_resultado" value="' + esc(k) + '"' + (k === seleccionado ? ' checked' : '') + ' required>'
                + '<span>' + esc(o[0]) + '<small>' + esc(o[1]) + '</small></span></label>';
        }).join('');
    }
    function editar(id) {
        obtener(id).then(function (d) {
            if (d.anulacion) { Swal.fire({ icon: 'info', title: 'Concepto anulado', text: 'Un concepto anulado no se puede corregir.' }); return; }
            editando = d;
            var c = d.concepto;
            $('#eNum').textContent = 'N.º ' + d.fila.numero;
            $('#ePac').textContent = (d.paciente.nombre || '') + ' · Id. ' + (d.paciente.identificacion || '—') + ' · ' + (d.fila.fecha || '');
            $('#eTipo').value = c.tipo_atencion || 'ingreso';
            pintarOpciones($('#eTipo').value, c.concepto_resultado);
            $('#eEnfasis').innerHTML = c.enfasis.length
                ? c.enfasis.map(function (k) { return '<span class="cs-tipo" style="margin:0 4px 4px 0;">' + esc(ENFASIS[k] || k) + '</span>'; }).join('')
                : '<span style="color:var(--so-mut)">Sin énfasis</span>';
            // Concepto por énfasis (solo para los énfasis de la consulta que lo tienen)
            $('#eEnfOps').innerHTML = c.enfasis.filter(function (k) { return ENF_CONCEPTOS[k]; }).map(function (k) {
                var cfg = ENF_CONCEPTOS[k], sel = c.concepto_enfasis[k] || '';
                return '<label class="so-lbl" style="margin-top:14px;">' + esc(cfg.titulo) + '</label><select class="so-in" data-enf="' + esc(k) + '"><option value="">Sin concepto</option>'
                    + Object.keys(cfg.opciones).map(function (o) { return '<option value="' + esc(o) + '"' + (o === sel ? ' selected' : '') + '>' + esc(cfg.opciones[o][0]) + '</option>'; }).join('')
                    + '</select>';
            }).join('');
            $('#eRec').value = c.recomendaciones || '';
            $('#eRes').value = c.restricciones || '';
            $('#eTemp').value = c.restricciones_temporalidad || '';
            $('#eSst').value = c.sst || '';
            $('#eObs').value = c.observaciones_concepto || '';
            $('#eMotivo').value = '';
            window.jQuery('#mEditar').modal('show');
        }).catch(function (e) { Swal.fire({ icon: 'error', title: 'No se pudo abrir', text: e.message }); });
    }
    $('#eTipo').addEventListener('change', function () {
        var marcado = $('#eOps input:checked');
        pintarOpciones(this.value, marcado ? marcado.value : null);
    });
    $('#formEditar').addEventListener('submit', function (e) {
        e.preventDefault();
        if (!editando) return;
        var marcado = $('#eOps input:checked');
        if (!marcado) { Swal.fire({ icon: 'warning', title: 'Seleccione el concepto emitido' }); return; }
        if ($('#eMotivo').value.trim().length < 10) { Swal.fire({ icon: 'warning', title: 'Falta el motivo', text: 'Describa el motivo de la corrección (mínimo 10 caracteres).' }); $('#eMotivo').focus(); return; }
        var enf = {};
        $$('#eEnfOps select[data-enf]').forEach(function (s) { if (s.value) enf[s.dataset.enf] = s.value; });
        var body = {
            tipo_atencion: $('#eTipo').value, concepto_resultado: marcado.value, concepto_enfasis: enf,
            recomendaciones: $('#eRec').value, restricciones: $('#eRes').value, restricciones_temporalidad: $('#eTemp').value,
            sst: $('#eSst').value, observaciones_concepto: $('#eObs').value, motivo: $('#eMotivo').value
        };
        var btn = $('#eGuardar'); btn.disabled = true;
        enviar('PUT', URL_BASE + '/' + editando.fila.id, body).then(function (j) {
            window.jQuery('#mEditar').modal('hide');
            toast('success', j.message); cargar();
        }).catch(function (err) {
            Swal.fire({ icon: 'error', title: 'No se guardó la corrección', text: err.message });
        }).finally(function () { btn.disabled = false; });
    });

    // ─── Eliminar (anular) ───
    function anular(id) {
        var f = fila(id) || (actual && actual.fila);
        Swal.fire({
            icon: 'warning',
            title: '¿Eliminar el concepto N.º ' + (f ? f.numero : id) + '?',
            html: '<div style="font-size:14px;text-align:left;">' + (f ? '<strong>' + esc(f.paciente) + '</strong> · ' + esc(f.fecha) + ' · ' + esc(f.concepto) + '<br><br>' : '')
                + 'El concepto quedará <strong>anulado</strong>: deja de estar vigente pero se conserva en la base de datos '
                + 'como registro clínico, con quién lo anuló, cuándo y el motivo.</div>',
            input: 'textarea',
            inputLabel: 'Motivo de la anulación',
            inputPlaceholder: 'Ej.: Concepto registrado a un paciente equivocado.',
            inputAttributes: { maxlength: 500 },
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Anular concepto',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#c4453b',
            focusCancel: true,
            showLoaderOnConfirm: true,
            inputValidator: function (v) { if (!v || v.trim().length < 10) return 'Describa el motivo (mínimo 10 caracteres).'; },
            preConfirm: function (motivo) {
                return enviar('DELETE', URL_BASE + '/' + id, { motivo: motivo })
                    .catch(function (err) { Swal.showValidationMessage(err.message); });
            },
            allowOutsideClick: function () { return !Swal.isLoading(); }
        }).then(function (r) {
            if (r.isConfirmed && r.value) { toast('success', r.value.message); if (seleccion === id) seleccion = null; cargar(); }
        });
    }

    function enviar(metodo, url, body) {
        return fetch(url, {
            method: metodo,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body)
        }).then(function (r) {
            return r.json().catch(function () { return {}; }).then(function (j) {
                if (!r.ok || j.ok === false) {
                    var msg = j.message || 'No fue posible completar la acción.';
                    if (j.errors) msg = Object.values(j.errors).map(function (x) { return x[0]; }).join(' ');
                    throw new Error(msg);
                }
                return j;
            });
        });
    }

    cargar();
})();
</script>
@endpush
