
@extends('layouts.app')

@section('title','Crear Vinculación')
@section('header','Crear Vinculación')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('config.vinculaciones.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" maxlength="150" required>
            </div>
            <button class="btn btn-primary">Crear</button>
            <a href="{{ route('config.vinculaciones.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
