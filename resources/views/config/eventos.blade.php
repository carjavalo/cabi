@extends('layouts.app')

@section('title','Gestión de Eventos')
@section('header','Gestión de Eventos')

@push('head')
@vite(['resources/css/app.css'])
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Gestión de Eventos</h5>
            <button id="btnNuevoEvento" class="btn" style="background:var(--brand);color:#fff;" data-toggle="modal" data-target="#nuevoEventoModal">Nuevo Evento</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped" id="tablaEventos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Cupos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se cargarán los eventos -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Nuevo Evento (Control Administrativo de Cupos HUV) -->
<div class="modal fade" id="nuevoEventoModal" tabindex="-1" role="dialog" aria-labelledby="nuevoEventoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document" style="max-width:98%;">
    <div class="modal-content" style="border:none;">
      <div class="modal-body p-0" style="max-height:90vh; overflow-y:auto;">

<!-- Contenido completo del modal -->
<div class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">

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
<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-10 py-3">
<div class="flex items-center gap-4">
<div class="size-8 flex items-center justify-center bg-primary rounded-lg text-white">
<span class="material-symbols-outlined">local_hospital</span>
</div>
<div class="flex flex-col">
<h2 class="text-primary text-lg font-bold leading-tight tracking-[-0.015em]">Hospital Universitario del Valle</h2>
<span class="text-[10px] uppercase tracking-wider font-bold text-slate-500">Panel Administrativo</span>
</div>
</div>
<div class="flex flex-1 justify-end gap-8">
<nav class="flex items-center gap-9">
<a class="text-slate-600 dark:text-slate-300 text-sm font-medium hover:text-primary transition-colors" href="#">Dashboard</a>
<a class="text-slate-600 dark:text-slate-300 text-sm font-medium hover:text-primary transition-colors" href="#">Capacitaciones</a>
<a class="text-primary text-sm font-bold border-b-2 border-primary py-1" href="#">Horarios</a>
<a class="text-slate-600 dark:text-slate-300 text-sm font-medium hover:text-primary transition-colors" href="#">Reportes</a>
</nav>
<div class="flex gap-2 border-l border-slate-200 dark:border-slate-700 pl-6">
<button class="flex items-center justify-center rounded-lg h-10 w-10 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-primary/10 hover:text-primary transition-all">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="flex items-center justify-center rounded-lg h-10 w-10 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-primary/10 hover:text-primary transition-all">
<span class="material-symbols-outlined">settings</span>
</button>
</div>
<div class="flex items-center gap-3">
<div class="text-right hidden sm:block">
<p class="text-xs font-bold text-slate-900 dark:text-slate-100">Admin HUV</p>
<p class="text-[10px] text-slate-500">Superusuario</p>
</div>
<div class="bg-primary/20 aspect-square rounded-full size-10 flex items-center justify-center text-primary font-bold overflow-hidden border-2 border-white dark:border-slate-800 shadow-sm">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnGPH7R9sRjLJcPNxwRUXT0NOzuWhZS4evZB6TLrxXqhyiNnT3ZCP-tGvSkT6zfMyF55VxNzMe-e29n1P-YT1WfPuHn-w6sSq20P1TzIC41Kw3CWUIMjRQQGK3n0t8nMeAneNUgbJdTiMWeHZau1Sgd5z4Ep1ssVYD1OUUSVeH7iUpr0TgVYnU515cttq4Uxm3cuGx3C_bbsIgWfJngVEOUx3dHViiQOrHHvlxN-S7t1DOw4nsbnSmVrSFwZOU1W36b0O9bnwFN54N"/>
</div>
</div>
</div>
</header>

<main class="flex flex-1 justify-center py-8 px-4 md:px-10">
<div class="layout-content-container flex flex-col max-w-[1024px] flex-1 gap-8">
<div class="flex flex-col gap-2">
<h1 class="text-slate-900 dark:text-slate-50 text-4xl font-black leading-tight tracking-[-0.033em]">Control Administrativo de Cupos HUV</h1>
<p class="text-slate-500 dark:text-slate-400 text-base font-normal">Administre las jornadas, franjas horarias y el personal registrado en el sistema.</p>
</div>

