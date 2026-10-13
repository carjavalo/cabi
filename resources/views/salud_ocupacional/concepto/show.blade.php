@extends('layouts.app')

{{-- El título es el nombre sugerido al guardar el PDF desde la ventana de impresión --}}
@section('title', 'Certificado concepto medico ' . ($c->identificacion ?: $c->id) . ' ' . optional($c->fecha_atencion)->format('Y-m-d'))
@section('header', 'Concepto Médico Ocupacional')

@php
    $dash = fn($v) => ($v!==null && trim((string)$v)!=='') ? $v : '—';
    $noRegistra = fn($v) => ($v!==null && trim((string)$v)!=='') ? $v : 'No registra.';
    $sv = $c->signos_vitales ?? [];
    // Secciones con varias filas (las consultas antiguas tienen un solo registro)
    $filas = [
        'Antecedentes ocupacionales' => [\App\Models\ConceptoMedico::filas($c->antecedentes_ocupacionales),
            ['empresa'=>'Empresa','cargo'=>'Cargo','tiempo'=>'Tiempo de exposición','tarea'=>'Descripción de la tarea']],
        'Accidentes laborales' => [\App\Models\ConceptoMedico::filas($c->accidentes_laborales),
            ['empresa'=>'Empresa','cargo'=>'Cargo','fecha'=>'Fecha del evento','descripcion'=>'Descripción']],
        'Enfermedad laboral' => [\App\Models\ConceptoMedico::filas($c->enfermedad_laboral),
            ['empresa'=>'Empresa','cargo'=>'Cargo','fecha'=>'Fecha del evento','descripcion'=>'Descripción']],
    ];
    $colores = ['ok'=>'#2e9e6b','warn'=>'#c98a1e','bad'=>'#c4453b'];
    $conceptoClase = $colores[$c->concepto_clase] ?? '#2e3a75';
    // Historia clínica de la consulta: no hace parte del certificado
    $hayHistoria = ($c->factores_riesgo && count($c->factores_riesgo)) || $c->motivo_consulta || $c->estado_salud
        || collect($filas)->contains(fn ($f) => count($f[0])) || array_filter((array) $sv) || $c->diagnostico
        || $c->observaciones_concepto || $c->documentos->count();
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
    .kv .k, .k{ font-size:10.5px;color:#9297ae;text-transform:uppercase;letter-spacing:.05em; }
    .kv .v{ font-size:14.5px;font-weight:600;color:#1f2440; }
    .tbl{ width:100%;border-collapse:collapse;font-size:13.5px; }
    .tbl th{ text-align:left;font-size:10.5px;color:#9297ae;text-transform:uppercase;letter-spacing:.05em;font-weight:600;padding:6px 8px;border-bottom:1.5px solid var(--so-line); }
    .tbl td{ padding:7px 8px;border-bottom:1px solid var(--so-line);color:#1f2440;vertical-align:top;white-space:pre-wrap; }
    .blk{ font-size:14px;color:#3a3e56;line-height:1.5;white-space:pre-wrap; }
    .badge-c{ background:#eef0fb;border:1px solid #d6dbf0;border-radius:12px;padding:15px 18px;margin:0 0 10px; }
    .temp{ margin-top:8px;font-size:13.5px;color:#3a3e56; }
    .temp .k{ font-weight:700; }
    .firma{ padding-top:6px; }
    .toolbar{ max-width:920px;margin:0 auto 14px;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap; }
    .b{ display:inline-flex;align-items:center;gap:8px;padding:11px 20px;border-radius:11px;font-weight:700;font-size:14.5px;cursor:pointer;border:none;text-decoration:none; }
    .b.prim{ background:var(--so-brand);color:#fff; } .b.ghost{ background:#fff;color:#4a4f66;border:1.5px solid #d5daea; }
    .hist{ max-width:920px;margin:18px auto 0;background:#fff;border:1px solid var(--so-line);border-radius:14px;overflow:hidden; }
    .hist summary{ cursor:pointer;padding:15px 22px;font-weight:700;color:var(--so-brand);display:flex;align-items:center;gap:9px;list-style:none; }
    .hist summary::-webkit-details-marker{ display:none; }
    .hist summary .nota{ font-weight:500;font-size:13px;color:var(--so-mut); }
    .hist summary .fa-chevron-down{ margin-left:auto;transition:transform .2s; }
    .hist[open] summary{ border-bottom:1px solid var(--so-line); }
    .hist[open] summary .fa-chevron-down{ transform:rotate(180deg); }
    @media print{
        /* Solo se imprime el certificado (la historia clínica nunca sale en el PDF) */
        .main-header,.main-sidebar,.main-footer,.content-header,.toolbar,.no-print,.alert{ display:none !important; }
        html,body,.wrapper,.content-wrapper{ background:#fff !important; }
        .content-wrapper,.content,.content > .container-fluid{ margin:0 !important;padding:0 !important;min-height:0 !important; }
        .cert{ border:none;box-shadow:none;border-radius:0;max-width:none; }
        .cert-h,.sec,.badge-c{ -webkit-print-color-adjust:exact;print-color-adjust:exact; }
        .cert-b{ padding:16px 14mm 14mm; }
        .sec{ margin:14px 0 9px;break-after:avoid; }
        .badge-c,.firma,.kv{ break-inside:avoid; }
        /* Sin márgenes de página: el navegador no agrega encabezados/pies (URL, fecha) al PDF */
        @page{ size:letter;margin:0; }
    }
</style>
@endpush

@section('content')
<div class="toolbar">
    <a href="{{ route('salud.concepto.index') }}" class="b ghost" data-permiso="salud.concepto.index salud.concepto.store"><i class="fas fa-plus"></i> Nuevo concepto</a>
    <button type="button" onclick="window.print()" class="b prim" title="En la ventana de impresión elija «Guardar como PDF»"><i class="fas fa-file-pdf"></i> Imprimir / Guardar PDF</button>
</div>

{{-- Certificado: únicamente la información que se entrega (sin historia clínica) --}}
<div class="cert" id="certificado">
    <div class="cert-h">
        <div class="lg">HUV</div>
        <div style="flex:1;">
            <div style="font-size:10.5px;letter-spacing:.12em;text-transform:uppercase;opacity:.8;">Hospital Universitario del Valle · Evaristo García E.S.E</div>
            <div style="font-size:18px;font-weight:800;">Certificado de Concepto Médico Ocupacional</div>
        </div>
        <div style="text-align:right;font-size:12px;opacity:.9;">
            {{ optional($c->fecha_atencion)->format('d/m/Y') }}<br>
            <span style="opacity:.8;">Evaluación: {{ $c->tipo_label }}</span>
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

        <div class="sec">Concepto emitido</div>
        <div class="badge-c">
            <div style="font-size:20px;font-weight:800;color:{{ $conceptoClase }};">{{ $c->concepto_label }}</div>
        </div>
        @foreach($c->conceptos_enfasis_lista as [$titulo, $lb, $clase])
        <div class="badge-c">
            <div style="font-size:10.5px;color:var(--so-brand);text-transform:uppercase;letter-spacing:.1em;font-weight:700;margin-bottom:4px;">{{ $titulo }}</div>
            <div style="font-size:17px;font-weight:800;color:{{ $colores[$clase] ?? '#2e3a75' }};">{{ $lb }}</div>
        </div>
        @endforeach

        <div class="sec">Recomendaciones médicas y compromisos</div>
        <div class="blk">{{ $noRegistra($c->recomendaciones) }}</div>

        <div class="sec">Restricciones médicas ocupacionales</div>
        <div class="blk">{{ $noRegistra($c->restricciones) }}</div>
        @if($c->temporalidad_label)
        <div class="temp"><span class="k">Temporalidad:</span> {{ $c->temporalidad_label }}@if($c->restricciones_hasta) · vigentes hasta el {{ $c->restricciones_hasta->format('d/m/Y') }}@endif</div>
        @endif

        <div class="sec">Recomendaciones para el área de SST</div>
        <div class="blk">{{ $noRegistra($c->sst) }}</div>

        <div class="sec">Firma del médico</div>
        <div class="firma">
            @if($c->firma && strlen($c->firma) > 200)
            <img src="{{ $c->firma }}" alt="Firma del médico" style="height:70px;max-width:280px;object-fit:contain;display:block;margin-bottom:2px;">
            @else
            <div style="height:56px;"></div>
            @endif
            <div style="width:280px;border-top:1.5px solid #1f2440;padding-top:6px;">
                <div style="font-weight:700;">{{ $dash($c->medico) }}</div>
                <div style="font-size:12.5px;color:var(--so-mut);">{{ $dash($c->registro) }}</div>
            </div>
        </div>

    </div>
</div>

{{-- Historia clínica de la consulta: registro del médico. No hace parte del certificado ni se imprime. --}}
@if($hayHistoria)
<details class="hist no-print">
    <summary>
        <i class="fas fa-notes-medical"></i> Historia clínica de esta consulta
        <span class="nota">· no hace parte del certificado ni del PDF</span>
        <i class="fas fa-chevron-down"></i>
    </summary>
    <div class="cert-b">
        @if($c->factores_riesgo && count($c->factores_riesgo))
        <div class="sec" style="margin-top:0;">Factores de riesgo ocupacional</div>
        <div class="blk">{{ implode(' · ', $c->factores_riesgo) }}</div>
        @endif

        @if($c->motivo_consulta || $c->estado_salud)
        <div class="sec">Estado de salud</div>
        @if($c->motivo_consulta)<div style="margin-bottom:6px;"><span class="k">Motivo de consulta:</span> <span class="v">{{ $c->motivo_consulta }}</span></div>@endif
        <div class="blk">{{ $dash($c->estado_salud) }}</div>
        @endif

        @foreach($filas as $titulo => [$lista, $cols])
            @if(count($lista))
            <div class="sec">{{ $titulo }}</div>
            <table class="tbl">
                <thead><tr>@foreach($cols as $lb)<th>{{ $lb }}</th>@endforeach</tr></thead>
                <tbody>
                @foreach($lista as $f)
                    <tr>@foreach($cols as $k => $lb)
                        <td>{{ $k==='fecha' && !empty($f[$k]) ? \Carbon\Carbon::parse($f[$k])->format('d/m/Y') : $dash($f[$k] ?? null) }}</td>
                    @endforeach</tr>
                @endforeach
                </tbody>
            </table>
            @endif
        @endforeach

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

        @if($c->observaciones_concepto)
        <div class="sec">Observaciones del concepto</div>
        <div class="blk">{{ $c->observaciones_concepto }}</div>
        @endif

        @if($c->documentos->count())
        <div class="sec">Documentos de la EPS adjuntos</div>
        <ul style="font-size:14px;color:#3a3e56;">
            @foreach($c->documentos as $doc)
            <li><a href="{{ $doc->url }}" target="_blank">{{ $doc->nombre_original }}</a></li>
            @endforeach
        </ul>
        @endif
    </div>
</details>
@endif
@endsection
