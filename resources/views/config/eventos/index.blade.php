@extends('layouts.app')

@section('title','Gestión de Eventos')
@section('header','Gestión de Eventos')

@push('head')
@vite(['resources/css/app.css'])
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="corporate-gradient rounded-3 shadow-lg mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-1 fw-bold">
                    <i class="fas fa-calendar-alt me-2"></i>Gestión de Eventos
                </h2>
                <p class="text-white-50 mb-0 small">Administra los eventos institucionales</p>
            </div>
            <button id="btnNuevoEvento" class="btn btn-light btn-lg shadow-sm hover-scale" data-toggle="modal" data-target="#modalEvento">
                <i class="fas fa-plus-circle me-2"></i>Nuevo Evento
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" id="searchEvento" class="form-control border-start-0" placeholder="Buscar por título, ubicación o descripción...">
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaEventos">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-bold small">#</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Título</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Ubicación</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Fecha Inicio</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Fecha Fin</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center">Capacidad</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center">Inscritos</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center">QR/Link</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center">Estado</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Evento con Tailwind CSS -->
<div class="modal fade" id="modalEvento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(1200px, 95vw); margin: 0.5rem auto;">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(46, 58, 117, 0.2);">
            <div class="modal-body p-0" style="max-height:90vh; overflow:auto;">

<!-- Contenido completo del modal -->
<div class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 h-full">

<div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0" id="confirmCancelModal" style="display:none;">
<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
<div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 shadow-2xl transition-all sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-800">
<div class="p-6">
<div class="flex items-center justify-center size-16 bg-red-50 dark:bg-red-900/20 rounded-full mx-auto mb-4">
<span class="material-symbols-outlined text-red-500 text-4xl">warning</span>
</div>
<div class="text-center">
<h3 class="text-xl font-bold text-slate-900 dark:text-slate-50 mb-2">¿Confirmar Cancelación?</h3>
<p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
Esta acción cancelará la cita del usuario de forma permanente. Al confirmar, <span class="font-semibold text-primary">se liberará el cupo</span> para que otro profesional pueda registrarse en este horario.
</p>
</div>
<div class="mt-8 flex flex-col gap-3">
<button class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-red-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-red-600/20 hover:bg-red-700 transition-all" type="button">
<span class="material-symbols-outlined text-sm">delete_forever</span>
Sí, Cancelar Cita
</button>
<button class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all" type="button" onclick="document.getElementById('confirmCancelModal').style.display='none'">
No, Mantener
</button>
</div>
<button class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" onclick="document.getElementById('confirmCancelModal').style.display='none'">
<span class="material-symbols-outlined">close</span>
</button>
</div>
</div>
</div>

<div class="relative flex h-auto w-full flex-col group/design-root">
<div class="layout-container flex h-full grow flex-col">
<!-- HEADER DEL MODAL -->
<header class="flex items-center justify-between flex-wrap gap-3 border-b border-solid border-slate-200 dark:border-slate-800 px-4 sm:px-6 md:px-10 py-3 sticky top-0 z-10 shadow-sm" style="background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%);">
<div class="flex items-center gap-3 min-w-0">
<div class="size-8 flex-shrink-0 flex items-center justify-center bg-white/20 rounded-lg text-white">
<span class="material-symbols-outlined">local_hospital</span>
</div>
<div class="flex flex-col min-w-0">
<h2 class="text-white text-sm sm:text-lg font-bold leading-tight tracking-[-0.015em] truncate">Hospital Universitario del Valle</h2>
<span class="text-[10px] uppercase tracking-wider font-bold text-slate-200">Panel Administrativo</span>
</div>
</div>
<div class="flex items-center gap-2 sm:gap-4 ml-auto">
<nav class="items-center gap-4 lg:gap-9 hidden lg:flex">
<a class="text-slate-200 text-sm font-medium hover:text-white transition-colors" href="#">Dashboard</a>
<a class="text-slate-200 text-sm font-medium hover:text-white transition-colors" href="#">Capacitaciones</a>
<a class="text-white text-sm font-bold border-b-2 border-white py-1" href="#">Horarios</a>
<a class="text-slate-200 text-sm font-medium hover:text-white transition-colors" href="#">Reportes</a>
</nav>
<div class="flex gap-2 lg:border-l lg:border-slate-400 lg:pl-4">
<button class="hidden sm:flex items-center justify-center rounded-lg h-10 w-10 bg-white/10 text-white hover:bg-white/20 transition-all">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="hidden sm:flex items-center justify-center rounded-lg h-10 w-10 bg-white/10 text-white hover:bg-white/20 transition-all">
<span class="material-symbols-outlined">settings</span>
</button>
<!-- Botón Cerrar Modal -->
<button type="button" class="flex items-center justify-center rounded-lg h-10 w-10 bg-red-500/20 text-white hover:bg-red-500/40 transition-all border border-red-500/30" data-dismiss="modal" aria-label="Close" title="Cerrar ventana">
    <span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="flex items-center gap-3 hidden md:flex">
<div class="text-right hidden sm:block">
<p class="text-xs font-bold text-white">Admin HUV</p>
<p class="text-[10px] text-slate-300">Superusuario</p>
</div>
<div class="bg-white/20 aspect-square rounded-full size-10 flex items-center justify-center text-white font-bold overflow-hidden border-2 border-white/50 shadow-sm">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnGPH7R9sRjLJcPNxwRUXT0NOzuWhZS4evZB6TLrxXqhyiNnT3ZCP-tGvSkT6zfMyF55VxNzMe-e29n1P-YT1WfPuHn-w6sSq20P1TzIC41Kw3CWUIMjRQQGK3n0t8nMeAneNUgbJdTiMWeHZau1Sgd5z4Ep1ssVYD1OUUSVeH7iUpr0TgVYnU515cttq4Uxm3cuGx3C_bbsIgWfJngVEOUx3dHViiQOrHHvlxN-S7t1DOw4nsbnSmVrSFwZOU1W36b0O9bnwFN54N"/>
</div>
</div>
</div>
</header>
<!-- FIN HEADER -->

<main class="flex flex-1 justify-center py-8 px-4 md:px-10">
    <form id="formEvento" class="contents">
        @csrf
        <input type="hidden" id="evento_id" name="id">
        <input type="hidden" id="evento_dias_activos" name="dias_activos">
        <input type="hidden" id="evento_franjas_horarias" name="franjas_horarias">

    <div class="layout-content-container flex flex-col max-w-[1024px] flex-1 gap-8">
        <div class="flex flex-col gap-2">
            <h1 id="modalEventoTituloHeader" class="text-slate-900 dark:text-slate-50 text-4xl font-black leading-tight tracking-[-0.033em]">Control Administrativo de Cupos HUV</h1>
            <p id="modalEventoDescripcionHeader" class="text-slate-500 dark:text-slate-400 text-base font-normal">Administre las jornadas, franjas horarias y el personal registrado en el sistema.</p>
        </div>

        <!-- SECCION DATOS GENERALES -->
        <section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-slate-900 dark:text-slate-50 text-xl font-bold leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">edit_note</span>
                    Datos del Evento
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Título del Evento <span class="text-red-500">*</span></label>
                    <input type="text" id="evento_titulo" name="titulo" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" required placeholder="Ingrese el título del evento">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Descripción</label>
                    <textarea id="evento_descripcion" name="descripcion" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" rows="2" placeholder="Descripción breve del evento"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Ubicación</label>
                    <input type="text" id="evento_ubicacion" name="ubicacion" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" placeholder="Lugar del evento">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Capacidad Total Estimada</label>
                    <input type="number" id="evento_capacidad" name="capacidad_maxima" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Fecha Inicio</label>
                    <input type="datetime-local" id="evento_fecha_inicio" name="fecha_inicio" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Fecha Fin</label>
                    <input type="datetime-local" id="evento_fecha_fin" name="fecha_fin" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary">
                </div>
                <!-- Límites de inscripción -->
                <div class="col-span-2 mt-2 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <h3 class="text-xs font-bold text-blue-700 dark:text-blue-300 mb-3 uppercase tracking-wide flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">policy</span>
                        Límites de Inscripción por Usuario
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Máx. inscripciones por día</label>
                            <select id="evento_max_dia" name="max_inscripciones_dia" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary text-sm">
                                <option value="0">Sin límite (ilimitado)</option>
                                <option value="1">1 vez por día</option>
                                <option value="2">2 veces por día</option>
                                <option value="3">3 veces por día</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-1">Cuántas franjas distintas puede reservar un usuario en un mismo día</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Máx. inscripciones por semana</label>
                            <select id="evento_max_semana" name="max_inscripciones_semana" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary text-sm">
                                <option value="0">Sin límite (ilimitado)</option>
                                <option value="1">1 vez por semana</option>
                                <option value="2">2 veces por semana</option>
                                <option value="3">3 veces por semana</option>
                                <option value="4">4 veces por semana</option>
                                <option value="5">5 veces por semana</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-1">Cuántas veces en total puede inscribirse un usuario durante la semana</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Botón guardar visible solo para esta sección si se desea, ayuda a UX -->
            <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-4 flex justify-end">
                 <button type="submit" class="text-sm font-bold text-primary hover:underline">Guardar Cambios</button>
            </div>
        </section>

