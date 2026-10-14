@extends('layouts.app')

@section('title', 'Certificado concepto medico ' . ($c->identificacion ?: $c->id) . ' ' . optional($c->fecha_atencion)->format('Y-m-d'))
@section('header', 'Concepto Médico Ocupacional')

@php
    $dash = fn($v) => ($v!==null && trim((string)$v)!=='') ? $v : '—';
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
    // Historia clínica de la consulta: no hace parte del certificado
    $hayHistoria = ($c->factores_riesgo && count($c->factores_riesgo)) || $c->motivo_consulta || $c->estado_salud
        || collect($filas)->contains(fn ($f) => count($f[0])) || array_filter((array) $sv) || $c->diagnostico
        || $c->observaciones_concepto || $c->documentos->count();
@endphp

@push('head')
@include('salud_ocupacional.concepto._certificado_estilos')
<style>
    :root{ --so-brand:#2e3a75; --so-mut:#7c82a0; --so-line:#e1e5f2; }
    .toolbar{ max-width:215.9mm;margin:0 auto 14px;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap; }
    .b{ display:inline-flex;align-items:center;gap:8px;padding:11px 20px;border-radius:11px;font-weight:700;font-size:14.5px;cursor:pointer;border:none;text-decoration:none; }
    .b.prim{ background:var(--so-brand);color:#fff; } .b.ghost{ background:#fff;color:#4a4f66;border:1.5px solid #d5daea; }
    .b:disabled{ opacity:.7;cursor:wait; }
    /* Vista previa: el certificado como hoja de papel */
    .papel .doc{ box-shadow:0 12px 32px -14px rgba(20,30,70,.35);border:1px solid #dfe3ee; }
    /* Historia clínica (solo en pantalla) */
    .hist{ max-width:215.9mm;margin:18px auto 0;background:#fff;border:1px solid var(--so-line);border-radius:14px;overflow:hidden; }
    .hist summary{ cursor:pointer;padding:15px 22px;font-weight:700;color:var(--so-brand);display:flex;align-items:center;gap:9px;list-style:none; }
    .hist summary::-webkit-details-marker{ display:none; }
    .hist summary .nota{ font-weight:500;font-size:13px;color:var(--so-mut); }
    .hist summary .fa-chevron-down{ margin-left:auto;transition:transform .2s; }
    .hist[open] summary{ border-bottom:1px solid var(--so-line); }
    .hist[open] summary .fa-chevron-down{ transform:rotate(180deg); }
    .hist-b{ padding:22px; }
    .sec{ font-family:'Courier New',monospace;font-size:11px;letter-spacing:.11em;text-transform:uppercase;color:var(--so-brand);font-weight:700;background:#eef0fb;padding:9px 13px;border-radius:8px;margin:20px 0 12px; }
    .kv{ display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px 22px; }
    .kv .k, .k{ font-size:10.5px;color:#9297ae;text-transform:uppercase;letter-spacing:.05em; }
    .kv .v{ font-size:14.5px;font-weight:600;color:#1f2440; }
    .tbl{ width:100%;border-collapse:collapse;font-size:13.5px; }
    .tbl th{ text-align:left;font-size:10.5px;color:#9297ae;text-transform:uppercase;letter-spacing:.05em;font-weight:600;padding:6px 8px;border-bottom:1.5px solid var(--so-line); }
    .tbl td{ padding:7px 8px;border-bottom:1px solid var(--so-line);color:#1f2440;vertical-align:top;white-space:pre-wrap; }
    .blk{ font-size:14px;color:#3a3e56;line-height:1.5;white-space:pre-wrap; }
    @media print{
        /* Respaldo si se usa Ctrl+P en esta página: solo sale el documento, sin el diseño del aplicativo */
        @page{ size:letter;margin:0; }
        html,body,.wrapper,.content-wrapper,.content,.content > .container-fluid{
            height:auto !important;min-height:0 !important;overflow:visible !important;position:static !important;
            margin:0 !important;padding:0 !important;background:#fff !important; }
        .main-header,.main-sidebar,.main-footer,.content-header,.toolbar,.no-print,.alert{ display:none !important; }
        .papel .doc{ max-width:none;min-height:0;box-shadow:none;border:none; }
    }
</style>
@endpush

@section('content')
<div class="toolbar">
    <a href="{{ route('salud.concepto.index') }}" class="b ghost" data-permiso="salud.concepto.index salud.concepto.store"><i class="fas fa-plus"></i> Nuevo concepto</a>
    <button type="button" id="btnImprimirCertificado" class="b prim" data-url="{{ route('salud.concepto.show', ['concepto' => $c->id, 'documento' => 1]) }}"
            title="Abre la impresión solo del certificado. Para obtener el PDF elija «Guardar como PDF».">
        <i class="fas fa-file-pdf"></i> Imprimir / Guardar PDF
    </button>
</div>

{{-- Certificado tal como se imprime --}}
<div class="papel">
    @include('salud_ocupacional.concepto._certificado')
</div>

{{-- Historia clínica de la consulta: registro del médico. No hace parte del certificado ni se imprime. --}}
@if($hayHistoria)
<details class="hist no-print">
    <summary>
        <i class="fas fa-notes-medical"></i> Historia clínica de esta consulta
        <span class="nota">· no hace parte del certificado ni del PDF</span>
        <i class="fas fa-chevron-down"></i>
    </summary>
    <div class="hist-b">
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
        <div class="sec">Documentos adjuntos</div>
        <ul style="font-size:14px;color:#3a3e56;">
            @foreach($c->documentos as $doc)
            <li><a href="{{ $doc->url }}" target="_blank" rel="noopener">{{ $doc->nombre_original }}</a>
                <span style="color:var(--so-mut);font-size:12.5px;">@if($doc->created_at) · cargado el {{ $doc->created_at->format('d/m/Y h:i A') }}@endif @if($doc->tamano) · {{ number_format($doc->tamano / 1048576, 1) }} MB @endif</span></li>
            @endforeach
        </ul>
        @endif
    </div>
</details>
@endif
@endsection

@push('scripts')
<script>
// Imprime el documento independiente del certificado (sin el diseño del aplicativo) desde un marco oculto.
(function () {
    var boton = document.getElementById('btnImprimirCertificado');
    if (!boton) return;
    boton.addEventListener('click', function () {
        var url = boton.getAttribute('data-url');
        var previo = document.getElementById('marcoCertificado');
        if (previo) previo.remove();
        var marco = document.createElement('iframe');
        marco.id = 'marcoCertificado';
        marco.setAttribute('aria-hidden', 'true');
        marco.setAttribute('tabindex', '-1');
        marco.style.cssText = 'position:fixed;left:-10000px;top:0;width:816px;height:1056px;border:0;';
        boton.disabled = true;
        marco.onload = function () {
            var tituloPagina = document.title;
            try {
                // Nombre sugerido del PDF: el título del documento (sin "CABI")
                document.title = marco.contentDocument.title || tituloPagina;
                marco.contentWindow.focus();
                marco.contentWindow.print();
            } catch (e) {
                window.open(url + '&imprimir=1', '_blank');   // respaldo: el documento en otra pestaña
            }
            setTimeout(function () { document.title = tituloPagina; boton.disabled = false; }, 500);
        };
        marco.src = url;
        document.body.appendChild(marco);
    });
})();
</script>
@endpush
