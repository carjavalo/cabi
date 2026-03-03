@extends('layouts.app')

@push('head')
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
    </style>
@endpush

@section('title','Inscripción GYM')
@section('header','Inscripción Gimnasio')

@section('content')
<div class="relative w-full">
    <!-- Background Overlay Image (absolute inside content so scrolling stays in layout) -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-primary/10 z-10"></div>
        <img class="w-full h-full object-cover opacity-20" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD-kWSr6IfoWHHBI9OPn1cDvVRg2xOdI8YRGwzXsBf4SUml8V_aXdbwBZ1cajSuTPEVmriUXMMG3qYMTUTGoB_2vzd4PnGnd7WvhJYNhZrsQrj4Q7nlwsD3HP9oA4YF3QnTYT6Pql1uwK55IZEsRhNc4cRZFD7Uiepr8KoWOkkpRkbyowuXPZls2-sOK93k8NGmYZJds5-cy7GND-fZxyi_0VC42c7ssR0hUyJFG3v_E224Z5B9Wv3DdcMNevTkF5erkvXrGBBKou7M" alt="Gym background"/>
    </div>

    <!-- Main Content -->
    <main class="relative z-20 w-full mx-auto max-w-4xl px-4 pt-3 pb-6">
        <!-- Hero Title & Progress -->
        <div class="mb-4 text-center md:text-left">
            <h2 class="text-2xl font-extrabold text-slate-900 mb-1">Inscripción Gimnasio HUV</h2>
            <p class="text-slate-600 mb-3 max-w-xl">Únete a nuestra comunidad de bienestar. Completa los datos a continuación para formalizar tu registro en las instalaciones deportivas del hospital.</p>
            @if(session('success'))
                <div class="mt-4 max-w-xl mx-auto md:mx-0">
                    <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 text-sm font-semibold">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if($errors->any())
                <div class="mt-4 max-w-xl mx-auto md:mx-0">
                    <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 p-3 text-sm">
                        <ul class="list-disc ml-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <div class="flex flex-col gap-2 max-w-md">
                <div class="flex justify-between items-end">
                    <span class="text-sm font-bold text-primary">Progreso de Inscripción</span>
                    <span class="text-xs font-bold text-primary/70">Paso 1 de 2</span>
                </div>
                <div class="h-2 w-full bg-primary/10 rounded-full overflow-hidden">
                    <div class="h-full bg-primary w-1/2 rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- Registration Card -->
        <div class="glass-card border border-white rounded-xl shadow-2xl shadow-primary/5 p-4 md:p-6 mb-6">
            <div class="flex items-center gap-2 mb-8 border-b border-slate-100 pb-4">
                <span class="material-symbols-outlined text-primary">person_add</span>
                <h3 class="text-xl font-bold text-slate-800">Información del Solicitante</h3>
            </div>
            <form method="POST" action="{{ route('inscripcion.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 lg:gap-x-10 gap-y-5 sm:gap-y-6">
                @csrf
                <!-- Column 1: Personal Info -->
                <div class="space-y-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Nombres</label>
                        <input name="nombres" class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="Ej. Juan Andrés" type="text"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Primer Apellido</label>
                        <input name="primer_apellido" class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="Ej. Rodríguez" type="text"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Segundo Apellido</label>
                        <input name="segundo_apellido" class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="Ej. Gómez" type="text"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">No. Documento</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">badge</span>
                            <input name="identificacion" class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 pl-12 pr-4 text-slate-900 transition-all" placeholder="1.234.567.890" type="text"/>
                        </div>
                    </div>
                </div>
                <!-- Column 2: Details -->
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-slate-700">Edad</label>
                            <input name="edad" class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="25" type="number"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-slate-700">Celular</label>
                            <input name="celular" class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="300 123 4567" type="tel"/>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Tipo de Vinculación</label>
                        <select name="tipo_vinculacion_id" class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all appearance-none">
                            <option value="">Seleccione vinculación</option>
                            @if(isset($vinculaciones) && $vinculaciones->count())
                                @foreach($vinculaciones as $v)
                                    <option value="{{ $v->id }}" {{ old('tipo_vinculacion_id') == $v->id ? 'selected' : '' }}>{{ $v->nombre }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Servicio / Unidad</label>
                        <select name="servicio_id" class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all appearance-none">
                            <option value="">Seleccione servicio</option>
                            @if(isset($servicios) && $servicios->count())
                                @foreach($servicios as $s)
                                    <option value="{{ $s->id }}" {{ old('servicio_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Contacto de Emergencia</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">emergency</span>
                            <input name="contacto_emergencia" class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 pl-12 pr-4 text-slate-900 transition-all" placeholder="Nombre y Teléfono" type="text"/>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Correo (lectura)</label>
                        <input name="correolec" class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="correo.lectura@ejemplo.com" type="email"/>
                    </div>
                </div>
                <!-- Action Button Row -->
                <div class="md:col-span-2 pt-4 flex flex-col md:flex-row items-center justify-between gap-4 border-t border-slate-100 mt-3">
                    <div class="flex items-center gap-3 text-slate-500">
                        <span class="material-symbols-outlined text-xl">verified_user</span>
                        <p class="text-xs leading-tight">Al realizar la inscripción, acepto el reglamento interno y el tratamiento de datos personales de la institución.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if(auth()->check() && in_array(auth()->user()->role, ['Operador', 'Super Admin', 'Administrador']))
                            <button id="autorizarAgendaBtn" type="button" class="hidden md:inline-flex items-center gap-2 px-4 py-2 bg-white text-primary border border-primary rounded-lg font-semibold shadow-sm hover:bg-primary/5 transition" title="Autorizar agenda" onclick="abrirModalAutorizacion()">
                                <span class="material-symbols-outlined">check_circle</span>
                                Autorizar Agenda
                            </button>
                        @endif

                        <button class="w-full md:w-auto px-8 py-3 bg-primary text-white font-bold rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all flex items-center justify-center gap-3 group" type="submit">
                            Guardar
                            <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">save</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-8">
            <div class="flex items-start gap-4 p-4 rounded-xl bg-white/40 border border-white">
                <div class="bg-primary/10 p-2 rounded-lg text-primary">
                    <span class="material-symbols-outlined">schedule</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Horarios</h4>
                    <p class="text-xs text-slate-600">Lunes a Viernes<br/>5:00 AM - 9:00 PM</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 rounded-xl bg-white/40 border border-white">
                <div class="bg-primary/10 p-2 rounded-lg text-primary">
                    <span class="material-symbols-outlined">location_on</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Ubicación</h4>
                    <p class="text-xs text-slate-600">Sótano Torre Central<br/>Hospital Universitario del Valle</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 rounded-xl bg-white/40 border border-white">
                <div class="bg-primary/10 p-2 rounded-lg text-primary">
                    <span class="material-symbols-outlined">fitness_center</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Equipamiento</h4>
                    <p class="text-xs text-slate-600">Área Cardiovascular<br/>Pesas y Funcional</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="relative z-20 w-full py-8 bg-slate-100 border-t border-slate-200 text-center">
        <p class="text-sm text-slate-500 font-medium">© 2024 Hospital Universitario del Valle - Bienestar Institucional</p>
    </footer>
</div>

@if(auth()->check() && in_array(auth()->user()->role, ['Operador', 'Super Admin', 'Administrador']))
<!-- Modal Autorización de Agenda -->
<div id="modalAutorizacion" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity" style="z-index: 9999;">
    <div class="bg-white dark:bg-slate-900 w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
            <div class="flex items-center gap-3">
                <div class="bg-primary/10 p-2 rounded-lg text-primary">
                    <span class="material-symbols-outlined">how_to_reg</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Autorización para Agendamiento</h2>
                    <p class="text-xs text-slate-500">Gestione los permisos de acceso a los eventos</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalAutorizacion()" class="p-2 rounded-lg text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Body con DataTable -->
        <div class="p-6 overflow-y-auto flex-1">
            <div class="mb-4 flex items-center justify-between">
                <input type="text" id="buscarInscritos" placeholder="Buscar por Nombre / Identificación..." class="px-4 py-2 border-slate-200 rounded-lg text-sm w-full md:w-1/3 focus:border-primary focus:ring-primary">
            </div>
            <div class="border border-slate-200 rounded-lg overflow-x-auto">
                <table class="w-full text-left border-collapse" id="tablaInscritos">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase">
                            <th class="px-4 py-3 border-b border-slate-200">Identificación</th>
                            <th class="px-4 py-3 border-b border-slate-200">Nombres</th>
                            <th class="px-4 py-3 border-b border-slate-200">Servicio</th>
                            <th class="px-4 py-3 border-b border-slate-200 text-center">Estado</th>
                            <th class="px-4 py-3 border-b border-slate-200 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyInscritos" class="divide-y divide-slate-100 text-sm">
                        <!-- Llenado por JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function abrirModalAutorizacion() {
        document.getElementById('modalAutorizacion').classList.remove('hidden');
        document.getElementById('modalAutorizacion').classList.add('flex');
        cargarInscritos();
    }

    function cerrarModalAutorizacion() {
        document.getElementById('modalAutorizacion').classList.add('hidden');
        document.getElementById('modalAutorizacion').classList.remove('flex');
    }

    let todosInscritos = [];

    function cargarInscritos() {
        document.getElementById('tbodyInscritos').innerHTML = '<tr><td colspan="5" class="text-center py-4 text-slate-500">Cargando datos...</td></tr>';
        
        fetch('{{ route("api.inscritos.lista") }}')
            .then(res => res.json())
            .then(data => {
                todosInscritos = data.data;
                renderizarTabla(todosInscritos);
            })
            .catch(e => {
                document.getElementById('tbodyInscritos').innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Error cargando inscritos</td></tr>';
            });
    }

    document.getElementById('buscarInscritos').addEventListener('keyup', function(e) {
        const texto = e.target.value.toLowerCase();
        const filtrados = todosInscritos.filter(i => 
            (i.identificacion && i.identificacion.toLowerCase().includes(texto)) || 
            (i.nombres && i.nombres.toLowerCase().includes(texto)) || 
            (i.primer_apellido && i.primer_apellido.toLowerCase().includes(texto))
        );
        renderizarTabla(filtrados);
    });

    function renderizarTabla(inscritos) {
        const tbody = document.getElementById('tbodyInscritos');
        tbody.innerHTML = '';
        
        if(inscritos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-slate-500">No hay registros encontrados.</td></tr>';
            return;
        }

        inscritos.forEach(i => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';
            
            const estadoBadge = i.autorizado == 1 
                ? '<span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">Autorizado</span>'
                : '<span class="px-2 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">Pendiente</span>';
                
            const urlAcesso = '{{ url("/eventos/inscripcion/1") }}?identificacion=' + (i.identificacion || '');

            const btnClass = i.autorizado == 1 ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100';
            const btnIcon = i.autorizado == 1 ? 'block' : 'check';
            const btnText = i.autorizado == 1 ? 'Revocar' : 'Autorizar';
            
            const linkActivo = i.autorizado == 1 ? `<a href="${urlAcesso}" target="_blank" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="Ir al evento"><span class="material-symbols-outlined text-sm">link</span></a>` : '';

            tr.innerHTML = `
                <td class="px-4 py-3 font-semibold text-slate-700">${i.identificacion || 'N/A'}</td>
                <td class="px-4 py-3">${i.nombres || ''} ${i.primer_apellido || ''} ${i.segundo_apellido || ''}</td>
                <td class="px-4 py-3 text-slate-500">${i.servicio_unidad || 'N/A'}</td>
                <td class="px-4 py-3 text-center">${estadoBadge}</td>
                <td class="px-4 py-3">
                    <div class="flex justify-center items-center gap-2">
                        <button onclick="toggleAutorizacion(${i.id})" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors ${btnClass}">
                            <span class="material-symbols-outlined text-sm">${btnIcon}</span>
                            ${btnText}
                        </button>
                        ${linkActivo}
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function toggleAutorizacion(id) {
        fetch(`{{ url('/api/inscritos') }}/${id}/autorizar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Actualizar array en memoria
                const idx = todosInscritos.findIndex(i => i.id === id);
                if(idx !== -1) {
                    todosInscritos[idx].autorizado = data.autorizado ? 1 : 0;
                    document.getElementById('buscarInscritos').dispatchEvent(new Event('keyup')); // refresca vista actual
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error al intentar cambiar autorización.');
        });
    }
</script>
@endif

@endsection


