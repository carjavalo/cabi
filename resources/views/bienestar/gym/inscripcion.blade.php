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
                        <input name="nombres" required class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="Ej. Juan Andrés" type="text"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Primer Apellido</label>
                        <input name="primer_apellido" required class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="Ej. Rodríguez" type="text"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Segundo Apellido</label>
                        <input name="segundo_apellido" required class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="Ej. Gómez" type="text"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">No. Documento</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">badge</span>
                            <input name="identificacion" required class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 pl-12 pr-4 text-slate-900 transition-all" placeholder="1.234.567.890" type="text"/>
                        </div>
                    </div>
                </div>
                <!-- Column 2: Details -->
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-slate-700">Edad</label>
                            <input name="edad" required class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="25" type="number"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-slate-700">Celular</label>
                            <input name="celular" required class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="300 123 4567" type="tel"/>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Tipo de Vinculación</label>
                        <select name="tipo_vinculacion_id" required class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all appearance-none">
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
                        <select name="servicio_id" required class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all appearance-none">
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
                            <input name="contacto_emergencia" required class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 pl-12 pr-4 text-slate-900 transition-all" placeholder="Nombre y Teléfono" type="text"/>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-slate-700">Correo (lectura)</label>
                        <input name="correolec" required class="w-full rounded-lg border-slate-200 bg-white/50 focus:border-primary focus:ring-primary h-10 px-4 text-slate-900 transition-all" placeholder="correo.lectura@ejemplo.com" type="email"/>
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
    <div class="bg-white dark:bg-slate-900 w-full max-w-6xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
            <div class="flex items-center gap-3">
                <div class="bg-primary/10 p-2 rounded-lg text-primary">
                    <span class="material-symbols-outlined">how_to_reg</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Autorización para Agendamiento</h2>
                    <p class="text-xs text-slate-500">Gestione los permisos de acceso y listados</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalAutorizacion()" class="p-2 rounded-lg text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex border-b border-slate-200 dark:border-slate-700 bg-slate-50/30 px-6">
            <button type="button" id="tabAutorizacion" onclick="cambiarTab('autorizacion')" class="px-5 py-3 text-sm font-bold border-b-2 border-primary text-primary transition-colors">
                <span class="material-symbols-outlined text-[16px] align-middle mr-1">how_to_reg</span>
                Autorización
            </button>
            <button type="button" id="tabListado" onclick="cambiarTab('listado')" class="px-5 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-700 transition-colors">
                <span class="material-symbols-outlined text-[16px] align-middle mr-1">groups</span>
                Listados Inscriptos
            </button>
        </div>

        <!-- ====== TAB 1: Autorización (contenido existente) ====== -->
        <div id="panelAutorizacion" class="p-6 overflow-y-auto flex-1">
            <div class="mb-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                <input type="text" id="buscarInscritos" placeholder="Buscar por Nombre / Identificación..." class="px-4 py-2 border-slate-200 rounded-lg text-sm w-full md:w-1/3 focus:border-primary focus:ring-primary">
                
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <select id="accionMasiva" class="px-3 py-2 border-slate-200 rounded-lg text-sm focus:border-primary focus:ring-primary h-[42px]">
                        <option value="">Seleccione una acción</option>
                        <option value="1">Autorizar seleccionados</option>
                        <option value="0">Revocar seleccionados</option>
                    </select>
                    <button onclick="aplicarAccionMasiva()" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-colors shadow-sm flex items-center gap-2 h-[42px]">
                        <span class="material-symbols-outlined text-[18px]">published_with_changes</span>
                        Cambiar Estado
                    </button>
                </div>
            </div>
            <div class="border border-slate-200 rounded-lg overflow-x-auto">
                <table class="w-full text-left border-collapse" id="tablaInscritos">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase">
                            <th class="px-4 py-3 border-b border-slate-200 text-center w-12">
                                <input type="checkbox" id="checkTodosInscritos" class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer" onclick="toggleTodosInscritos()">
                            </th>
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

        <!-- ====== TAB 2: Listados Inscriptos ====== -->
        <div id="panelListado" class="p-6 overflow-y-auto flex-1 hidden">
            <div class="mb-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                <input type="text" id="buscarListado" placeholder="Buscar por nombre, identificación, correo..." class="px-4 py-2 border-slate-200 rounded-lg text-sm w-full md:w-1/3 focus:border-primary focus:ring-primary">
                <button onclick="exportarExcel()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2 h-[42px]">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Exportar a Excel
                </button>
            </div>
            <div class="border border-slate-200 rounded-lg overflow-x-auto">
                <table class="w-full text-left border-collapse" id="tablaListadoInscritos">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase">
                            <th class="px-3 py-3 border-b border-slate-200">Nombre</th>
                            <th class="px-3 py-3 border-b border-slate-200">Apellidos</th>
                            <th class="px-3 py-3 border-b border-slate-200">Edad</th>
                            <th class="px-3 py-3 border-b border-slate-200">Contacto</th>
                            <th class="px-3 py-3 border-b border-slate-200">Correo</th>
                            <th class="px-3 py-3 border-b border-slate-200">Emergencia</th>
                            <th class="px-3 py-3 border-b border-slate-200">Servicio</th>
                            <th class="px-3 py-3 border-b border-slate-200">Vinculación</th>
                            <th class="px-3 py-3 border-b border-slate-200 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyListado" class="divide-y divide-slate-100 text-sm">
                        <!-- Llenado por JS -->
                    </tbody>
                </table>
            </div>
            <div id="paginacionListado" class="mt-4 flex items-center justify-between text-sm text-slate-500">
                <span id="infoListado"></span>
                <div class="flex gap-1" id="btnsPaginacion"></div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Editar Inscrito -->
