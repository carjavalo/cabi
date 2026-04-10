<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $capacitacion->titulo }} - Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --brand: #2e3a75; --brand-light: #3b4a8a; --brand-dark: #1f2850; --accent: #4fc3f7; }
        body {
            background: linear-gradient(135deg, rgba(46,58,117,0.94) 0%, rgba(31,40,80,0.97) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 1.5rem 0;
        }
        .asist-card {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 12px 48px rgba(0,0,0,0.25);
            max-width: 540px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.4s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .asist-header {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-light) 100%);
            color: #fff;
            padding: 1.8rem 2rem;
            text-align: center;
            position: relative;
        }
        .asist-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--brand-light), var(--accent));
        }
        .asist-header h2 { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.4rem; }
        .asist-header .meta { font-size: 0.82rem; opacity: 0.85; }
        .asist-body { padding: 1.8rem 2rem; }
        .info-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; color: #555; font-size: 0.83rem; }
        .info-row i { color: var(--brand); width: 16px; text-align: center; }
        .btn-brand {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-light) 100%);
            color: #fff; border: none; border-radius: 0.75rem; padding: 0.85rem; font-weight: 600; width: 100%; font-size: 1rem;
            transition: all 0.2s;
        }
        .btn-brand:hover { background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand) 100%); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(46,58,117,0.3); }
        .form-control-custom {
            border-radius: 0.65rem; padding: 0.6rem 0.9rem; border: 2px solid #e2e5ea; font-size: 0.88rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control-custom:focus { border-color: var(--brand); box-shadow: 0 0 0 3px rgba(46,58,117,0.12); }
        .form-label-sm { font-weight: 600; font-size: 0.82rem; color: var(--brand); margin-bottom: 0.25rem; }
        .firma-box {
            border: 2px dashed #ccc; border-radius: 0.75rem; padding: 0.9rem;
            text-align: center; background: #fafbfc; transition: all 0.2s;
        }
        .firma-box.checked { border-color: var(--brand); background: rgba(46,58,117,0.04); }
        .row-fields { display: flex; gap: 0.8rem; }
        .row-fields > div { flex: 1; }
        @media (max-width: 480px) {
            .row-fields { flex-direction: column; gap: 0; }
            .asist-body { padding: 1.2rem 1.3rem; }
        }
        .counter-bar {
            background: linear-gradient(135deg, #f8f9fa, #eef1f5);
            padding: 0.7rem;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="asist-card">
        <div class="asist-header">
            <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
            <h2>{{ $capacitacion->titulo }}</h2>
            <div class="meta">Registro de Asistencia</div>
        </div>
        <div class="asist-body">
            {{-- Info capacitación --}}
            <div class="mb-3">
                <div class="info-row">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ $sesion->fecha->format('d/m/Y') }}</span>
                </div>
                @if($sesion->hora_inicio && $sesion->hora_fin)
                <div class="info-row">
                    <i class="fas fa-clock"></i>
                    <span>{{ \Carbon\Carbon::parse($sesion->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($sesion->hora_fin)->format('H:i') }}</span>
                </div>
                @endif
                @if($capacitacion->ubicacion)
                <div class="info-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $capacitacion->ubicacion }}</span>
                </div>
                @endif
                @if($capacitacion->instructor)
                <div class="info-row">
                    <i class="fas fa-user-tie"></i>
                    <span>{{ $capacitacion->instructor }}</span>
                </div>
                @endif
            </div>

            {{-- Alertas --}}
            @if(session('success'))
                <div class="alert alert-success rounded-lg py-2 small">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger rounded-lg py-2 small">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info rounded-lg py-2 small">
                    <i class="fas fa-info-circle mr-1"></i> {{ session('info') }}
                </div>
            @endif

            {{-- Verificar si el enlace es vigente y si está en horario --}}
            @if(!$esUltimaSesion)
                <div class="alert alert-warning rounded-lg py-2 small">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Este enlace ya no está vigente. Se ha generado una nueva sesión para esta capacitación.
                </div>
            @elseif(!$sesion->estaAbierta())
                <div class="alert alert-warning rounded-lg py-2 small">
                    <i class="fas fa-clock mr-1"></i>
                    El registro de asistencia solo está disponible durante la fecha y horario de la capacitación.
                </div>
            @else
                {{-- Formulario de asistencia --}}
                <form method="POST" action="{{ route('capacitaciones.asistencia.marcar', $sesion->token) }}" id="formAsistencia">
                    @csrf

                    {{-- Número de identificación --}}
                    <div class="form-group mb-2">
                        <label class="form-label-sm"><i class="fas fa-id-card mr-1"></i> Número de identificación</label>
                        <input type="text" name="identificacion" id="identificacion"
                               class="form-control form-control-custom @error('identificacion') is-invalid @enderror"
                               placeholder="Número de identificación"
                               value="{{ old('identificacion') }}" required>
                        @error('identificacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nombre + Apellidos --}}
                    <div class="row-fields">
                        <div class="form-group mb-2">
                            <label class="form-label-sm"><i class="fas fa-user mr-1"></i> Nombre</label>
                            <input type="text" name="nombre" id="nombre"
                                   class="form-control form-control-custom @error('nombre') is-invalid @enderror"
                                   placeholder="Nombres"
                                   value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label-sm"><i class="fas fa-user mr-1"></i> Apellidos</label>
                            <input type="text" name="apellidos" id="apellidos"
                                   class="form-control form-control-custom @error('apellidos') is-invalid @enderror"
                                   placeholder="Apellidos"
                                   value="{{ old('apellidos') }}" required>
                            @error('apellidos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Área/Servicio + Cargo --}}
                    <div class="row-fields">
                        <div class="form-group mb-2">
                            <label class="form-label-sm"><i class="fas fa-building mr-1"></i> Área / Servicio</label>
                            <input type="text" name="area_servicio" id="area_servicio"
                                   class="form-control form-control-custom @error('area_servicio') is-invalid @enderror"
                                   placeholder="Ej: Urgencias, Cirugía..."
                                   value="{{ old('area_servicio') }}" required>
                            @error('area_servicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label-sm"><i class="fas fa-briefcase mr-1"></i> Cargo</label>
                            <input type="text" name="cargo" id="cargo"
                                   class="form-control form-control-custom @error('cargo') is-invalid @enderror"
                                   placeholder="Ej: Enfermera, Médico..."
                                   value="{{ old('cargo') }}" required>
                            @error('cargo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Tipo de vinculación --}}
                    <div class="form-group mb-2">
                        <label class="form-label-sm"><i class="fas fa-file-contract mr-1"></i> Tipo de vinculación</label>
                        <select name="tipo_contrato" id="tipo_contrato"
                                class="form-control form-control-custom @error('tipo_contrato') is-invalid @enderror" required>
                            <option value="">Seleccione tipo de vinculación...</option>
                            @php
                                $tiposVinculacion = ['Agesoc', 'Asstracud', 'Diamante', 'Estudiantes', 'Imagenes San Jose', 'Napoles', 'Planta', 'Contratista'];
                            @endphp
                            @foreach($tiposVinculacion as $tipo)
                                <option value="{{ $tipo }}" {{ old('tipo_contrato') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                            @endforeach
                        </select>
                        @error('tipo_contrato')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Firma / Autorización --}}
                    <div class="form-group mb-3">
                        <div class="firma-box" id="firmaBox">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input @error('autoriza_firma') is-invalid @enderror"
                                       id="autoriza_firma" name="autoriza_firma" value="1" {{ old('autoriza_firma') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="autoriza_firma" style="font-size:0.83rem;">
                                    <i class="fas fa-signature mr-1"></i>
                                    <strong>Autorizo y firmo</strong> mi registro de asistencia a esta capacitación
                                </label>
                            </div>
                            @error('autoriza_firma')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand">
                        <i class="fas fa-user-check mr-1"></i> Registrar Asistencia
                    </button>
                </form>
            @endif
        </div>

        {{-- Contador --}}
        @php $totalRegistros = $sesion->registros()->count(); @endphp
        <div class="counter-bar">
            <small class="text-muted">
                <i class="fas fa-users mr-1" style="color: var(--brand);"></i>
                <strong>{{ $totalRegistros }}</strong> {{ $totalRegistros == 1 ? 'persona ha' : 'personas han' }} registrado asistencia
            </small>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var identificacionInput = document.getElementById('identificacion');
        var timer = null;

        if (identificacionInput) {
            identificacionInput.addEventListener('input', function() {
                clearTimeout(timer);
                var val = this.value.trim();
                if (val.length >= 5) {
                    timer = setTimeout(function() {
                        fetch('/capacitaciones/asistencia/buscar-usuario/' + encodeURIComponent(val))
                            .then(function(r) { return r.json(); })
                            .then(function(data) {
                                if (data.found) {
                                    var nombreInput = document.getElementById('nombre');
                                    var apellidosInput = document.getElementById('apellidos');
                                    var areaInput = document.getElementById('area_servicio');
                                    var cargoInput = document.getElementById('cargo');
                                    var tipoInput = document.getElementById('tipo_contrato');

                                    if (nombreInput && !nombreInput.value) nombreInput.value = data.nombre || '';
                                    if (apellidosInput && !apellidosInput.value) apellidosInput.value = data.apellidos || '';
                                    if (areaInput && !areaInput.value) areaInput.value = data.area_servicio || '';
                                    if (cargoInput && !cargoInput.value) cargoInput.value = data.cargo || '';
                                    if (tipoInput && !tipoInput.value && data.tipo_vinculacion) {
                                        for (var i = 0; i < tipoInput.options.length; i++) {
                                            if (tipoInput.options[i].value === data.tipo_vinculacion) {
                                                tipoInput.selectedIndex = i;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }).catch(function() {});
                    }, 500);
                }
            });
        }

        // Visual feedback for firma checkbox
        var firmaCheck = document.getElementById('autoriza_firma');
        var firmaBox = document.getElementById('firmaBox');
        if (firmaCheck && firmaBox) {
            firmaCheck.addEventListener('change', function() {
                firmaBox.classList.toggle('checked', this.checked);
            });
            if (firmaCheck.checked) firmaBox.classList.add('checked');
        }
    });
    </script>
</body>
</html>
