@extends('layouts.layout')

@section('title', 'Crear Nuevo Plan')

@section('content')

<div class="container my-5">

    <h2 class="mb-4">Crear Nuevo Plan</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('plans.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">
                Nombre del Plan
            </label>

            <input type="text"
                   name="nombre"
                   id="nombre"
                   class="form-control"
                   value="{{ old('nombre') }}"
                   required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">
                Descripción
            </label>

            <textarea name="descripcion"
                      id="descripcion"
                      class="form-control"
                      rows="5"
                      required>{{ old('descripcion') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="ubicacion" class="form-label">
                Ubicación
            </label>

            <input type="text"
                name="ubicacion"
                id="ubicacion"
                class="form-control"
                value="{{ old('ubicacion') }}"
                placeholder="Ej: Arrecife, Lanzarote"
                required>
        </div>

        <div class="mb-3">
            <label for="fecha" class="form-label">
                Fecha y hora
            </label>

            <input type="datetime-local"
                name="fecha"
                id="fecha"
                class="form-control"
                value="{{ old('fecha') }}"
                min="{{ now()->addDay()->startOfDay()->format('Y-m-d\TH:i') }}"
                required>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">
                Imagen del Plan (opcional)
            </label>

            <input type="file"
                   name="imagen"
                   id="imagen"
                   class="form-control"
                   accept="image/*">
        </div>

        <button type="submit"
                class="btn btn-danger">
            Publicar Plan
        </button>

        <a href="{{ route('myplans.index') }}"
           class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>

@endsection