<!-- SECCION CONFIGURACIÓN DE DÍAS Y FRANJAS HORARIAS (UNIFICADA) -->
<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
<div class="bg-slate-50 dark:bg-slate-800/50 p-6 border-b border-slate-200 dark:border-slate-800">
<div class="flex items-center justify-between">
<div>
<h2 class="text-slate-900 dark:text-slate-50 text-xl font-bold leading-tight flex items-center gap-2">
    <span class="material-symbols-outlined text-primary">calendar_month</span>
    Configuración de Días y Franjas Horarias
</h2>
<p class="text-slate-500 text-sm mt-1">Seleccione un día y configure sus franjas horarias. Cada día puede tener horarios independientes.</p>
</div>
<button type="button" class="text-red-500 text-sm font-semibold hover:underline flex items-center gap-1" id="btnRestablecerDias">
    <span class="material-symbols-outlined text-sm">restart_alt</span>
    Restablecer todo
</button>
</div>
</div>
<div class="p-6">
<!-- Pestañas de Días -->
<div class="flex gap-2 flex-wrap mb-6" id="diasTabs">
    @php
        $dias = [
            1 => 'Lunes', 
            2 => 'Martes', 
            3 => 'Miércoles', 
            4 => 'Jueves', 
            5 => 'Viernes', 
            6 => 'Sábado', 
            0 => 'Domingo'
        ];
    @endphp
    @foreach($dias as $num => $nombre)
        <button type="button" class="dia-tab-btn relative flex h-12 items-center justify-center gap-x-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 px-5 font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all border-2 border-transparent text-sm" data-dia="{{ $num }}">
            <span class="material-symbols-outlined text-sm dia-check hidden">check_circle</span>
            {{ $nombre }}
            <span class="dia-franja-count hidden ml-1 bg-primary/20 text-primary text-[10px] font-bold px-1.5 py-0.5 rounded-full">0</span>
        </button>
    @endforeach
</div>

<!-- Indicador de día seleccionado -->
<div id="diaSeleccionadoInfo" class="mb-4 hidden">
    <div class="flex items-center justify-between bg-primary/5 border border-primary/20 rounded-lg px-4 py-3">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">edit_calendar</span>
            <span class="text-sm font-bold text-primary">Editando franjas para: <span id="textoDiaActual" class="text-slate-900 dark:text-slate-50">-</span></span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="btnCopiarFranjas" class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all" title="Copiar las franjas de este día a otros días">
                <span class="material-symbols-outlined text-sm">content_copy</span>
                Copiar a otros días
            </button>
        </div>
    </div>
</div>

<!-- Contenedor de franjas del día seleccionado -->
<div id="franjasContainer" class="space-y-3">
    <div class="text-center py-8 text-slate-400">
        <span class="material-symbols-outlined text-4xl mb-2 block">touch_app</span>
        <p class="text-sm font-medium">Seleccione un día de la semana para configurar sus franjas horarias</p>
    </div>
</div>

<!-- Botón agregar franja (inicialmente oculto) -->
<button type="button" id="btnAgregarFranja" class="hidden mt-6 w-full flex items-center justify-center gap-2 py-4 border-2 border-dashed border-primary/30 rounded-xl text-primary font-bold hover:bg-primary/5 transition-all group">
<span class="material-symbols-outlined group-hover:scale-110 transition-transform">add_circle</span>
Agregar Franja Horaria
</button>

<!-- Resumen de configuración -->
<div id="resumenDias" class="mt-6 border-t border-slate-200 dark:border-slate-800 pt-4">
    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Resumen de configuración</h3>
    <div id="resumenDiasContenido" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
        <p class="text-slate-400 col-span-full text-center py-2">No hay días configurados aún</p>
    </div>
</div>
</div>
<div class="p-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex justify-between items-center">
<p class="text-sm text-slate-500">Los cambios se guardarán para el evento actual.</p>
<div class="flex gap-4">
<button type="button" data-dismiss="modal" class="px-6 py-2 rounded-lg font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
Cancelar
</button>
<button type="submit" class="bg-primary text-white px-8 py-2 rounded-lg font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all flex items-center gap-2">
<span class="material-symbols-outlined text-sm">save</span>
Guardar Evento
</button>
</div>
</div>
</section>

<!-- Modal Copiar Franjas -->
<div id="modalCopiarFranjas" class="hidden fixed inset-0 z-[9999] flex items-center justify-center" style="background:rgba(0,0,0,0.4);">
<div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 w-full max-w-sm">
    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-1">Copiar Franjas</h3>
    <p class="text-sm text-slate-500 mb-4">Seleccione los días a los que desea copiar las franjas del día actual. Se reemplazarán las franjas existentes.</p>
    <div id="copiarDiasCheckboxes" class="space-y-2 mb-4"></div>
    <div class="flex justify-end gap-2">
        <button type="button" id="btnCerrarCopiar" class="px-4 py-2 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-lg">Cancelar</button>
        <button type="button" id="btnConfirmarCopiar" class="px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-primary/90">Copiar</button>
    </div>
</div>
</div>

<!-- SECCION USUARIOS INSCRITOS -->
<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
<div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col gap-4 sm:gap-6">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
<div class="flex items-center gap-3">
<div class="bg-primary/10 p-2 rounded-lg text-primary flex-shrink-0">
<span class="material-symbols-outlined">group</span>
</div>
<div>
<h2 class="text-slate-900 dark:text-slate-50 text-lg sm:text-xl font-bold leading-tight">Usuarios Inscritos</h2>
<p class="text-slate-500 text-xs sm:text-sm">Visualización y control de asistencia por jornada</p>
</div>
</div>
<div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
<div class="relative w-full sm:w-auto">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
<input id="inputBuscarInscritos" oninput="filtrarYRenderInscritos()" class="pl-10 pr-4 py-2 w-full sm:w-56 md:w-72 rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary transition-all" placeholder="Buscar por Nombre o ID..." type="text"/>
</div>
        <button type="button" onclick="refreshInscritos()" class="flex items-center gap-2 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition-all shadow-sm text-sm">
            <span class="material-symbols-outlined text-lg">refresh</span>
            <span class="hidden sm:inline">Actualizar</span>
        </button>
        <button type="button" id="btnEliminarSeleccionados" onclick="eliminarInscritosSeleccionados()" class="hidden items-center gap-2 px-3 sm:px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition-all shadow-sm whitespace-nowrap text-sm">
            <span class="material-symbols-outlined text-lg">delete</span>
            <span class="hidden sm:inline">Eliminar</span> (<span id="countSeleccionados">0</span>)
        </button>
        <button class="flex items-center gap-2 px-3 sm:px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold transition-all shadow-sm text-sm">
