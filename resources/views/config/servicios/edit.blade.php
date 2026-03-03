
@extends('layouts.app')

@section('title','Editar Servicio')
@section('header','Editar Servicio')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('config.servicios.update',$servicio->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $servicio->nombre) }}" maxlength="150" required>
            </div>
            <button class="btn btn-primary">Guardar</button>
            <a href="{{ route('config.servicios.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
