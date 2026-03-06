@extends('layouts.app')

@section('title', 'Listados - Bienestar')
@section('header', 'Listados de Bienestar')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Listados</h4>
                <p class="text-muted">Esta es una vista placeholder para la sección <strong>Listados</strong> bajo el menú Bienestar. Añade aquí los listados que necesites (inscritos, reportes, exportaciones, etc.).</p>

                <!-- Ejemplo de enlaces rápidos -->
                <div class="list-group">
                    <a href="{{ url('/bienestar/gym/inscripcion') }}" class="list-group-item list-group-item-action">Ir a Inscripción GYM</a>
                    <a href="{{ url('/bienestar/gym/agenda') }}" class="list-group-item list-group-item-action">Ir a Agenda GYM</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