<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
<div class="flex items-center justify-between mb-6">
<h2 class="text-slate-900 dark:text-slate-50 text-xl font-bold leading-tight flex items-center gap-2">
<span class="material-symbols-outlined text-primary">calendar_month</span>
Configuración de Días
</h2>
<button class="text-primary text-sm font-semibold hover:underline">Restablecer semana</button>
</div>
<div class="flex gap-3 flex-wrap">
<button class="flex h-12 items-center justify-center gap-x-2 rounded-lg bg-primary text-white px-6 font-semibold shadow-md shadow-primary/20 transition-all">
<span class="material-symbols-outlined text-sm">check_circle</span>
Lunes
</button>
<button class="flex h-12 items-center justify-center gap-x-2 rounded-lg bg-primary text-white px-6 font-semibold shadow-md shadow-primary/20 transition-all">
<span class="material-symbols-outlined text-sm">check_circle</span>
Martes
</button>
<button class="flex h-12 items-center justify-center gap-x-2 rounded-lg bg-primary text-white px-6 font-semibold shadow-md shadow-primary/20 transition-all">
<span class="material-symbols-outlined text-sm">check_circle</span>
Miércoles
</button>
<button class="flex h-12 items-center justify-center gap-x-2 rounded-lg bg-primary text-white px-6 font-semibold shadow-md shadow-primary/20 transition-all">
<span class="material-symbols-outlined text-sm">check_circle</span>
Jueves
</button>
<button class="flex h-12 items-center justify-center gap-x-2 rounded-lg bg-primary text-white px-6 font-semibold shadow-md shadow-primary/20 transition-all">
<span class="material-symbols-outlined text-sm">check_circle</span>
Viernes
</button>
<button class="flex h-12 items-center justify-center gap-x-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 px-6 font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
Sábado
</button>
<button class="flex h-12 items-center justify-center gap-x-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 px-6 font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
Domingo
</button>
</div>
</section>

<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
<div class="bg-slate-50 dark:bg-slate-800/50 p-6 border-b border-slate-200 dark:border-slate-800">
<div class="flex items-center justify-between">
<div>
<h2 class="text-slate-900 dark:text-slate-50 text-xl font-bold leading-tight">Gestor de Franjas Horarias</h2>
<p class="text-slate-500 text-sm mt-1">Configurando el horario para el día: <span class="text-primary font-bold">Lunes</span></p>
</div>
<div class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold uppercase">
Editando Jornada
</div>
</div>
</div>
<div class="p-6">
<div class="space-y-4">
<div class="grid grid-cols-12 gap-4 items-end bg-slate-50 dark:bg-slate-800/30 p-4 rounded-lg border border-slate-100 dark:border-slate-800">
<div class="col-span-3">
<label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Hora Inicio</label>
<input class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" type="time" value="08:00"/>
</div>
<div class="col-span-3">
<label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Hora Fin</label>
<input class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" type="time" value="10:00"/>
</div>
<div class="col-span-4">
<label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Capacidad Máxima</label>
<input class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" type="number" value="20"/>
</div>
<div class="col-span-2 flex justify-end">
<button class="flex items-center justify-center rounded-lg h-10 w-10 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</div>
<div class="grid grid-cols-12 gap-4 items-end bg-slate-50 dark:bg-slate-800/30 p-4 rounded-lg border border-slate-100 dark:border-slate-800">
<div class="col-span-3">
<label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Hora Inicio</label>
<input class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" type="time" value="10:30"/>
</div>
<div class="col-span-3">
<label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Hora Fin</label>
<input class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" type="time" value="12:30"/>
</div>
<div class="col-span-4">
<label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Capacidad Máxima</label>
<input class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary" type="number" value="20"/>
</div>
<div class="col-span-2 flex justify-end">
<button class="flex items-center justify-center rounded-lg h-10 w-10 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</div>
</div>
<button class="mt-8 w-full flex items-center justify-center gap-2 py-4 border-2 border-dashed border-primary/30 rounded-xl text-primary font-bold hover:bg-primary/5 transition-all group">
<span class="material-symbols-outlined group-hover:scale-110 transition-transform">add_circle</span>
Agregar Nueva Franja Horaria
</button>
</div>
<div class="p-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex justify-between items-center">
<p class="text-sm text-slate-500">Los cambios se aplicarán a todos los lunes del mes actual.</p>
<div class="flex gap-4">
<button class="px-6 py-2 rounded-lg font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
Cancelar
</button>
<button class="bg-primary text-white px-8 py-2 rounded-lg font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all flex items-center gap-2">
<span class="material-symbols-outlined text-sm">save</span>
Guardar Configuración
</button>
</div>
</div>
</section>