<div id="modalEditarInscrito" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity" style="z-index: 10000;">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">edit</span>
                <h3 class="text-lg font-bold text-slate-800">Editar Inscrito</h3>
            </div>
            <button type="button" onclick="cerrarModalEditar()" class="p-2 rounded-lg text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <input type="hidden" id="editId">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-slate-600">Nombres</label>
                    <input type="text" id="editNombres" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:border-primary focus:ring-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-slate-600">Primer Apellido</label>
                    <input type="text" id="editPrimerApellido" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:border-primary focus:ring-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-slate-600">Segundo Apellido</label>
                    <input type="text" id="editSegundoApellido" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:border-primary focus:ring-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-slate-600">Celular</label>
                    <input type="text" id="editCelular" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:border-primary focus:ring-primary">
                </div>
                <div class="flex flex-col gap-1 md:col-span-2">
                    <label class="text-xs font-bold text-slate-600">Correo</label>
                    <input type="email" id="editCorreo" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:border-primary focus:ring-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-slate-600">Contacto Emergencia</label>
                    <input type="text" id="editEmergencia" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:border-primary focus:ring-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-slate-600">Servicio</label>
                    <input type="text" id="editServicio" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:border-primary focus:ring-primary">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="cerrarModalEditar()" class="px-4 py-2 text-sm font-bold text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Cancelar</button>
                <button type="button" onclick="guardarEdicion()" class="px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
