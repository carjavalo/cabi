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
        :root { --brand: #2e3a75; }
        body {
            background: linear-gradient(135deg, rgba(46,58,117,0.92) 0%, rgba(31,40,80,0.95) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 2rem 0;
        }
        .asist-card {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 8px 40px rgba(0,0,0,0.2);
            max-width: 520px;
            width: 100%;
            overflow: hidden;
        }
        .asist-header {
            background: linear-gradient(135deg, var(--brand) 0%, #3b4a8a 100%);
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
        .asist-header h2 { font-size: 1.3rem; font-weight: 700; margin-bottom: 0.5rem; }
        .asist-header .meta { font-size: 0.85rem; opacity: 0.85; }
        .asist-body { padding: 2rem; }
        .info-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: #555; font-size: 0.85rem; }
        .info-row i { color: var(--brand); width: 18px; text-align: center; }
        .btn-brand { background: var(--brand); color: #fff; border: none; border-radius: 0.75rem; padding: 0.8rem; font-weight: 600; width: 100%; font-size: 1rem; }
        .btn-brand:hover { background: #1f2850; color: #fff; }
        .form-control-custom { border-radius: 0.75rem; padding: 0.7rem 1rem; border: 2px solid #e0e0e0; font-size: 0.9rem; }
        .form-control-custom:focus { border-color: var(--brand); box-shadow: 0 0 0 3px rgba(46,58,117,0.15); }
        .form-label-sm { font-weight: 600; font-size: 0.85rem; color: var(--brand); margin-bottom: 0.3rem; }
        .firma-box {
            border: 2px dashed #ccc; border-radius: 0.75rem; padding: 1rem;
            text-align: center; background: #fafafa;
        }
        .firma-box.checked { border-color: var(--brand); background: rgba(46,58,117,0.05); }
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
                {{-- Formulario --}}
                <form method="POST" action="{{ route('capacitaciones.asistencia.marcar', $sesion->token) }}" id="formAsistencia">
                    @csrf

                    <div class="form-group mb-2">
                        <label class="form-label-sm"><i class="fas fa-id-card mr-1"></i> Identificación</label>
                        <input type="text" name="identificacion" id="identificacion"
                               class="form-control form-control-custom @error('identificacion') is-invalid @enderror"
                               placeholder="Número de identificación"
                               value="{{ old('identificacion') }}" required>
                        @error('identificacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label-sm"><i class="fas fa-user mr-1"></i> Nombre completo</label>
                        <input type="text" name="nombre" id="nombre"
                               class="form-control form-control-custom @error('nombre') is-invalid @enderror"
                               placeholder="Nombre y apellidos"
                               value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label-sm"><i class="fas fa-file-contract mr-1"></i> Tipo de contrato</label>
                        <select name="tipo_contrato" id="tipo_contrato"
                                class="form-control form-control-custom @error('tipo_contrato') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($vinculaciones as $v)
                                <option value="{{ $v }}" {{ old('tipo_contrato') == $v ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        @error('tipo_contrato')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label-sm"><i class="fas fa-envelope mr-1"></i> Correo electrónico</label>
                        <input type="email" name="correo" id="correo"
                               class="form-control form-control-custom @error('correo') is-invalid @enderror"
                               placeholder="correo@ejemplo.com"
                               value="{{ old('correo') }}">
                        @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <div class="firma-box" id="firmaBox">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input @error('autoriza_firma') is-invalid @enderror"
                                       id="autoriza_firma" name="autoriza_firma" value="1" {{ old('autoriza_firma') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="autoriza_firma" style="font-size:0.85rem;">
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

            {{-- Contador --}}
            @php $totalRegistros = $sesion->registros()->count(); @endphp
            <div class="mt-3 text-center">
                <small class="text-muted">
                    <i class="fas fa-users mr-1"></i>
                    {{ $totalRegistros }} {{ $totalRegistros == 1 ? 'persona ha' : 'personas han' }} registrado asistencia
                </small>
            </div>
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
                                    var correoInput = document.getElementById('correo');
                                    var tipoInput = document.getElementById('tipo_contrato');
                                    if (nombreInput && !nombreInput.value) nombreInput.value = data.nombre;
                                    if (correoInput && !correoInput.value) correoInput.value = data.correo || '';
                                    if (tipoInput && !tipoInput.value && data.tipo_contrato) {
                                        // Try to select matching option
                                        for (var i = 0; i < tipoInput.options.length; i++) {
                                            if (tipoInput.options[i].value === data.tipo_contrato) {
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