<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
<div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col gap-6">
<div class="flex items-center justify-between flex-wrap gap-4">
<div class="flex items-center gap-3">
<div class="bg-primary/10 p-2 rounded-lg text-primary">
<span class="material-symbols-outlined">group</span>
</div>
<div>
<h2 class="text-slate-900 dark:text-slate-50 text-xl font-bold leading-tight">Usuarios Inscritos</h2>
<p class="text-slate-500 text-sm">Visualización y control de asistencia por jornada</p>
</div>
</div>
<div class="flex items-center gap-3 ml-auto">
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
<input class="pl-10 pr-4 py-2 w-64 md:w-80 rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary transition-all" placeholder="Buscar por Nombre o ID..." type="text"/>
</div>
<button class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold transition-all shadow-sm">
<span class="material-symbols-outlined text-lg">download</span>
Export to Excel
</button>
</div>
</div>
<div class="flex flex-col md:flex-row gap-4">
<div class="flex-1">
<label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Seleccionar Día</label>
<div class="flex p-1 bg-slate-100 dark:bg-slate-800 rounded-lg w-fit">
<button class="px-4 py-2 text-sm font-bold rounded-md bg-white dark:bg-slate-700 text-primary shadow-sm">Lun 22</button>
<button class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-primary transition-colors">Mar 23</button>
<button class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-primary transition-colors">Mie 24</button>
<button class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-primary transition-colors">Jue 25</button>
<button class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-primary transition-colors">Vie 26</button>
</div>
</div>
<div class="w-full md:w-64">
<label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Franja Horaria</label>
<select class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary text-sm font-semibold">
<option>08:00 - 10:00 (15 inscritos)</option>
<option>10:30 - 12:30 (8 inscritos)</option>
<option>14:00 - 16:00 (12 inscritos)</option>
</select>
</div>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-200 dark:border-slate-800">
<th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre Completo</th>
<th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Identificación</th>
<th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Servicio</th>
<th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha de Registro</th>
<th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Asistencia</th>
<th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-800">
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="size-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">JR</div>
<span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Julian Restrepo</span>
</div>
</td>
<td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">1.114.567.890</td>
<td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">Urgencias</td>
<td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">15/05/2024 09:15</td>
<td class="px-6 py-4 text-center">
<input checked="" class="h-5 w-5 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="px-6 py-4">
<div class="flex justify-center">
<button class="flex items-center justify-center rounded-lg h-10 w-10 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all" title="Cancelar Cita" onclick="document.getElementById('confirmCancelModal').style.display='flex'">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="size-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">MG</div>
<span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Maria García</span>
</div>
</td>
<td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">29.456.123</td>
<td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">UCI Adultos</td>
<td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">15/05/2024 10:02</td>
<td class="px-6 py-4 text-center">
<input class="h-5 w-5 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="px-6 py-4">
<div class="flex justify-center">
<button class="flex items-center justify-center rounded-lg h-10 w-10 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all" title="Cancelar Cita" onclick="document.getElementById('confirmCancelModal').style.display='flex'">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</td>
</tr>
</tbody>
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

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
<div class="bg-blue-50 dark:bg-primary/10 p-5 rounded-xl border border-primary/20 flex flex-col gap-2">
<span class="material-symbols-outlined text-primary">info</span>
<h4 class="font-bold text-primary">Capacidad Total</h4>
<p class="text-2xl font-black text-slate-900 dark:text-slate-50">40 Cupos</p>
<p class="text-xs text-slate-500">Configurados para el día seleccionado</p>
</div>
<div class="bg-green-50 dark:bg-green-900/10 p-5 rounded-xl border border-green-200 dark:border-green-900/20 flex flex-col gap-2">
<span class="material-symbols-outlined text-green-600">how_to_reg</span>
<h4 class="font-bold text-green-700 dark:text-green-500">Asistencia Registrada</h4>
<p class="text-2xl font-black text-slate-900 dark:text-slate-50">12 / 15</p>
<p class="text-xs text-slate-500">Personal que ya marcó ingreso</p>
</div>
<div class="bg-amber-50 dark:bg-amber-900/10 p-5 rounded-xl border border-amber-200 dark:border-amber-900/20 flex flex-col gap-2">
<span class="material-symbols-outlined text-amber-600">groups</span>
<h4 class="font-bold text-amber-700 dark:text-amber-500">Inscritos Hoy</h4>
<p class="text-2xl font-black text-slate-900 dark:text-slate-100">35 Pers.</p>
<p class="text-xs text-slate-500">Total global para esta jornada</p>
</div>
</div>
</div>
</main>

<footer class="mt-auto py-8 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-10">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btnNuevoEvento');
        const modalEl = document.getElementById('nuevoEventoModal');
        if(!btn) return console.warn('btnNuevoEvento no encontrado');

        btn.addEventListener('click', function(e) {
            console.log('btnNuevoEvento clicked', { modalExists: !!modalEl, jQuery: typeof window.$ !== 'undefined' });
            try {
                if(typeof window.$ !== 'undefined' && typeof window.$.fn !== 'undefined' && typeof window.$.fn.modal === 'function'){
                    window.$('#nuevoEventoModal').modal('show');
                } else if (modalEl) {
                    // Fallback simple: toggle visibility classes
                    modalEl.style.display = 'block';
                    modalEl.classList.add('show');
                    document.body.classList.add('modal-open');
                    // add backdrop
                    let backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                } else {
                    console.error('Modal element no encontrado');
                    alert('Error: modal no encontrado en la página (ver consola).');
                }
            } catch(err) {
                console.error('Error al mostrar modal:', err);
                alert('Error al intentar abrir el modal. Revisa la consola para más detalles.');
            }
        });
    });
</script>
@endpush

@endsection
