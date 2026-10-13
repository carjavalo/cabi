{{--
    Certificado de Concepto Médico Ocupacional (documento que se entrega).
    Contiene únicamente: identificación del paciente, datos laborales y de afiliación, concepto emitido,
    recomendaciones médicas y compromisos, restricciones médicas ocupacionales (con su temporalidad),
    recomendaciones para el área de SST y firma del médico. No incluye la historia clínica.
--}}
@php
    $valor = fn($v) => ($v !== null && trim((string) $v) !== '') ? $v : '—';
    $colorConcepto = ['ok' => '#2e9e6b', 'warn' => '#c98a1e', 'bad' => '#c4453b'][$c->concepto_clase] ?? '#2e3a75';
    $genero = ['F' => 'Femenino', 'M' => 'Masculino', 'O' => 'Otro'][$c->genero ?? ''] ?? $valor($c->genero);
    $colgroup = '<colgroup><col style="width:17%"><col style="width:16.33%"><col style="width:17%"><col style="width:16.33%"><col style="width:17%"><col style="width:16.34%"></colgroup>';
@endphp
<article class="doc">
    <header class="doc-h">
        <img class="doc-logo" src="{{ asset('img/huv-h.png') }}" alt="Hospital Universitario del Valle Evaristo García E.S.E.">
        <div class="doc-h-der">
            <div class="doc-titulo">Certificado de Concepto<br>Médico Ocupacional</div>
            <table class="doc-meta">
                <tr><th>Certificado N.º</th><td>{{ str_pad((string) $c->id, 6, '0', STR_PAD_LEFT) }}</td></tr>
                <tr><th>Fecha de evaluación</th><td>{{ $valor(optional($c->fecha_atencion)->format('d/m/Y')) }}</td></tr>
                <tr><th>Tipo de evaluación</th><td>{{ $valor($c->tipo_label) }}</td></tr>
            </table>
        </div>
    </header>

    <section class="doc-sec">
        <h2>Identificación del paciente</h2>
        <table class="doc-tabla">
            {!! $colgroup !!}
            <tr><th>Nombre</th><td colspan="5">{{ $valor($c->paciente_nombre ?: optional($c->user)->name) }}</td></tr>
            <tr><th>Identificación</th><td>{{ $valor($c->identificacion) }}</td><th>Edad</th><td>{{ $valor($c->edad) }}</td><th>Género</th><td>{{ $genero }}</td></tr>
        </table>
    </section>

    <section class="doc-sec">
        <h2>Datos laborales y de afiliación</h2>
        <table class="doc-tabla">
            {!! $colgroup !!}
            <tr><th>Cargo</th><td colspan="2">{{ $valor($c->cargo_inicio) }}</td><th>Servicio</th><td colspan="2">{{ $valor($c->servicio) }}</td></tr>
            <tr><th>EPS</th><td>{{ $valor($c->eps) }}</td><th>AFP</th><td>{{ $valor($c->afp) }}</td><th>ARL</th><td>{{ $valor($c->arl) }}</td></tr>
            <tr><th>Empleador</th><td colspan="3">{{ $valor($c->empleador) }}</td><th>NIT</th><td>{{ $valor($c->nit) }}</td></tr>
        </table>
    </section>

    <section class="doc-sec">
        <h2>Concepto emitido</h2>
        <div class="doc-concepto" style="border-left-color:{{ $colorConcepto }};">
            <div class="principal">{{ $c->concepto_label }}</div>
            @foreach($c->conceptos_enfasis_lista as [$titulo, $etiqueta, $clase])
            <div class="enfasis"><strong>{{ $titulo }}:</strong> {{ $etiqueta }}</div>
            @endforeach
        </div>
    </section>

    @foreach(['recomendaciones' => 'Recomendaciones médicas y compromisos', 'restricciones' => 'Restricciones médicas ocupacionales', 'sst' => 'Recomendaciones para el área de SST'] as $campo => $titulo)
    <section class="doc-sec">
        <h2>{{ $titulo }}</h2>
        @php $texto = trim((string) $c->{$campo}); @endphp
        <div class="doc-texto {{ $texto === '' ? 'vacio' : '' }}">{{ $texto !== '' ? $texto : 'No registra.' }}</div>
        @if($campo === 'restricciones' && $c->temporalidad_label)
        <div class="doc-temp"><strong>Temporalidad:</strong> {{ $c->temporalidad_label }}@if($c->restricciones_hasta) · vigentes hasta el {{ $c->restricciones_hasta->format('d/m/Y') }}@endif</div>
        @endif
    </section>
    @endforeach

    <section class="doc-sec">
        <h2>Firma del médico</h2>
        <div class="doc-firma">
            @if($c->firma && strlen($c->firma) > 200)
            <img src="{{ $c->firma }}" alt="Firma del médico">
            @else
            <div class="espacio"></div>
            @endif
            <div class="linea">
                <div class="nombre">{{ $valor($c->medico) }}</div>
                <div class="cargo">{{ $valor($c->registro) }}</div>
            </div>
        </div>
    </section>
</article>