<span class="material-symbols-outlined text-lg">download</span>
<span class="hidden sm:inline">Export to Excel</span>
</button>
</div>
</div>
<div class="flex flex-col md:flex-row gap-4">
<div class="flex-1">
<label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Seleccionar Día</label>
<div class="flex flex-wrap p-1 bg-slate-100 dark:bg-slate-800 rounded-lg w-fit gap-1" id="inscritosDiasContainer">
<span class="px-4 py-2 text-sm text-slate-400">Seleccione un evento para ver los días</span>
</div>
</div>
<div class="w-full md:w-64">
<label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Franja Horaria</label>
<select id="inscritosFranjaSelect" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary text-sm font-semibold">
<option value="">Todas las franjas</option>
</select>
</div>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-200 dark:border-slate-800">
<th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
    <input type="checkbox" id="checkAllInscritos" onchange="toggleAllInscritos(this)" class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer" title="Seleccionar todos"/>
</th>
<th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre Completo</th>
<th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Identificación</th>
<th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Servicio</th>
<th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha de Registro</th>
<th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Franja Horaria</th>
<th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-800" id="inscritosTbody"></tbody>
</table>
</div>
<div class="p-4 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center text-xs text-slate-500 font-medium border-t border-slate-200 dark:border-slate-800">
<span>Mostrando 2 de 15 usuarios registrados</span>
<div class="flex gap-2">
<button class="px-3 py-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded hover:bg-slate-50 transition-colors">Anterior</button>
<button class="px-3 py-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded hover:bg-slate-50 transition-colors">Siguiente</button>
</div>
</div>
</section>

<!-- SECCION ESTADISTICAS -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
<div class="bg-blue-50 dark:bg-primary/10 p-5 rounded-xl border border-primary/20 flex flex-col gap-2">
<span class="material-symbols-outlined text-primary">info</span>
<h4 class="font-bold text-primary">Capacidad Total</h4>
<p class="text-2xl font-black text-slate-900 dark:text-slate-50" id="statCapacidad">- Cupos</p>
<p class="text-xs text-slate-500">Configurados para el día seleccionado</p>
</div>
<div class="bg-green-50 dark:bg-green-900/10 p-5 rounded-xl border border-green-200 dark:border-green-900/20 flex flex-col gap-2">
<span class="material-symbols-outlined text-green-600">how_to_reg</span>
<h4 class="font-bold text-green-700 dark:text-green-500">Asistencia Registrada</h4>
<p class="text-2xl font-black text-slate-900 dark:text-slate-50" id="statAsistencia">-</p>
<p class="text-xs text-slate-500">Personal que ya marcó ingreso</p>
</div>
<div class="bg-amber-50 dark:bg-amber-900/10 p-5 rounded-xl border border-amber-200 dark:border-amber-900/20 flex flex-col gap-2">
<span class="material-symbols-outlined text-amber-600">groups</span>
<h4 class="font-bold text-amber-700 dark:text-amber-500">Inscritos Hoy</h4>
<p class="text-2xl font-black text-slate-900 dark:text-slate-100" id="statInscritos">- Pers.</p>
<p class="text-xs text-slate-500">Total global para esta jornada</p>
</div>
</div>
</div>
    </form>
</main>

<footer class="mt-auto py-6 sm:py-8 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 sm:px-6 md:px-10">
<div class="flex flex-col md:flex-row justify-between items-center gap-4 max-w-[1024px] mx-auto">
<div class="flex items-center gap-3">
<div class="size-6 bg-slate-200 dark:bg-slate-700 rounded flex items-center justify-center">
<span class="material-symbols-outlined text-xs text-slate-500">apartment</span>
</div>
<p class="text-xs text-slate-500">© 2024 Hospital Universitario del Valle "Evaristo García" E.S.E. - Gestión de Talento Humano</p>
</div>
<div class="flex gap-6">
<a class="text-xs text-slate-400 hover:text-primary transition-colors" href="#">Política de Privacidad</a>
<a class="text-xs text-slate-400 hover:text-primary transition-colors" href="#">Soporte Técnico</a>
<a class="text-xs text-slate-400 hover:text-primary transition-colors" href="#">Ayuda</a>
</div>
</div>
</footer>
</div>
</div>

</div>
<!-- Fin contenido modal -->
        </div>
    </div>
</div>

