
@extends('layouts.app')

@section('title','Editar Vinculación')
@section('header','Editar Vinculación')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('config.vinculaciones.update',$vinculacion->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $vinculacion->nombre) }}" maxlength="150" required>
            </div>
            <button class="btn btn-primary">Guardar</button>
            <a href="{{ route('config.vinculaciones.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