<script>
    // ====== TAB SWITCHING ======
    function cambiarTab(tab) {
        const tabAuth = document.getElementById('tabAutorizacion');
        const tabList = document.getElementById('tabListado');
        const panelAuth = document.getElementById('panelAutorizacion');
        const panelList = document.getElementById('panelListado');

        if (tab === 'autorizacion') {
            tabAuth.classList.add('border-primary', 'text-primary');
            tabAuth.classList.remove('border-transparent', 'text-slate-500');
            tabList.classList.remove('border-primary', 'text-primary');
            tabList.classList.add('border-transparent', 'text-slate-500');
            panelAuth.classList.remove('hidden');
            panelList.classList.add('hidden');
        } else {
            tabList.classList.add('border-primary', 'text-primary');
            tabList.classList.remove('border-transparent', 'text-slate-500');
            tabAuth.classList.remove('border-primary', 'text-primary');
            tabAuth.classList.add('border-transparent', 'text-slate-500');
            panelList.classList.remove('hidden');
            panelAuth.classList.add('hidden');
            cargarListadoInscritos();
        }
    }

    function abrirModalAutorizacion() {
        document.getElementById('modalAutorizacion').classList.remove('hidden');
        document.getElementById('modalAutorizacion').classList.add('flex');
        cambiarTab('autorizacion');
        cargarInscritos();
    }

    function cerrarModalAutorizacion() {
        document.getElementById('modalAutorizacion').classList.add('hidden');
        document.getElementById('modalAutorizacion').classList.remove('flex');
    }

    // ====== TAB 1: AUTORIZACIÓN (existente) ======
    let todosInscritos = [];

    function cargarInscritos() {
        document.getElementById('tbodyInscritos').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-slate-500">Cargando datos...</td></tr>';
        
        fetch('{{ route("api.inscritos.lista") }}')
            .then(res => res.json())
            .then(data => {
                todosInscritos = data.data;
                renderizarTabla(todosInscritos);
            })
            .catch(e => {
                document.getElementById('tbodyInscritos').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-red-500">Error cargando inscritos</td></tr>';
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
        
        document.getElementById('checkTodosInscritos').checked = false;

        if(inscritos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-slate-500">No hay registros encontrados.</td></tr>';
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
                <td class="px-4 py-3 text-center">
                    <input type="checkbox" class="check-inscrito-auth h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer" value="${i.id}">
                </td>
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

    function toggleTodosInscritos() {
        const master = document.getElementById('checkTodosInscritos');
        const checkboxes = document.querySelectorAll('.check-inscrito-auth');
        checkboxes.forEach(chk => chk.checked = master.checked);
    }

    async function aplicarAccionMasiva() {
        const accion = document.getElementById('accionMasiva').value;
        if (accion === '') {
            alert('Por favor seleccione una acción a realizar.');
            return;
        }

        const checkboxes = document.querySelectorAll('.check-inscrito-auth:checked');
        if (checkboxes.length === 0) {
            alert('Por favor seleccione al menos un usuario de la lista.');
            return;
        }

        const accionNum = parseInt(accion);
        const accionTexto = accionNum === 1 ? 'autorizar' : 'revocar autorización a';

        if (!confirm(`¿Está seguro que desea ${accionTexto} los ${checkboxes.length} usuarios seleccionados?`)) {
            return;
        }

        const btnStatus = document.querySelector('button[onclick="aplicarAccionMasiva()"]');
        const originalText = btnStatus.innerHTML;
        btnStatus.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">refresh</span> Procesando...';
        btnStatus.disabled = true;

        let promesas = [];
        
        for (let chk of checkboxes) {
            const id = parseInt(chk.value);
            const obj = todosInscritos.find(i => parseInt(i.id) === id);
            
            if (obj && parseInt(obj.autorizado) !== accionNum) {
                const req = fetch(`{{ url('/api/inscritos') }}/${id}/autorizar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        obj.autorizado = data.autorizado ? 1 : 0;
                    }
                }).catch(err => console.error('Error en inscrito ' + id, err));
                promesas.push(req);
            }
        }

        await Promise.all(promesas);

        btnStatus.innerHTML = originalText;
        btnStatus.disabled = false;
        document.getElementById('accionMasiva').value = '';
        const textoBusqueda = document.getElementById('buscarInscritos').value.toLowerCase();
        if (textoBusqueda) {
            const filtrados = todosInscritos.filter(i => 
                (i.identificacion && i.identificacion.toLowerCase().includes(textoBusqueda)) || 
                (i.nombres && i.nombres.toLowerCase().includes(textoBusqueda)) || 
                (i.primer_apellido && i.primer_apellido.toLowerCase().includes(textoBusqueda))
            );
            renderizarTabla(filtrados);
        } else {
            renderizarTabla(todosInscritos);
        }
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
                const idx = todosInscritos.findIndex(i => i.id === id);
                if(idx !== -1) {
                    todosInscritos[idx].autorizado = data.autorizado ? 1 : 0;
                    document.getElementById('buscarInscritos').dispatchEvent(new Event('keyup'));
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

    // ====== TAB 2: LISTADOS INSCRIPTOS ======
    let datosListado = [];
    let listadoFiltrado = [];
    let paginaActual = 1;
    const porPagina = 10;

    function cargarListadoInscritos() {
        document.getElementById('tbodyListado').innerHTML = '<tr><td colspan="9" class="text-center py-4 text-slate-500">Cargando datos...</td></tr>';

        fetch('{{ route("api.inscritos.lista") }}')
            .then(res => res.json())
            .then(data => {
                datosListado = data.data;
                listadoFiltrado = [...datosListado];
                paginaActual = 1;
                renderizarListado();
            })
            .catch(() => {
                document.getElementById('tbodyListado').innerHTML = '<tr><td colspan="9" class="text-center py-4 text-red-500">Error cargando datos</td></tr>';
            });
    }

    document.getElementById('buscarListado').addEventListener('keyup', function(e) {
        const texto = e.target.value.toLowerCase();
        listadoFiltrado = datosListado.filter(i =>
            (i.nombres && i.nombres.toLowerCase().includes(texto)) ||
            (i.primer_apellido && i.primer_apellido.toLowerCase().includes(texto)) ||
            (i.segundo_apellido && i.segundo_apellido.toLowerCase().includes(texto)) ||
            (i.identificacion && i.identificacion.toLowerCase().includes(texto)) ||
            (i.correolec && i.correolec.toLowerCase().includes(texto)) ||
            (i.celular && i.celular.toLowerCase().includes(texto)) ||
            (i.servicio_unidad && i.servicio_unidad.toLowerCase().includes(texto)) ||
            (i.tipo_vinculacion && i.tipo_vinculacion.toLowerCase().includes(texto))
        );
        paginaActual = 1;
        renderizarListado();
    });

    function renderizarListado() {
        const tbody = document.getElementById('tbodyListado');
        tbody.innerHTML = '';

        const total = listadoFiltrado.length;
        const totalPaginas = Math.ceil(total / porPagina);
        const inicio = (paginaActual - 1) * porPagina;
        const fin = Math.min(inicio + porPagina, total);
        const paginados = listadoFiltrado.slice(inicio, fin);

        if (paginados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-slate-500">No hay registros encontrados.</td></tr>';
            document.getElementById('infoListado').textContent = '';
            document.getElementById('btnsPaginacion').innerHTML = '';
            return;
        }

        paginados.forEach(i => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';

            const escapeHtml = (str) => {
                if (!str) return '';
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            };

            tr.innerHTML = `
                <td class="px-3 py-3 font-semibold text-slate-700">${escapeHtml(i.nombres)}</td>
                <td class="px-3 py-3 text-slate-600">${escapeHtml(i.primer_apellido)} ${escapeHtml(i.segundo_apellido)}</td>
                <td class="px-3 py-3 text-slate-500 text-center">${i.edad != null ? i.edad : '<span class="text-slate-300">—</span>'}</td>
                <td class="px-3 py-3 text-slate-500">${escapeHtml(i.celular) || '<span class="text-slate-300">—</span>'}</td>
                <td class="px-3 py-3 text-slate-500 text-xs">${escapeHtml(i.correolec) || '<span class="text-slate-300">—</span>'}</td>
                <td class="px-3 py-3 text-slate-500 text-xs">${escapeHtml(i.contacto_emergencia) || '<span class="text-slate-300">—</span>'}</td>
                <td class="px-3 py-3 text-slate-500">${escapeHtml(i.servicio_unidad) || '<span class="text-slate-300">—</span>'}</td>
                <td class="px-3 py-3 text-slate-500">${escapeHtml(i.tipo_vinculacion) || '<span class="text-slate-300">—</span>'}</td>
                <td class="px-3 py-3 text-center">
                    <div class="flex justify-center items-center gap-1">
                        <button onclick="abrirModalEditar(${i.id})" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="Editar">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                        </button>
                        <button onclick="eliminarInscrito(${i.id})" class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Eliminar">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Info paginación
        document.getElementById('infoListado').textContent = `Mostrando ${inicio + 1} - ${fin} de ${total} registros`;

        // Botones paginación
        const btnsCont = document.getElementById('btnsPaginacion');
        btnsCont.innerHTML = '';

        if (totalPaginas > 1) {
            // Anterior
            const btnPrev = document.createElement('button');
            btnPrev.innerHTML = '&laquo;';
            btnPrev.className = `px-3 py-1 rounded text-xs font-bold ${paginaActual === 1 ? 'bg-slate-100 text-slate-300 cursor-not-allowed' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}`;
            btnPrev.disabled = paginaActual === 1;
            btnPrev.onclick = () => { if (paginaActual > 1) { paginaActual--; renderizarListado(); } };
            btnsCont.appendChild(btnPrev);

            // Páginas
            const maxBtns = 5;
            let startPage = Math.max(1, paginaActual - Math.floor(maxBtns / 2));
            let endPage = Math.min(totalPaginas, startPage + maxBtns - 1);
            if (endPage - startPage < maxBtns - 1) startPage = Math.max(1, endPage - maxBtns + 1);

            for (let p = startPage; p <= endPage; p++) {
                const btn = document.createElement('button');
                btn.textContent = p;
                btn.className = `px-3 py-1 rounded text-xs font-bold ${p === paginaActual ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}`;
                btn.onclick = () => { paginaActual = p; renderizarListado(); };
                btnsCont.appendChild(btn);
            }

            // Siguiente
            const btnNext = document.createElement('button');
            btnNext.innerHTML = '&raquo;';
            btnNext.className = `px-3 py-1 rounded text-xs font-bold ${paginaActual === totalPaginas ? 'bg-slate-100 text-slate-300 cursor-not-allowed' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}`;
            btnNext.disabled = paginaActual === totalPaginas;
            btnNext.onclick = () => { if (paginaActual < totalPaginas) { paginaActual++; renderizarListado(); } };
            btnsCont.appendChild(btnNext);
        }
    }

    // ====== EDITAR INSCRITO ======
    function abrirModalEditar(id) {
        const inscrito = datosListado.find(i => i.id === id);
        if (!inscrito) return;

        document.getElementById('editId').value = inscrito.id;
        document.getElementById('editNombres').value = inscrito.nombres || '';
        document.getElementById('editPrimerApellido').value = inscrito.primer_apellido || '';
        document.getElementById('editSegundoApellido').value = inscrito.segundo_apellido || '';
        document.getElementById('editCelular').value = inscrito.celular || '';
        document.getElementById('editCorreo').value = inscrito.correolec || '';
        document.getElementById('editEmergencia').value = inscrito.contacto_emergencia || '';
        document.getElementById('editServicio').value = inscrito.servicio_unidad || '';

        document.getElementById('modalEditarInscrito').classList.remove('hidden');
        document.getElementById('modalEditarInscrito').classList.add('flex');
    }

    function cerrarModalEditar() {
        document.getElementById('modalEditarInscrito').classList.add('hidden');
        document.getElementById('modalEditarInscrito').classList.remove('flex');
    }

    function guardarEdicion() {
        const id = document.getElementById('editId').value;
        const payload = {
            nombres: document.getElementById('editNombres').value,
            primer_apellido: document.getElementById('editPrimerApellido').value,
            segundo_apellido: document.getElementById('editSegundoApellido').value,
            celular: document.getElementById('editCelular').value,
            correolec: document.getElementById('editCorreo').value,
            contacto_emergencia: document.getElementById('editEmergencia').value,
            servicio_unidad: document.getElementById('editServicio').value,
        };

        fetch(`{{ url('/api/inscritos') }}/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Actualizar en memoria
                const idx = datosListado.findIndex(i => i.id == id);
                if (idx !== -1) {
                    Object.assign(datosListado[idx], payload);
                }
                cerrarModalEditar();
                // Refrescar listado filtrado
                document.getElementById('buscarListado').dispatchEvent(new Event('keyup'));
                alert('Inscrito actualizado correctamente.');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error al guardar los cambios.');
        });
    }

    // ====== ELIMINAR INSCRITO ======
    function eliminarInscrito(id) {
        if (!confirm('¿Está seguro que desea eliminar este inscrito? Esta acción no se puede deshacer.')) return;

        fetch(`{{ url('/api/inscritos') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                datosListado = datosListado.filter(i => i.id != id);
                listadoFiltrado = listadoFiltrado.filter(i => i.id != id);
                // También actualizar la pestaña de autorización
                todosInscritos = todosInscritos.filter(i => i.id != id);
                renderizarListado();
                alert('Inscrito eliminado correctamente.');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error al eliminar el inscrito.');
        });
    }

    // ====== EXPORTAR A EXCEL (.xlsx) ======
    function exportarExcel() {
        const datos = listadoFiltrado.length > 0 ? listadoFiltrado : datosListado;
        if (datos.length === 0) {
            alert('No hay datos para exportar.');
            return;
        }

        const headers = ['Nombre', 'Primer Apellido', 'Segundo Apellido', 'Edad', 'Celular', 'Correo', 'Contacto Emergencia', 'Servicio', 'Vinculación'];
        const filas = datos.map(i => [
            i.nombres || '',
            i.primer_apellido || '',
            i.segundo_apellido || '',
            i.edad != null ? i.edad : '',
            i.celular || '',
            i.correolec || '',
            i.contacto_emergencia || '',
            i.servicio_unidad || '',
            i.tipo_vinculacion || ''
        ]);

        const wsData = [headers, ...filas];
        const ws = XLSX.utils.aoa_to_sheet(wsData);

        // Ajustar ancho de columnas (mínimo 10 caracteres)
        ws['!cols'] = headers.map((_, idx) => ({
            wch: Math.max(10, headers[idx].length, ...filas.map(f => String(f[idx] || '').length)) + 2
        }));

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Inscritos');
        XLSX.writeFile(wb, 'listado_inscritos_gym_' + new Date().toISOString().slice(0, 10) + '.xlsx');
    }
</script>
@endif

@endsection


