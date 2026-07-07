@extends('layouts.app')

@section('title', 'Certificado · Concepto Médico')
@section('header', 'Concepto Médico Ocupacional')

@php
    $dash = fn($v) => ($v!==null && trim((string)$v)!=='') ? $v : '—';
    $sv = $c->signos_vitales ?? [];
    $ex = $c->examen_sistemas ?? [];
    $ap = $c->antecedentes_personales ?? [];
    $hb = $c->habitos ?? [];
    $ao = $c->antecedentes_ocupacionales ?? [];
    $al = $c->accidentes_laborales ?? [];
    $el = $c->enfermedad_laboral ?? [];
    $conceptoClase = ['apto'=>'#2e9e6b','apto_restricciones'=>'#c98a1e','con_restricciones'=>'#c4453b'][$c->concepto_resultado] ?? '#2e3a75';
@endphp

@push('head')
<style>
    :root{ --so-brand:#2e3a75; --so-mut:#7c82a0; --so-line:#e1e5f2; }
    .cert{ max-width:920px;margin:0 auto;background:#fff;border:1px solid var(--so-line);border-radius:14px;overflow:hidden;
        box-shadow:0 16px 34px -26px rgba(46,58,117,.4); }
    .cert-h{ display:flex;align-items:center;gap:14px;background:var(--so-brand);color:#fff;padding:20px 24px; }
    .cert-h .lg{ width:44px;height:44px;border-radius:11px;background:rgba(255,255,255,.16);display:flex;align-items:center;justify-content:center;font-weight:700; }
    .cert-b{ padding:24px; }
    .sec{ font-family:'Courier New',monospace;font-size:11px;letter-spacing:.11em;text-transform:uppercase;color:var(--so-brand);font-weight:700;background:#eef0fb;padding:9px 13px;border-radius:8px;margin:20px 0 12px; }
    .kv{ display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px 22px; }
    .kv .k{ font-size:10.5px;color:#9297ae;text-transform:uppercase;letter-spacing:.05em; }
    .kv .v{ font-size:14.5px;font-weight:600;color:#1f2440; }
    .blk{ font-size:14px;color:#3a3e56;line-height:1.5;white-space:pre-wrap; }
    .badge-c{ background:#eef0fb;border:1px solid #d6dbf0;border-radius:12px;padding:15px 18px;margin:14px 0; }
    .toolbar{ max-width:920px;margin:0 auto 14px;display:flex;gap:10px;justify-content:flex-end; }
    .b{ display:inline-flex;align-items:center;gap:8px;padding:11px 20px;border-radius:11px;font-weight:700;font-size:14.5px;cursor:pointer;border:none;text-decoration:none; }
    .b.prim{ background:var(--so-brand);color:#fff; } .b.ghost{ background:#fff;color:#4a4f66;border:1.5px solid #d5daea; }
    @media print{
        .main-header,.main-sidebar,.main-footer,.content-header,.toolbar{ display:none !important; }
        .content-wrapper{ margin:0 !important;background:#fff !important;padding:0 !important; }
        .cert{ border:none;box-shadow:none;border-radius:0;max-width:none; }
        @page{ margin:14mm; }
    }
</style>
@endpush

@section('content')
<div class="toolbar">
    <a href="{{ route('salud.concepto.index') }}" class="b ghost"><i class="fas fa-plus"></i> Nuevo concepto</a>
    <button onclick="window.print()" class="b prim"><i class="fas fa-print"></i> Imprimir / Guardar PDF</button>
</div>

<div class="cert">
    <div class="cert-h">
        <div class="lg">HUV</div>
        <div style="flex:1;">
            <div style="font-size:10.5px;letter-spacing:.12em;text-transform:uppercase;opacity:.8;">Hospital Universitario del Valle · Evaristo García E.S.E</div>
            <div style="font-size:18px;font-weight:800;">Concepto Médico Ocupacional</div>
        </div>
        <div style="text-align:right;font-size:12px;opacity:.9;">
            {{ optional($c->fecha_atencion)->format('d/m/Y') }} · {{ $c->hora_atencion ? \Carbon\Carbon::parse($c->hora_atencion)->format('h:i A') : '' }}<br>
            <span style="opacity:.75;">{{ $c->tipo_label }} · {{ $dash($c->lugar_atencion) }}</span>
        </div>
    </div>
    <div class="cert-b">

        <div class="sec" style="margin-top:0;">Identificación del paciente</div>
        <div class="kv">
            <div><div class="k">Nombre</div><div class="v">{{ $dash($c->paciente_nombre ?: optional($c->user)->name) }}</div></div>
            <div><div class="k">Identificación</div><div class="v">{{ $dash($c->identificacion) }}</div></div>
            <div><div class="k">Edad</div><div class="v">{{ $dash($c->edad) }}</div></div>
            <div><div class="k">Género</div><div class="v">{{ ['F'=>'Femenino','M'=>'Masculino','O'=>'Otro'][$c->genero ?? ''] ?? $dash($c->genero) }}</div></div>
        </div>

        <div class="sec">Datos laborales y de afiliación</div>
        <div class="kv">
            <div><div class="k">Cargo</div><div class="v">{{ $dash($c->cargo_inicio) }}</div></div>
            <div><div class="k">Servicio</div><div class="v">{{ $dash($c->servicio) }}</div></div>
            <div><div class="k">EPS</div><div class="v">{{ $dash($c->eps) }}</div></div>
            <div><div class="k">AFP</div><div class="v">{{ $dash($c->afp) }}</div></div>
            <div><div class="k">ARL</div><div class="v">{{ $dash($c->arl) }}</div></div>
            <div style="grid-column:1/-1;"><div class="k">Empleador</div><div class="v">{{ $dash($c->empleador) }} · NIT {{ $dash($c->nit) }}</div></div>
        </div>

        @if($c->factores_riesgo && count($c->factores_riesgo))
        <div class="sec">Factores de riesgo ocupacional</div>
        <div class="blk">{{ implode(' · ', $c->factores_riesgo) }}</div>
        @endif

        @if($c->motivo_consulta || $c->estado_salud)
        <div class="sec">Estado de salud</div>
        @if($c->motivo_consulta)<div style="margin-bottom:6px;"><span class="k">Motivo de consulta:</span> <span class="v">{{ $c->motivo_consulta }}</span></div>@endif
        <div class="blk">{{ $dash($c->estado_salud) }}</div>
        @endif

        @if(array_filter((array)$sv))
        <div class="sec">Examen físico · signos vitales</div>
        <div class="kv">
            @foreach(['peso'=>'Peso (kg)','talla'=>'Talla (cm)','imc'=>'IMC','pa'=>'P/A','fc'=>'FC','fr'=>'FR','temp'=>'T°','dominancia'=>'Dominancia','clasificacion'=>'Clasificación'] as $k=>$lb)
                @if(!empty($sv[$k]))<div><div class="k">{{ $lb }}</div><div class="v">{{ $sv[$k] }}</div></div>@endif
            @endforeach
        </div>
        @endif

        @if($c->diagnostico)
        <div class="sec">Diagnóstico</div>
        <div class="blk">{{ $c->diagnostico }}</div>
        @endif

        <div class="badge-c">
            <div style="font-size:10.5px;color:var(--so-brand);text-transform:uppercase;letter-spacing:.1em;font-weight:700;margin-bottom:4px;">Concepto emitido</div>
            <div style="font-size:20px;font-weight:800;color:{{ $conceptoClase }};">{{ $c->concepto_label }}</div>
        </div>

        @foreach(['recomendaciones'=>'Recomendaciones médicas y compromisos','restricciones'=>'Restricciones médicas ocupacionales','sst'=>'Recomendaciones para el área de SST'] as $k=>$lb)
            @if($c->{$k})
            <div style="margin-bottom:12px;"><div class="k" style="font-size:10.5px;color:#9297ae;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">{{ $lb }}</div><div class="blk">{{ $c->{$k} }}</div></div>
            @endif
        @endforeach

        @if($c->documentos->count())
        <div class="sec">Documentos de la EPS adjuntos</div>
        <ul style="font-size:14px;color:#3a3e56;">
            @foreach($c->documentos as $doc)
            <li><a href="{{ $doc->url }}" target="_blank">{{ $doc->nombre_original }}</a></li>
            @endforeach
        </ul>
        @endif

        <div style="border-top:1px solid var(--so-line);margin-top:22px;padding-top:18px;">
            @if($c->firma && strlen($c->firma) > 200)
            <img src="{{ $c->firma }}" alt="Firma" style="height:70px;object-fit:contain;display:block;margin-bottom:2px;">
            @endif
            <div style="width:280px;border-top:1.5px solid #1f2440;padding-top:6px;">
                <div style="font-weight:700;">{{ $dash($c->medico) }}</div>
                <div style="font-size:12.5px;color:var(--so-mut);">{{ $dash($c->registro) }}</div>
            </div>
        </div>

    </div>
</div>
@endsection
