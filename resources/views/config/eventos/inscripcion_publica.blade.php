@extends('layouts.app')

@section('title', $evento->titulo . ' - Inscripción')
@section('header', 'Inscripción a Evento')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
<style>
    /* Corporate color utilities */
    .text-corp { color: var(--brand); }
    .bg-corp { background-color: var(--brand); }
    .border-corp { border-color: var(--brand); }

    .bg-corp-5 { background-color: rgba(46,58,117,0.05); }
    .bg-corp-10 { background-color: rgba(46,58,117,0.10); }
    .bg-corp-40 { background-color: rgba(46,58,117,0.40); }
    .border-corp-20 { border-color: rgba(46,58,117,0.20); }

    .btn-corp-custom { background-color: var(--brand); color: #fff; border: none; }
    .btn-corp-custom:hover { background-color: rgba(46,58,117,0.90); color: #fff; }

    /* Franja seleccionada */
    .franja-selected {
        border-color: var(--brand) !important;
        background-color: rgba(46,58,117,0.05) !important;
    }
    .franja-selected .franja-hora { color: var(--brand) !important; }
    .franja-selected .franja-bar-bg { background-color: #e2e8f0 !important; }
    .franja-selected .franja-bar-fill { background-color: var(--brand) !important; }

    /* Card de inscripción */
    .inscripcion-card {
        background: rgba(255,255,255,0.97);
        border-radius: 1rem;
        box-shadow: 0 4px 24px rgba(46,58,117,0.10);
        border: 1px solid rgba(46,58,117,0.08);
    }
    .inscripcion-header {
        background: linear-gradient(135deg, var(--brand) 0%, #3b4a8a 100%);
        color: #fff;
        border-radius: 1rem 1rem 0 0;
        padding: 1.5rem 2rem;
    }
    .inscripcion-body { padding: 2rem; }

    .seccion-titulo {
        font-size: 1rem;
        font-weight: 700;
        color: var(--brand);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid rgba(46,58,117,0.10);
        padding-bottom: 0.5rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .seccion-titulo .material-symbols-outlined { font-size: 1.3rem; max-width: none; max-height: none; }

    /* Franja cards */
    .franja-btn {
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
        width: 100%;
    }
    .franja-btn:hover { border-color: rgba(46,58,117,0.30); box-shadow: 0 4px 12px rgba(46,58,117,0.08); }
    .franja-hora { font-size: 1.15rem; font-weight: 800; color: #334155; }
    .franja-bar-bg { height: 6px; border-radius: 999px; background: #f1f5f9; overflow: hidden; margin-top: 0.75rem; }
    .franja-bar-fill { height: 100%; background: rgba(46,58,117,0.40); border-radius: 999px; }
    .franja-agotada { opacity: 0.5; cursor: not-allowed; background: #f8fafc; }

    .badge-seleccionado { background: var(--brand); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }
    .badge-agotado { background: #ef4444; color: #fff; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; text-transform: uppercase; }

    /* Form controls override */
    .inscripcion-card .form-control:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 0.15rem rgba(46,58,117,0.15);
    }

    /* Submit button */
    .btn-submit-inscripcion {
        background: linear-gradient(135deg, var(--brand) 0%, #3b4a8a 100%);
        color: #fff;
        font-weight: 700;
        font-size: 1rem;
        padding: 0.85rem 2rem;
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 8px 20px rgba(46,58,117,0.20);
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-width: 250px;
    }
    .btn-submit-inscripcion:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(46,58,117,0.28); color: #fff; }
    .btn-submit-inscripcion:active { transform: scale(0.98); }
    .btn-submit-inscripcion .material-symbols-outlined { font-size: 1.3rem; max-width: none; max-height: none; }
</style>
@endpush

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

{{-- ============ ALERTAS FLASH EN DOM (Ocultas si usaremos SweetAlert, pero de respaldo) ============ --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="flash-success" style="display:none;">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="flash-error" style="display:none;">
    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="flash-validation" style="display:none;">
    <strong><i class="fas fa-exclamation-triangle mr-1"></i> Por favor corrija los siguientes errores:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif

{{-- ============ CARD PRINCIPAL ============ --}}
<div class="inscripcion-card">


    {{-- Encabezado institucional --}}
    <div class="inscripcion-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h2 class="mb-1" style="font-size:1.5rem; font-weight:800;">
                    <span class="material-symbols-outlined mr-2" style="font-size:1.6rem; vertical-align:middle; max-width:none; max-height:none;">calendar_month</span>
                    {{ $evento->titulo }}
                </h2>
                <p class="mb-0" style="opacity:0.85; font-size:0.9rem;">
                    @if($evento->descripcion)
                        {{ $evento->descripcion }}
                    @else
                        Complete los siguientes campos para programar su sesión de entrenamiento.
                    @endif
                </p>
            </div>
            <div class="text-right mt-2 mt-md-0">
                @if($evento->ubicacion)
                <p class="mb-0" style="opacity:0.75; font-size:0.82rem;">
                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $evento->ubicacion }}
                </p>
                @endif
                <p class="mb-0" style="opacity:0.6; font-size:0.75rem;">
                    Vigente del {{ $evento->fecha_inicio->format('d/m/Y') }} al {{ $evento->fecha_fin->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Cuerpo del formulario --}}
    <div class="inscripcion-body">
        <form method="POST" action="{{ route('eventos.inscripcion.guardar', $evento->id) }}" id="inscripcionForm">
        @csrf

        {{-- ============ SECCIÓN 1: DATOS PERSONALES ============ --}}
        <div class="mb-5">
            <div class="seccion-titulo">
                <span class="material-symbols-outlined">person</span>
                Sección 1: Datos Personales
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold small text-uppercase text-muted" for="identificacion">Identificación</label>
                    <input id="identificacion" name="identificacion" value="{{ old('identificacion') }}" required
                        class="form-control" placeholder="C.C. / C.E." type="text"/>
                    <small id="identificacion-msg" class="form-text mt-1 font-weight-bold" style="display: none;"></small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold small text-uppercase text-muted" for="nombres">Nombres</label>
                    <input id="nombres" name="nombres" value="{{ old('nombres') }}" required readonly
                        class="form-control" placeholder="Sus nombres" type="text" style="background-color: #f8f9fa; cursor: not-allowed;"/>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold small text-uppercase text-muted" for="primer_apellido">Primer Apellido</label>
                    <input id="primer_apellido" name="primer_apellido" value="{{ old('primer_apellido') }}" required readonly
                        class="form-control" placeholder="Primer apellido" type="text" style="background-color: #f8f9fa; cursor: not-allowed;"/>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold small text-uppercase text-muted" for="segundo_apellido">Segundo Apellido</label>
                    <input id="segundo_apellido" name="segundo_apellido" value="{{ old('segundo_apellido') }}" readonly
                        class="form-control" placeholder="Segundo apellido" type="text" style="background-color: #f8f9fa; cursor: not-allowed;"/>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="font-weight-bold small text-uppercase text-muted" for="servicio">Servicio</label>
                    <select id="servicio" name="servicio" class="form-control" style="background-color: #f8f9fa; pointer-events: none;" onmousedown="return false;">
                        <option value="">Seleccione el servicio o unidad</option>
                        @if($servicios->count())
                            @foreach($servicios as $servicio)
                                <option value="{{ $servicio->nombre }}" {{ old('servicio') == $servicio->nombre ? 'selected' : '' }}>{{ $servicio->nombre }}</option>
                            @endforeach
                        @else
                            <option>Urgencias Adultos</option>
                            <option>Urgencias Pediátricas</option>
                            <option>Cuidado Intensivo (UCI)</option>
                            <option>Hospitalización</option>
                            <option>Cirugía</option>
                            <option>Administración</option>
                            <option>Apoyo Diagnóstico</option>
                        @endif
                    </select>
                    <!-- Agregamos un campo hidden para enviar el servicio al backend si el select está deshabilitado visualmente -->
                    <input type="hidden" name="servicio_hidden" id="servicio_hidden" value="{{ old('servicio') }}">
                </div>
            </div>
        </div>

        {{-- ============ INFO LÍMITES DE INSCRIPCIÓN ============ --}}
        @if($evento->max_inscripciones_dia > 0 || $evento->max_inscripciones_semana > 0)
        <div class="mb-4 p-3 rounded-lg" style="background: rgba(46,58,117,0.05); border: 1px solid rgba(46,58,117,0.15);">
            <div class="d-flex align-items-center mb-1">
                <span class="material-symbols-outlined mr-2" style="color: var(--brand); font-size:1.2rem; max-width:none; max-height:none;">info</span>
                <strong class="small text-uppercase" style="color: var(--brand);">Política de inscripción</strong>
            </div>
            <ul class="mb-0 small" style="color: #475569; padding-left: 1.5rem;">
                @if($evento->max_inscripciones_dia > 0)
                <li>Máximo <strong>{{ $evento->max_inscripciones_dia }}</strong> inscripción{{ $evento->max_inscripciones_dia > 1 ? 'es' : '' }} por día</li>
                @endif
                @if($evento->max_inscripciones_semana > 0)
                <li>Máximo <strong>{{ $evento->max_inscripciones_semana }}</strong> inscripción{{ $evento->max_inscripciones_semana > 1 ? 'es' : '' }} por semana</li>
                @endif
            </ul>
        </div>
        @endif

        {{-- ============ SECCIÓN 2: SELECCIÓN DE DÍA ============ --}}
        <div class="mb-5">
            <div class="seccion-titulo">
                <span class="material-symbols-outlined">today</span>
                Sección 2: Selección de Día
            </div>
            <div class="row">
                <div class="col-md-5">
                    <label class="font-weight-bold small text-uppercase text-muted" for="dia_semana">Día de entrenamiento</label>
                    <select id="dia_semana" name="dia_semana" required class="form-control font-weight-bold" style="border: 2px solid rgba(46,58,117,0.2); color: var(--brand);">
                        @if($evento->eventoDias->count())
                            @foreach($evento->eventoDias as $dia)
                                <option value="{{ $dia->dia_semana }}" {{ old('dia_semana') == $dia->dia_semana ? 'selected' : '' }}>
                                    {{ $diasMap[$dia->dia_semana] ?? $dia->dia_semana }}
                                </option>
                            @endforeach
                        @else
                            <option value="1">LUNES</option>
                            <option value="2">MARTES</option>
                            <option value="3">MIÉRCOLES</option>
                            <option value="4">JUEVES</option>
                            <option value="5">VIERNES</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        {{-- ============ SECCIÓN 3: SELECCIÓN DE HORARIO ============ --}}
        <div class="mb-5">
            <div class="seccion-titulo">
                <span class="material-symbols-outlined">schedule</span>
                Sección 3: Selección de Horario
            </div>

            <input type="hidden" name="evento_franja_id" id="evento_franja_id" value="{{ old('evento_franja_id') }}"/>

            <div class="row" id="franjasRow">
                @foreach($evento->eventoFranjas as $franja)
                    @php
                        $inscritos = $franja->inscripciones->count();
                        $capacidad = $franja->capacidad_maxima > 0 ? $franja->capacidad_maxima : $evento->capacidad_maxima;
                        $agotado = $capacidad > 0 && $inscritos >= $capacidad;
                        $porcentaje = $capacidad > 0 ? round(($inscritos / $capacidad) * 100) : 0;
                        $horaInicio = \Carbon\Carbon::parse($franja->hora_inicio)->format('H:i');
                        $horaFin = \Carbon\Carbon::parse($franja->hora_fin)->format('H:i');
                        $seleccionada = old('evento_franja_id') == $franja->id;
                    @endphp

                    <div class="col-sm-6 col-lg-4 mb-3 franja-card" data-dia-semana="{{ $franja->dia_semana }}">
                    @if($agotado)
                        {{-- Franja AGOTADA --}}
                        <div class="franja-btn franja-agotada">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="franja-hora" style="text-decoration: line-through; color: #94a3b8;">{{ $horaInicio }} - {{ $horaFin }}</span>
                                <span class="badge-agotado">AGOTADO</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-end">
                                <span style="font-size:11px; font-weight:600; color:#94a3b8; text-transform:uppercase;">{{ $evento->titulo }}</span>
                                <span style="font-size:11px; font-weight:700; color:#ef4444;">{{ $inscritos }}/{{ $capacidad }} cupos</span>
                            </div>
                            <div class="franja-bar-bg"><div class="franja-bar-fill" style="width:100%; background:#ef4444;"></div></div>
                        </div>
                    @else
                        {{-- Franja DISPONIBLE --}}
                        <button type="button" onclick="seleccionarFranja({{ $franja->id }}, this)"
                            data-franja-id="{{ $franja->id }}"
                            class="franja-btn {{ $seleccionada ? 'franja-selected' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="franja-hora {{ $seleccionada ? 'text-corp' : '' }}">{{ $horaInicio }} - {{ $horaFin }}</span>
                                @if($seleccionada)
                                    <span class="badge-seleccionado">SELECCIONADO</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-end">
                                <span style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase;">{{ $evento->titulo }}</span>
                                <span style="font-size:11px; font-weight:700; color:var(--brand);">{{ str_pad($inscritos, 2, '0', STR_PAD_LEFT) }}/{{ $capacidad }} cupos</span>
                            </div>
                            <div class="franja-bar-bg">
                                <div class="franja-bar-fill {{ $seleccionada ? '' : '' }}" style="width: {{ $porcentaje }}%; {{ $seleccionada ? 'background:var(--brand);' : '' }}"></div>
                            </div>
                        </button>
                    @endif
                    </div>
                @endforeach

                @if($evento->eventoFranjas->count() === 0)
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                        <p class="font-weight-bold">No hay franjas horarias configuradas para este evento.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============ BOTÓN CONFIRMAR ============ --}}
        <div class="pt-4 text-center" style="border-top: 1px solid rgba(46,58,117,0.08);">
            <button type="submit" id="btnConfirmar" class="btn-submit-inscripcion">
                <span>CONFIRMAR MI INSCRIPCIÓN</span>
                <span class="material-symbols-outlined">check_circle</span>
            </button>
            <p class="text-center text-muted small mt-3">
                <i class="fas fa-info-circle mr-1"></i>
                Al confirmar, se registrará su inscripción al evento.
            </p>
        </div>

        </form>
    </div>
</div>

    </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 for beautiful alerts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Manejo de alertas con SweetAlert2 al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Inscripción Exitosa!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#2e3a75',
            confirmButtonText: 'Entendido'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'warning',
            title: 'No se pudo realizar el agendamiento',
            html: `{{ session('error') }}`,
            confirmButtonColor: '#2e3a75',
            confirmButtonText: 'Volver a intentar'
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: `
                <ul style="text-align: left; margin: 0; padding-left: 1.2rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonColor: '#2e3a75'
        });
    @endif
});

function seleccionarFranja(franjaId, el) {
    // Limpiar selección previa
    document.querySelectorAll('.franja-btn').forEach(function(btn) {
        btn.classList.remove('franja-selected');

        var hora = btn.querySelector('.franja-hora');
        if (hora) { hora.classList.remove('text-corp'); hora.style.color = ''; }

        var badge = btn.querySelector('.badge-seleccionado');
        if (badge) badge.remove();

        var barFill = btn.querySelector('.franja-bar-fill');
        if (barFill) { barFill.style.background = ''; }
    });

    // Aplicar selección al botón clickeado
    el.classList.add('franja-selected');

    var hora = el.querySelector('.franja-hora');
    if (hora) { hora.classList.add('text-corp'); }

    // Agregar badge "SELECCIONADO"
    var headerDiv = el.querySelector('.d-flex.justify-content-between.align-items-start');
    if (headerDiv && !headerDiv.querySelector('.badge-seleccionado')) {
        var badge = document.createElement('span');
        badge.className = 'badge-seleccionado';
        badge.textContent = 'SELECCIONADO';
        headerDiv.appendChild(badge);
    }

    var barFill = el.querySelector('.franja-bar-fill');
    if (barFill) { barFill.style.background = 'var(--brand)'; }

    // Setear valor hidden
    document.getElementById('evento_franja_id').value = franjaId;
}

// Escuchar cambios en la identificación para traer datos del usuario
document.getElementById('identificacion').addEventListener('change', function() {
    var identificacion = this.value.trim();
    var msgEl = document.getElementById('identificacion-msg');
    
    if (identificacion.length > 4) {
        // Mostrar indicador de carga
        document.body.style.cursor = 'wait';
        msgEl.style.display = 'block';
        msgEl.className = 'form-text mt-1 text-muted small';
        msgEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Buscando datos...';
        
        // Usar la ruta absoluta generada por Blade
        var url = "{{ url('/eventos/api/usuario') }}/" + identificacion;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                document.body.style.cursor = 'default';
                if (data.found) {
                    msgEl.className = 'form-text mt-1 text-success small font-weight-bold';
                    msgEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Datos encontrados';
                    
                    if(data.nombres) document.getElementById('nombres').value = data.nombres;
                    if(data.primer_apellido) document.getElementById('primer_apellido').value = data.primer_apellido;
                    if(data.segundo_apellido) document.getElementById('segundo_apellido').value = data.segundo_apellido;
                    
                    if(data.servicio_unidad) {
                        // Buscar la opción que coincida en el select (value o text)
                        var select = document.getElementById('servicio');
                        var foundOption = false;
                        // Normalizar para comparación (opcional)
                        var serviceToFind = data.servicio_unidad.toLowerCase().trim();

                        for (var i = 0; i < select.options.length; i++) {
                            var optText = select.options[i].text.toLowerCase().trim();
                            var optVal = select.options[i].value.toLowerCase().trim();
                            
                            if (optText === serviceToFind || optVal === serviceToFind) {
                                select.selectedIndex = i;
                                foundOption = true;
                                document.getElementById('servicio_hidden').value = select.value;
                                break;
                            }
                        }
                    }
                    
                    // Ocultar mensaje después de 3 segundos si fue exitoso
                    setTimeout(() => { msgEl.style.display = 'none'; }, 3000);
                } else {
                    // Limpiar campos si no se encuentra (opcional, pero útil si se equivocó de cédula)
                    document.getElementById('nombres').value = '';
                    document.getElementById('primer_apellido').value = '';
                    document.getElementById('segundo_apellido').value = '';
                    document.getElementById('servicio').selectedIndex = 0;
                    document.getElementById('servicio_hidden').value = '';
                    
                    // Mostrar mensaje advirtiendo que no existen datos
                    msgEl.className = 'form-text mt-1 text-danger small font-weight-bold';
                    if (data.error_msg) {
                        msgEl.innerHTML = '<i class="fas fa-ban mr-1"></i> ' + data.error_msg;
                        document.getElementById('btnConfirmar').disabled = true;
                    } else {
                        msgEl.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> No se encontraron datos para esta identificación';
                        document.getElementById('btnConfirmar').disabled = false;
                    }
                }
            })
            .catch(error => {
                document.body.style.cursor = 'default';
                console.error('Error:', error);
                msgEl.className = 'form-text mt-1 text-danger small font-weight-bold';
                msgEl.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Hubo un error al buscar los datos. Intente de nuevo.';
            });
    } else {
        msgEl.style.display = 'none';
    }
});

// Filtrar franjas al cambiar de día
var diaSelect = document.getElementById('dia_semana');
if (diaSelect) {
    function filtrarFranjasPorDia() {
        var diaSeleccionado = diaSelect.value;
        var franjaCards = document.querySelectorAll('.franja-card');
        var hayVisibles = false;
        
        franjaCards.forEach(function(card) {
            var diaSemana = card.getAttribute('data-dia-semana');
            // Mostrar si la franja pertenece al día seleccionado o si no tiene dia_semana (legacy)
            if (diaSemana === null || diaSemana === '' || diaSemana === diaSeleccionado) {
                card.style.display = '';
                hayVisibles = true;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Limpiar selección de franja al cambiar de día
        document.getElementById('evento_franja_id').value = '';
        document.querySelectorAll('.franja-btn').forEach(function(btn) {
            btn.classList.remove('franja-selected');
            var hora = btn.querySelector('.franja-hora');
            if (hora) { hora.classList.remove('text-corp'); hora.style.color = ''; }
            var badge = btn.querySelector('.badge-seleccionado');
            if (badge) badge.remove();
            var barFill = btn.querySelector('.franja-bar-fill');
            if (barFill) { barFill.style.background = ''; }
        });
    }
    
    diaSelect.addEventListener('change', filtrarFranjasPorDia);
    // Filtrar al cargar la página
    filtrarFranjasPorDia();
}

// Validar antes de enviar
document.getElementById('inscripcionForm').addEventListener('submit', function(e) {
    var franjaId = document.getElementById('evento_franja_id').value;
    if (!franjaId) {
        e.preventDefault();
        alert('Por favor seleccione una franja horaria antes de confirmar.');
        return false;
    }
});
</script>
@endpush