<!-- Modal para mostrar QR Code -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="z-index: 100000;">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(46, 58, 117, 0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%); border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="qrModalLabel" style="color: white; font-weight: 600;">
                    <i class="fas fa-qrcode mr-2"></i>Código QR del Evento
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" style="padding: 2rem;">
                <h6 id="qrEventoTitulo" style="color: #2e3a75; font-weight: 600; margin-bottom: 1.5rem;"></h6>
                <div id="qrCodeContainer" style="display: inline-block; padding: 1rem; background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"></div>
                <div class="mt-3">
                    <div class="input-group">
                        <input type="text" id="eventoLinkInput" class="form-control" readonly style="border-radius: 8px 0 0 8px; border: 2px solid #e8eaf0;">
                        <button class="btn btn-outline-secondary" type="button" id="copyLinkBtn" style="border-radius: 0 8px 8px 0; border: 2px solid #e8eaf0; border-left: none;">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <small class="text-muted mt-2 d-block">Comparte este enlace para que los usuarios se inscriban</small>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8f9fc;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" id="downloadQRBtn" class="btn" style="background: #2e3a75; color: white;">
                    <i class="fas fa-download mr-2"></i>Descargar QR
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiDataUrl = "{{ route('config.eventos.data') }}";
    const storeUrl = "{{ route('config.eventos.store') }}";
    const baseUrl = "{{ url('configuracion/eventos') }}";
    const token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    
    const tbody = document.querySelector('#tablaEventos tbody');
    const searchInput = document.getElementById('searchEvento');
    
    let eventosData = [];
    // franjasPorDia: { "1": [{hora_inicio, hora_fin, capacidad_maxima}, ...], "2": [...], ... }
    let franjasPorDia = {};
    let diaActualSeleccionado = null; // día tab actualmente activo para editar franjas
    
    // Cargar datos
    async function loadData() {
        try {
            const res = await fetch(apiDataUrl);
            eventosData = await res.json();
            renderTable(eventosData);
        } catch(e) {
            console.error('Error loading data:', e);
        }
    }
    
    // Renderizar tabla
    function renderTable(eventos) {
        tbody.innerHTML = '';
        eventos.forEach(evento => {
            const fechaInicio = new Date(evento.fecha_inicio);
            const fechaFin = new Date(evento.fecha_fin);
            const now = new Date();
            
            let estadoBadge = '';
            if (now < fechaInicio) {
                estadoBadge = '<span class="badge bg-info">Próximo</span>';
            } else if (now > fechaFin) {
                estadoBadge = '<span class="badge bg-secondary">Finalizado</span>';
            } else {
                estadoBadge = '<span class="badge bg-success">En Curso</span>';
            }
            
            const porcentaje = evento.capacidad_maxima > 0 ? Math.round((evento.inscritos / evento.capacidad_maxima) * 100) : 0;
            
            // Generar URL pública (asumida)
            const publicUrl = "{{ url('/eventos/inscripcion') }}" + '/' + evento.id;

            const tr = document.createElement('tr');
            tr.className = 'border-bottom';
            tr.innerHTML = `
                <td class="px-4 py-3">
                    <span class="badge bg-light text-dark">${evento.id}</span>
                </td>
                <td class="px-4 py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-calendar text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">${evento.titulo}</div>
                            ${evento.descripcion ? `<small class="text-muted">${evento.descripcion.substring(0, 50)}...</small>` : ''}
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <small class="text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>${evento.ubicacion || 'No especificada'}
                    </small>
                </td>
                <td class="px-4 py-3">
                    <small>${fechaInicio.toLocaleString('es-CO')}</small>
                </td>
                <td class="px-4 py-3">
                    <small>${fechaFin.toLocaleString('es-CO')}</small>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="badge bg-primary">${evento.capacidad_maxima}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div>
                        <span class="badge ${porcentaje >= 100 ? 'bg-danger' : porcentaje >= 80 ? 'bg-warning' : 'bg-success'}">${evento.inscritos}</span>
                        <div class="progress mt-1" style="height: 4px;">
                            <div class="progress-bar ${porcentaje >= 100 ? 'bg-danger' : porcentaje >= 80 ? 'bg-warning' : 'bg-success'}" style="width: ${Math.min(porcentaje, 100)}%"></div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-center">
                    <button class="btn btn-sm btn-outline-dark btn-qr" 
                            data-titulo="${evento.titulo}" 
                            data-url="${publicUrl}"
                            title="Ver Código QR">
                        <i class="fas fa-qrcode"></i>
                    </button>
                </td>
                <td class="px-4 py-3 text-center">${estadoBadge}</td>
                <td class="px-4 py-3 text-center">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary btn-edit" data-id="${evento.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        @if(auth()->check() && !in_array(auth()->user()->role, ['Operador', 'Administrador']))
                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${evento.id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        @endif
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    // Búsqueda
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const filtered = eventosData.filter(e => 
            (e.titulo || '').toLowerCase().includes(query) ||
            (e.descripcion || '').toLowerCase().includes(query) ||
            (e.ubicacion || '').toLowerCase().includes(query)
        );
        renderTable(filtered);
    });
    
    const nombresDias = {
        1: 'Lunes', 2: 'Martes', 3: 'Miércoles', 
        4: 'Jueves', 5: 'Viernes', 6: 'Sábado', 0: 'Domingo'
    };
    const ordenDias = [1, 2, 3, 4, 5, 6, 0];

    // ===== NUEVA LÓGICA DE DÍAS + FRANJAS POR DÍA =====

    // Guardar las franjas del día actualmente visible en franjasPorDia
    function guardarFranjasDiaActual() {
        if (diaActualSeleccionado === null) return;
        const franjas = [];
        document.querySelectorAll('#franjasContainer .franja-item').forEach(item => {
            const inicio = item.querySelector('.franja-inicio').value;
            const fin = item.querySelector('.franja-fin').value;
            const capacidad = item.querySelector('.franja-capacidad').value;
            if (inicio && fin && capacidad) {
                franjas.push({ hora_inicio: inicio, hora_fin: fin, capacidad_maxima: parseInt(capacidad) });
            }
        });
        const key = String(diaActualSeleccionado);
        if (franjas.length > 0) {
            franjasPorDia[key] = franjas;
        } else {
            delete franjasPorDia[key];
        }
        actualizarHiddenInputs();
        actualizarEstadoTabs();
        actualizarResumen();
    }

    // Renderizar las franjas de un día en el contenedor
    function renderFranjasDia(dia) {
        const container = document.getElementById('franjasContainer');
        container.innerHTML = '';
        const franjas = franjasPorDia[String(dia)] || [];
        if (franjas.length === 0) {
            container.innerHTML = '<p class="text-center text-slate-400 py-4 text-sm">No hay franjas horarias para este día. Agregue una con el botón de abajo.</p>';
        } else {
            franjas.forEach((franja, idx) => {
                container.insertAdjacentHTML('beforeend', crearFranjaHtml(franja.hora_inicio, franja.hora_fin, franja.capacidad_maxima));
            });
        }
    }

    // HTML de una franja
    function crearFranjaHtml(horaInicio, horaFin, capacidad) {
        const franjaId = Date.now() + Math.random();
        return `
            <div class="franja-item grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 md:gap-4 items-end bg-slate-50 dark:bg-slate-800/30 p-3 sm:p-4 rounded-lg border border-slate-100 dark:border-slate-800" data-franja-id="${franjaId}">
                <div class="sm:col-span-1 md:col-span-3">
                    <label class="block text-xs font-bold text-slate-500 mb-1 sm:mb-2 uppercase tracking-wide">Hora Inicio</label>
                    <input class="franja-inicio w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" type="time" value="${horaInicio || ''}" required/>
                </div>
                <div class="sm:col-span-1 md:col-span-3">
                    <label class="block text-xs font-bold text-slate-500 mb-1 sm:mb-2 uppercase tracking-wide">Hora Fin</label>
                    <input class="franja-fin w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" type="time" value="${horaFin || ''}" required/>
                </div>
                <div class="sm:col-span-1 md:col-span-4">
                    <label class="block text-xs font-bold text-slate-500 mb-1 sm:mb-2 uppercase tracking-wide">Capacidad Máxima</label>
                    <input class="franja-capacidad w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" type="number" value="${capacidad || ''}" min="1" required/>
                </div>
                <div class="sm:col-span-1 md:col-span-2 flex sm:justify-end">
                    <button type="button" class="btn-eliminar-franja flex items-center justify-center rounded-lg h-10 w-10 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                        <span class="material-symbols-outlined pointer-events-none">delete</span>
                    </button>
                </div>
            </div>
        `;
    }

    // Seleccionar un día tab
    function seleccionarDiaTab(dia) {
        // Guardar franjas del día anterior
        guardarFranjasDiaActual();
        diaActualSeleccionado = dia;

        // Actualizar estilos de tabs
        document.querySelectorAll('.dia-tab-btn').forEach(btn => {
            const d = parseInt(btn.dataset.dia);
            const hasFranjas = franjasPorDia[String(d)] && franjasPorDia[String(d)].length > 0;
            if (d === dia) {
                btn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400', 'bg-primary/10', 'text-primary', 'border-primary/30');
                btn.classList.add('bg-primary', 'text-white', 'shadow-md', 'shadow-primary/20', 'border-primary');
            } else if (hasFranjas) {
                btn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400', 'bg-primary', 'text-white', 'shadow-md', 'shadow-primary/20', 'border-primary');
                btn.classList.add('bg-primary/10', 'text-primary', 'border-primary/30');
            } else {
                btn.classList.remove('bg-primary', 'text-white', 'shadow-md', 'shadow-primary/20', 'border-primary', 'bg-primary/10', 'text-primary', 'border-primary/30');
                btn.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400');
            }
        });

        // Mostrar info y botón agregar
        document.getElementById('diaSeleccionadoInfo').classList.remove('hidden');
        document.getElementById('textoDiaActual').textContent = nombresDias[dia];
        document.getElementById('btnAgregarFranja').classList.remove('hidden');

        // Renderizar franjas del día
        renderFranjasDia(dia);
    }

    // Actualizar estado visual de todos los tabs
    function actualizarEstadoTabs() {
        document.querySelectorAll('.dia-tab-btn').forEach(btn => {
            const d = parseInt(btn.dataset.dia);
            const hasFranjas = franjasPorDia[String(d)] && franjasPorDia[String(d)].length > 0;
            const check = btn.querySelector('.dia-check');
            const count = btn.querySelector('.dia-franja-count');

            if (hasFranjas) {
                if (check) check.classList.remove('hidden');
                if (count) {
                    count.classList.remove('hidden');
                    count.textContent = franjasPorDia[String(d)].length;
                }
            } else {
                if (check) check.classList.add('hidden');
                if (count) count.classList.add('hidden');
            }

            // Solo aplicar estilos si no es el tab activo
            if (d !== diaActualSeleccionado) {
                if (hasFranjas) {
                    btn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400');
                    btn.classList.add('bg-primary/10', 'text-primary', 'border-primary/30');
                } else {
                    btn.classList.remove('bg-primary/10', 'text-primary', 'border-primary/30');
                    btn.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400');
                }
            }
        });
    }

    // Actualizar resumen visual
    function actualizarResumen() {
        const container = document.getElementById('resumenDiasContenido');
        if (!container) return;
        const diasConFranjas = ordenDias.filter(d => franjasPorDia[String(d)] && franjasPorDia[String(d)].length > 0);
        if (diasConFranjas.length === 0) {
            container.innerHTML = '<p class="text-slate-400 col-span-full text-center py-2">No hay días configurados aún</p>';
            return;
        }
        container.innerHTML = '';
        diasConFranjas.forEach(d => {
            const franjas = franjasPorDia[String(d)];
            const franjasTxt = franjas.map(f => `${f.hora_inicio}-${f.hora_fin}`).join(', ');
            container.innerHTML += `
                <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/30 rounded-lg px-3 py-2 border border-slate-100 dark:border-slate-800">
                    <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                    <div>
                        <span class="font-bold text-slate-700 dark:text-slate-200">${nombresDias[d]}</span>
                        <span class="text-slate-400 text-xs ml-1">(${franjas.length} franja${franjas.length > 1 ? 's' : ''})</span>
                        <p class="text-xs text-slate-500 mt-0.5">${franjasTxt}</p>
                    </div>
                </div>
            `;
        });
    }

    // Actualizar inputs hidden para el form
    function actualizarHiddenInputs() {
        // dias_activos: array de números de días que tienen franjas
        const diasConFranjas = ordenDias.filter(d => franjasPorDia[String(d)] && franjasPorDia[String(d)].length > 0);
        document.getElementById('evento_dias_activos').value = JSON.stringify(diasConFranjas);

        // franjas_horarias: array plano con dia_semana
        const todasFranjas = [];
        for (const [dia, franjas] of Object.entries(franjasPorDia)) {
            franjas.forEach(f => {
                todasFranjas.push({
                    dia_semana: parseInt(dia),
                    hora_inicio: f.hora_inicio,
                    hora_fin: f.hora_fin,
                    capacidad_maxima: f.capacidad_maxima
                });
            });
        }
        document.getElementById('evento_franjas_horarias').value = JSON.stringify(todasFranjas);
    }

    // Click en tabs de días
    document.getElementById('diasTabs').addEventListener('click', function(e) {
        const btn = e.target.closest('.dia-tab-btn');
        if (!btn) return;
        const dia = parseInt(btn.dataset.dia);
        seleccionarDiaTab(dia);
    });

    // Botón restablecer
    const btnRestablecerDias = document.getElementById('btnRestablecerDias');
    if (btnRestablecerDias) {
        btnRestablecerDias.addEventListener('click', async function() {
            const result = await Swal.fire({
                title: '¿Restablecer días y franjas?',
                text: 'Se eliminará toda la configuración actual de días y franjas. Esta acción no se guardará hasta no guardar el evento.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, restablecer',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'dark:bg-slate-800 dark:text-slate-100',
                    title: 'dark:text-slate-50',
                }
            });
            
            if (!result.isConfirmed) return;
            
            franjasPorDia = {};
            diaActualSeleccionado = null;
            document.getElementById('franjasContainer').innerHTML = '<div class="text-center py-8 text-slate-400"><span class="material-symbols-outlined text-4xl mb-2 block">touch_app</span><p class="text-sm font-medium">Seleccione un día de la semana para configurar sus franjas horarias</p></div>';
            document.getElementById('btnAgregarFranja').classList.add('hidden');
            document.getElementById('diaSeleccionadoInfo').classList.add('hidden');
            actualizarEstadoTabs();
            actualizarResumen();
            actualizarHiddenInputs();
            // Resetear estilos de tabs
            document.querySelectorAll('.dia-tab-btn').forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white', 'shadow-md', 'shadow-primary/20', 'border-primary', 'bg-primary/10', 'text-primary', 'border-primary/30');
                btn.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400');
                const check = btn.querySelector('.dia-check');
                const count = btn.querySelector('.dia-franja-count');
                if (check) check.classList.add('hidden');
                if (count) count.classList.add('hidden');
            });
        });
    }

    // Agregar franja al día actual
    const btnAgregarFranja = document.getElementById('btnAgregarFranja');
    if (btnAgregarFranja) {
        btnAgregarFranja.addEventListener('click', function() {
            if (diaActualSeleccionado === null) return;
            // Si es la primera franja, limpiar mensaje vacío
            const emptyMsg = document.querySelector('#franjasContainer > p');
            if (emptyMsg) emptyMsg.remove();
            document.getElementById('franjasContainer').insertAdjacentHTML('beforeend', crearFranjaHtml('', '', ''));
            guardarFranjasDiaActual();
        });
    }

    // Eliminar franja
    document.getElementById('franjasContainer').addEventListener('click', function(e) {
        if (e.target.closest('.btn-eliminar-franja')) {
            e.target.closest('.franja-item').remove();
            guardarFranjasDiaActual();
        }
    });
    
    // Auto-guardar franjas al editar valores
    document.getElementById('franjasContainer').addEventListener('input', function() {
        guardarFranjasDiaActual();
    });

    // Copiar franjas a otros días
    document.getElementById('btnCopiarFranjas').addEventListener('click', function() {
        if (diaActualSeleccionado === null) return;
        guardarFranjasDiaActual();
        const franjasSrc = franjasPorDia[String(diaActualSeleccionado)] || [];
        if (franjasSrc.length === 0) {
            Swal.fire({
                title: 'Día sin franjas',
                text: 'No hay franjas horarias configuradas en este día para copiar.',
                icon: 'info',
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'dark:bg-slate-800 dark:text-slate-100',
                    title: 'dark:text-slate-50',
                }
            });
            return;
        }
        
        const container = document.getElementById('copiarDiasCheckboxes');
        container.innerHTML = '';
        ordenDias.forEach(d => {
            if (d === diaActualSeleccionado) return;
            const hasFranjas = franjasPorDia[String(d)] && franjasPorDia[String(d)].length > 0;
            container.innerHTML += `
                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" value="${d}" class="copiar-dia-cb h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary"/>
                    <span class="text-sm font-medium text-slate-700">${nombresDias[d]}</span>
                    ${hasFranjas ? '<span class="text-xs text-amber-500 font-medium">(tiene franjas - se reemplazarán)</span>' : ''}
                </label>
            `;
        });
        document.getElementById('modalCopiarFranjas').classList.remove('hidden');
    });

    document.getElementById('btnCerrarCopiar').addEventListener('click', function() {
        document.getElementById('modalCopiarFranjas').classList.add('hidden');
    });

    document.getElementById('btnConfirmarCopiar').addEventListener('click', function() {
        const franjasSrc = franjasPorDia[String(diaActualSeleccionado)] || [];
        document.querySelectorAll('.copiar-dia-cb:checked').forEach(cb => {
            const targetDia = cb.value;
            franjasPorDia[targetDia] = JSON.parse(JSON.stringify(franjasSrc));
        });
        document.getElementById('modalCopiarFranjas').classList.add('hidden');
        actualizarEstadoTabs();
        actualizarResumen();
        actualizarHiddenInputs();
    });

    // Limpiar modal al crear nuevo
    document.getElementById('btnNuevoEvento').addEventListener('click', function() {
        document.getElementById('formEvento').reset();
        document.getElementById('evento_id').value = '';
        if(document.getElementById('inscritosTbody')) document.getElementById('inscritosTbody').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-slate-500">Ningún usuario inscrito (Evento Nuevo)</td></tr>';
        // Resetear sección inscritos
        allInscripciones = [];
        allFranjas = [];
        selectedDiaFilter = null;
        selectedFranjaFilter = '';
        const diasContainer = document.getElementById('inscritosDiasContainer');
        if(diasContainer) diasContainer.innerHTML = '<span class="px-4 py-2 text-sm text-slate-400">Seleccione un evento para ver los días</span>';
        const franjaSelect = document.getElementById('inscritosFranjaSelect');
        if(franjaSelect) franjaSelect.innerHTML = '<option value="">Todas las franjas</option>';
        
        const modalTitle = document.getElementById('modalEventoTitle');
        if (modalTitle) modalTitle.textContent = 'Nuevo Evento';
        
        const modalHeader = document.getElementById('modalEventoTituloHeader');
        if(modalHeader) modalHeader.textContent = 'Nuevo Evento';
        const modalDesc = document.getElementById('modalEventoDescripcionHeader');
        if(modalDesc) modalDesc.textContent = 'Complete la información para crear un nuevo evento.';
        
        const imgText = document.getElementById('imagenActualText');
        if (imgText) imgText.textContent = '';
        
        // Limpiar días y franjas
        franjasPorDia = {};
        diaActualSeleccionado = null;
        document.getElementById('franjasContainer').innerHTML = '<div class="text-center py-8 text-slate-400"><span class="material-symbols-outlined text-4xl mb-2 block">touch_app</span><p class="text-sm font-medium">Seleccione un día de la semana para configurar sus franjas horarias</p></div>';
        document.getElementById('btnAgregarFranja').classList.add('hidden');
        document.getElementById('diaSeleccionadoInfo').classList.add('hidden');
        document.getElementById('evento_dias_activos').value = '[]';
        document.getElementById('evento_franjas_horarias').value = '[]';
        
        // Resetear tabs
        document.querySelectorAll('.dia-tab-btn').forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white', 'shadow-md', 'shadow-primary/20', 'border-primary', 'bg-primary/10', 'text-primary', 'border-primary/30');
            btn.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400');
            btn.style.background = '';
            btn.style.color = '';
            btn.style.borderColor = '';
            const check = btn.querySelector('.dia-check');
            const count = btn.querySelector('.dia-franja-count');
            if (check) check.classList.add('hidden');
            if (count) count.classList.add('hidden');
        });
        actualizarResumen();
    });
    
    // Enviar formulario
    document.getElementById('formEvento').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Guardar franjas del día actual antes de enviar
        guardarFranjasDiaActual();
        actualizarHiddenInputs();
        
        const formData = new FormData(this);
        const id = formData.get('id');
        let url = storeUrl;
        let method = 'POST';
        
        if (id) {
            url = `${baseUrl}/${id}`;
            formData.append('_method', 'PUT');
        }
        
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await res.json();
            
            if (res.ok && data.success !== false) {
                $('#modalEvento').modal('hide');
                loadData();
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Evento guardado correctamente',
                    confirmButtonColor: '#2e3a75'
                });
            } else {
                let errorMsg = data.message || 'Error desconocido';
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0];
                    if (firstError && firstError.length > 0) {
                        errorMsg += '\\n' + firstError[0];
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar: ' + errorMsg,
                    confirmButtonColor: '#2e3a75'
                });
            }
        } catch(e) {
            console.error('Error:', e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar. Por favor, intenta nuevamente.',
                confirmButtonColor: '#2e3a75'
            });
        }
    });
    
    // Editar, eliminar y ver QR
    tbody.addEventListener('click', async function(e) {
        const editBtn = e.target.closest('.btn-edit');
        const deleteBtn = e.target.closest('.btn-delete');
        const qrBtn = e.target.closest('.btn-qr');
        
        // Manejar botón QR
        if (qrBtn) {
            // Prevenir propagación si es necesario
            e.stopPropagation();
            
            const titulo = qrBtn.dataset.titulo;
            const url = qrBtn.dataset.url;
            
            document.getElementById('qrEventoTitulo').textContent = titulo;
            document.getElementById('eventoLinkInput').value = url;
            
            // Asegurar que el modal esté en el body para evitar conflictos de z-index
            if ($('#qrModal').parent()[0] !== document.body) {
                $('#qrModal').appendTo('body');
            }
            
            // Generar QR
            const container = document.getElementById('qrCodeContainer');
            container.innerHTML = '';
            
            setTimeout(() => {
                new QRCode(container, {
                    text: url,
                    width: 200,
                    height: 200,
                    colorDark : "#2e3a75",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
                $('#qrModal').modal('show');
            }, 100);
            
            return;
        }
        
        if (editBtn) {
            const id = editBtn.dataset.id;
            try {
                const res = await fetch(`${baseUrl}/${id}`);
                const evento = await res.json();
                
                document.getElementById('evento_id').value = evento.id;
                document.getElementById('evento_titulo').value = evento.titulo || '';
                document.getElementById('evento_descripcion').value = evento.descripcion || '';
                document.getElementById('evento_ubicacion').value = evento.ubicacion || '';
                document.getElementById('evento_capacidad').value = evento.capacidad_maxima || '';
                document.getElementById('evento_max_dia').value = evento.max_inscripciones_dia || 0;
                document.getElementById('evento_max_semana').value = evento.max_inscripciones_semana || 0;

                // Actualizar título dinámico del modal
                const modalHeader = document.getElementById('modalEventoTituloHeader');
                if(modalHeader) modalHeader.textContent = evento.titulo;
                const modalDesc = document.getElementById('modalEventoDescripcionHeader');
                if(modalDesc) modalDesc.textContent = evento.descripcion ? evento.descripcion.substring(0, 100) + '...' : 'Editando evento: ' + evento.titulo;
                // Fin actualización
                
                if (evento.fecha_inicio) {
                    const fi = new Date(evento.fecha_inicio);
                    document.getElementById('evento_fecha_inicio').value = fi.toISOString().slice(0, 16);
                }
                if (evento.fecha_fin) {
                    const ff = new Date(evento.fecha_fin);
                    document.getElementById('evento_fecha_fin').value = ff.toISOString().slice(0, 16);
                }
                
                // Cargar días y franjas por día
                franjasPorDia = {};
                diaActualSeleccionado = null;

                // Reconstruir franjasPorDia desde evento_franjas (con dia_semana) o franjas_horarias legacy
                const eventoFranjas = evento.evento_franjas || [];
                const franjasLegacy = evento.franjas_horarias || [];

                if (eventoFranjas.length > 0 && eventoFranjas[0].dia_semana !== null && eventoFranjas[0].dia_semana !== undefined) {
                    // Nuevo formato: franjas con dia_semana desde tabla relacional
                    eventoFranjas.forEach(f => {
                        const key = String(f.dia_semana);
                        if (!franjasPorDia[key]) franjasPorDia[key] = [];
                        franjasPorDia[key].push({
                            hora_inicio: f.hora_inicio,
                            hora_fin: f.hora_fin,
                            capacidad_maxima: f.capacidad_maxima
                        });
                    });
                } else if (franjasLegacy.length > 0) {
                    // Legacy: franjas sin dia_semana, asignar a cada día activo
                    const diasAct = evento.dias_activos || [];
                    if (franjasLegacy[0] && franjasLegacy[0].dia_semana !== undefined) {
                        // franjas_horarias JSON ya tiene dia_semana
                        franjasLegacy.forEach(f => {
                            const key = String(f.dia_semana);
                            if (!franjasPorDia[key]) franjasPorDia[key] = [];
                            franjasPorDia[key].push({
                                hora_inicio: f.hora_inicio,
                                hora_fin: f.hora_fin,
                                capacidad_maxima: f.capacidad_maxima
                            });
                        });
                    } else {
                        // Old format: same franjas for all active days
                        diasAct.forEach(d => {
                            franjasPorDia[String(d)] = franjasLegacy.map(f => ({
                                hora_inicio: f.hora_inicio,
                                hora_fin: f.hora_fin,
                                capacidad_maxima: f.capacidad_maxima
                            }));
                        });
                    }
                }

                // Actualizar UI de tabs
                actualizarEstadoTabs();
                actualizarResumen();
                actualizarHiddenInputs();
                
                // Mostrar estado inicial del contenedor de franjas
                document.getElementById('franjasContainer').innerHTML = '<div class="text-center py-8 text-slate-400"><span class="material-symbols-outlined text-4xl mb-2 block">touch_app</span><p class="text-sm font-medium">Seleccione un día de la semana para configurar sus franjas horarias</p></div>';
                document.getElementById('btnAgregarFranja').classList.add('hidden');
                document.getElementById('diaSeleccionadoInfo').classList.add('hidden');
                
                // Auto-seleccionar primer día con franjas
                const primerDia = ordenDias.find(d => franjasPorDia[String(d)] && franjasPorDia[String(d)].length > 0);
                if (primerDia !== undefined) {
                    seleccionarDiaTab(primerDia);
                }
                
                const modalTitle = document.getElementById('modalEventoTitle');
                if(modalTitle) modalTitle.textContent = 'Editar Evento';
                
                if(modalHeader) modalHeader.textContent = 'Editar Evento';
                
                const imgText = document.getElementById('imagenActualText');
                if (evento.imagen && imgText) {
                    imgText.textContent = 'Imagen actual: ' + evento.imagen.split('/').pop();
                }
                
                if(typeof initSeccionInscritos === 'function') { initSeccionInscritos(evento); } $('#modalEvento').modal('show');
            } catch(e) {
                console.error('Error:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar el evento',
                    confirmButtonColor: '#2e3a75'
                });
            }
        }
        
        if (deleteBtn) {
            const result = await Swal.fire({
                title: '¿Está seguro?',
                text: '¿Está seguro de eliminar este evento? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#2e3a75',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            
            if (!result.isConfirmed) return;
            const id = deleteBtn.dataset.id;
            
            try {
                const res = await fetch(`${baseUrl}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ '_method': 'DELETE' })
                });
                
                const data = await res.json();
                if (data.success) {
                    loadData();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: 'Evento eliminado correctamente',
                        confirmButtonColor: '#2e3a75'
                    });
                }
            } catch(e) {
                console.error('Error:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al eliminar el evento',
                    confirmButtonColor: '#2e3a75'
                });
            }
        }
    });
    
    
    // Funciones del Modal QR
    document.getElementById('copyLinkBtn').addEventListener('click', function() {
        const linkInput = document.getElementById('eventoLinkInput');
        linkInput.select();
        linkInput.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        const originalHtml = this.innerHTML;
        this.innerHTML = '<i class="fas fa-check"></i>';
        this.classList.remove('btn-outline-secondary');
        this.classList.add('btn-success');
        
        setTimeout(() => {
            this.innerHTML = originalHtml;
            this.classList.remove('btn-success');
            this.classList.add('btn-outline-secondary');
        }, 2000);
    });
    
    document.getElementById('downloadQRBtn').addEventListener('click', function() {
        const img = document.querySelector('#qrCodeContainer img');
        if (img) {
            const link = document.createElement('a');
            link.download = 'QR_Evento_' + (document.getElementById('qrEventoTitulo').textContent || 'CABI') + '.png';
            link.href = img.src;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });

    // ===== SECCION: Usuarios Inscritos - Variables y lógica =====
    let allInscripciones = []; // Todas las inscripciones del evento actual
    let allFranjas = [];       // Todas las franjas del evento actual
    let selectedDiaFilter = null; // Día seleccionado para filtrar (fecha_reserva)
    let selectedFranjaFilter = ''; // Franja seleccionada para filtrar

    // Días de la semana ordenados Lunes→Domingo con su número JS (getDay: 0=Dom,1=Lun...)
    const diasSemana = [
        { dow: 1, nombre: 'Lunes' },
        { dow: 2, nombre: 'Martes' },
        { dow: 3, nombre: 'Miércoles' },
        { dow: 4, nombre: 'Jueves' },
        { dow: 5, nombre: 'Viernes' },
        { dow: 6, nombre: 'Sábado' },
        { dow: 0, nombre: 'Domingo' }
    ];

    // Poblar los botones de días en la sección Inscritos (Lun-Dom)
    function poblarDiasInscritos(evento) {
        const container = document.getElementById('inscritosDiasContainer');
        if (!container) return;
        container.innerHTML = '';
        selectedDiaFilter = null;

        const diasEvento = evento.dias_activos || [];
        if (diasEvento.length === 0) {
            container.innerHTML = '<span class="px-4 py-2 text-sm text-slate-400">No hay días configurados</span>';
            return;
        }

        // Botón "Todos" (seleccionado por defecto)
        const btnTodos = document.createElement('button');
        btnTodos.type = 'button';
        btnTodos.className = 'inscrito-dia-btn px-4 py-2 text-sm font-bold rounded-md bg-white dark:bg-slate-700 text-primary shadow-sm transition-colors';
        btnTodos.textContent = 'Todos';
        btnTodos.dataset.dia = '';
        container.appendChild(btnTodos);

        // Crear un botón por cada día de la semana que esté activo en el evento
        diasSemana.forEach(item => {
            if (!diasEvento.includes(item.dow)) return;
            // Contar inscritos de este día
            const count = allInscripciones.filter(ins => {
                if (!ins.fecha_reserva) return false;
                const d = new Date(ins.fecha_reserva + 'T12:00:00');
                return d.getDay() === item.dow;
            }).length;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'inscrito-dia-btn px-4 py-2 text-sm font-medium text-slate-500 hover:text-primary rounded-md transition-colors';
            btn.textContent = item.nombre + (count > 0 ? ` (${count})` : '');
            btn.dataset.dia = item.dow;
            container.appendChild(btn);
        });

        // Delegación de clic
        container.onclick = function(e) {
            const btn = e.target.closest('.inscrito-dia-btn');
            if (!btn) return;

            // Actualizar estilos
            container.querySelectorAll('.inscrito-dia-btn').forEach(b => {
                b.className = 'inscrito-dia-btn px-4 py-2 text-sm font-medium text-slate-500 hover:text-primary rounded-md transition-colors';
            });
            btn.className = 'inscrito-dia-btn px-4 py-2 text-sm font-bold rounded-md bg-white dark:bg-slate-700 text-primary shadow-sm transition-colors';

            selectedDiaFilter = btn.dataset.dia !== '' ? parseInt(btn.dataset.dia) : null;
            filtrarYRenderInscritos();
        };
    }

    // Poblar el select de franjas horarias
    function poblarFranjasInscritos(evento) {
        const select = document.getElementById('inscritosFranjaSelect');
        if (!select) return;
        select.innerHTML = '<option value="">Todas las franjas</option>';
        selectedFranjaFilter = '';

        allFranjas = evento.evento_franjas || [];
        allFranjas.forEach(franja => {
            const opt = document.createElement('option');
            opt.value = franja.id;
            // Contar inscritos de esta franja
            const count = allInscripciones.filter(i => parseInt(i.evento_franja_id) === parseInt(franja.id)).length;
            opt.textContent = `${franja.hora_inicio} - ${franja.hora_fin} (${count} inscritos)`;
            select.appendChild(opt);
        });

        select.onchange = function() {
            selectedFranjaFilter = this.value;
            filtrarYRenderInscritos();
        };
    }

    // Filtrar inscripciones según día de la semana y franja seleccionados
    function filtrarYRenderInscritos() {
        let filtered = [...allInscripciones];

        if (selectedDiaFilter !== null && selectedDiaFilter !== '') {
            const diaNum = typeof selectedDiaFilter === 'number' ? selectedDiaFilter : parseInt(selectedDiaFilter);
            filtered = filtered.filter(ins => {
                if (!ins.fecha_reserva) return false;
                const d = new Date(ins.fecha_reserva + 'T12:00:00');
                return d.getDay() === diaNum;
            });
        }

        if (selectedFranjaFilter) {
            const franjaId = parseInt(selectedFranjaFilter);
            filtered = filtered.filter(ins => parseInt(ins.evento_franja_id) === franjaId);
        }

        const inputBs = document.getElementById('inputBuscarInscritos');
        if (inputBs && inputBs.value.trim() !== '') {
            const q = inputBs.value.toLowerCase().trim();
            filtered = filtered.filter(ins => {
                const doc = ins.identificacion ? ins.identificacion.toLowerCase() : '';
                const nom = ins.nombre_completo ? ins.nombre_completo.toLowerCase() : '';
                return doc.includes(q) || nom.includes(q);
            });
        }

        renderUsuariosInscritos(filtered);
    }

    window.refreshInscritos = async function() {
        const id = document.getElementById('evento_id').value;
        if(!id) return;
        const btn = document.querySelector('button[onclick="refreshInscritos()"]');
        if(btn) btn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">refresh</span> Actualizando...';
        try {
            const res = await fetch(baseUrl + '/' + id);
            const evento = await res.json();
            allInscripciones = evento.evento_inscripciones || [];
            poblarFranjasInscritos(evento);
            filtrarYRenderInscritos();
        } catch(e) { console.error(e); }
        if(btn) btn.innerHTML = '<span class="material-symbols-outlined text-lg">refresh</span> Actualizar';
    }

    // Inicializar sección inscritos completa cuando se carga un evento
    function initSeccionInscritos(evento) {
        allInscripciones = evento.evento_inscripciones || [];
        selectedDiaFilter = null;
        selectedFranjaFilter = '';
        poblarDiasInscritos(evento);
        poblarFranjasInscritos(evento);
        renderUsuariosInscritos(allInscripciones);
    }

    function renderUsuariosInscritos(inscripciones) {
        const tbody = document.getElementById("inscritosTbody");
        if(!tbody) return;
        tbody.innerHTML = "";
        
        let checked = 0;
        let total = inscripciones ? inscripciones.length : 0;
        
        const statCap = document.getElementById("statCapacidad");
        const statAsist = document.getElementById("statAsistencia");
        const statInsc = document.getElementById("statInscritos");
        
        const capModal = document.getElementById("evento_capacidad");
        if(statCap && capModal) statCap.textContent = capModal.value + " Cupos";
        if(statAsist) statAsist.textContent = "0 / " + total;
        if(statInsc) statInsc.textContent = total + " Pers.";
        
        // Reset checkbox maestro
        const checkAll = document.getElementById('checkAllInscritos');
        if(checkAll) checkAll.checked = false;
        actualizarBtnEliminar();

        if(!inscripciones || inscripciones.length === 0) {
            tbody.innerHTML = "<tr><td colspan=\"7\" class=\"px-6 py-4 text-center text-slate-500\">No hay inscritos" + (selectedDiaFilter ? " para el día seleccionado" : " en este evento") + "</td></tr>";
            return;
        }
        
        inscripciones.forEach(ins => {
            const initials = ins.nombre_completo.substring(0,2).toUpperCase();
            const dateStr = new Date(ins.created_at).toLocaleString("es-CO");
            if(ins.asistencia) checked++;
            
            // Resolver franja horaria: primero desde la relación cargada, después por lookup
            let franjaObj = ins.franja || null;
            
            // Fallback: si no tiene relación cargada pero sí tiene evento_franja_id, buscar en allFranjas
            if (!franjaObj && ins.evento_franja_id && allFranjas && allFranjas.length > 0) {
                const fid = parseInt(ins.evento_franja_id);
                franjaObj = allFranjas.find(f => parseInt(f.id) === fid) || null;
            }
            
            let franjaStr = "Sin franja";
            if(franjaObj) {
                const hIni = franjaObj.hora_inicio ? franjaObj.hora_inicio.substring(0,5) : '';
                const hFin = franjaObj.hora_fin ? franjaObj.hora_fin.substring(0,5) : '';
                const dias = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];
                const diaText = franjaObj.dia_semana !== null && franjaObj.dia_semana !== undefined && dias[franjaObj.dia_semana] ? dias[franjaObj.dia_semana] + " " : "";
                franjaStr = `${diaText}${hIni} - ${hFin}`;
            }
            
            tbody.innerHTML += `
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors inscrito-row" data-inscrito-id="${ins.id}">
                    <td class="px-6 py-4">
                        <input type="checkbox" class="check-inscrito h-4 w-4 rounded border-slate-300 text-red-500 focus:ring-red-400 cursor-pointer" value="${ins.id}" onchange="actualizarBtnEliminar()"/>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary">${initials}</div>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">${ins.nombre_completo}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">${ins.identificacion}</td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">N/A</td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">${dateStr}</td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                        <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-xs font-medium">${franjaStr}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button type="button" onclick="eliminarInscritoIndividual(${ins.id}, '${ins.nombre_completo.replace(/'/g, "\\'")}')"
                            class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Eliminar inscripción">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        if(statAsist) statAsist.textContent = checked + " / " + total;
    }

    // ========== FUNCIONES DE SELECCIÓN Y ELIMINACIÓN DE INSCRITOS ==========

    // Seleccionar/deseleccionar todos los checkboxes de inscritos
    window.toggleAllInscritos = function(masterCheckbox) {
        const checks = document.querySelectorAll('.check-inscrito');
        checks.forEach(cb => cb.checked = masterCheckbox.checked);
        actualizarBtnEliminar();
    }

    // Actualizar visibilidad y contador del botón "Eliminar seleccionados"
    window.actualizarBtnEliminar = function() {
        const checks = document.querySelectorAll('.check-inscrito:checked');
        const btn = document.getElementById('btnEliminarSeleccionados');
        const countEl = document.getElementById('countSeleccionados');
        if (!btn) return;
        if (checks.length > 0) {
            btn.classList.remove('hidden');
            btn.classList.add('flex');
            if (countEl) countEl.textContent = checks.length;
        } else {
            btn.classList.add('hidden');
            btn.classList.remove('flex');
        }
    }

    // Obtener CSRF token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    // Ejecutar eliminación vía API
    async function ejecutarEliminacion(payload) {
        try {
            const res = await fetch("{{ route('eventos.inscripciones.eliminar') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                showToast(data.message, 'success');
                await refreshInscritos();
            } else {
                showToast(data.message || 'Error al eliminar.', 'error');
            }
        } catch(e) {
            console.error(e);
            showToast('Error de conexión al eliminar.', 'error');
        }
    }

    // Eliminar inscripciones seleccionadas (checkboxes)
    window.eliminarInscritosSeleccionados = async function() {
        const checks = document.querySelectorAll('.check-inscrito:checked');
        if (checks.length === 0) return;

        const ids = Array.from(checks).map(cb => parseInt(cb.value));
        const eventoId = document.getElementById('evento_id').value;
        if (!eventoId) return;

        const result = await Swal.fire({
            title: '¿Eliminar seleccionados?',
            text: `¿Está seguro de eliminar ${ids.length} inscripción(es)? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'dark:bg-slate-800 dark:text-slate-100',
                title: 'dark:text-slate-50',
            }
        });

        if (!result.isConfirmed) return;

        ejecutarEliminacion({ evento_id: parseInt(eventoId), ids: ids });
    }

    // Eliminar una inscripción individual (botón de cada fila)
    window.eliminarInscritoIndividual = async function(id, nombre) {
        const eventoId = document.getElementById('evento_id').value;
        if (!eventoId) return;

        const result = await Swal.fire({
            title: '¿Eliminar inscripción?',
            html: `Va a eliminar la inscripción de <b>${nombre}</b>.<br>¿Desea continuar?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'dark:bg-slate-800 dark:text-slate-100',
                title: 'dark:text-slate-50',
            }
        });

        if (!result.isConfirmed) return;

        ejecutarEliminacion({ evento_id: parseInt(eventoId), ids: [id] });
    }

    // Función auxiliar de toast si no existe
    if (typeof window.showToast !== 'function') {
        window.showToast = function(msg, type = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: type === 'error' ? 'error' : (type === 'info' ? 'info' : 'success'),
                title: msg
            });
        }
    }

    // Inicializar
    loadData();
});
</script>
@endpush
