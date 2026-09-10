@extends('layouts.layout')

@section('title', 'Editar Plan: ' . $plan->nombre)

@section('content')

<div class="container my-5">

<h2 class="mb-4">Editar Plan</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('plans.update', $plan) }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf
    @method('PATCH')

    {{-- Nombre --}}

    <div class="mb-3">

        <label for="nombre" class="form-label">
            Nombre del Plan
        </label>

        <input
            type="text"
            id="nombre"
            name="nombre"
            class="form-control @error('nombre') is-invalid @enderror"
            value="{{ old('nombre', $plan->nombre) }}"
            required
        >

        @error('nombre')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

    {{-- Descripción --}}

    <div class="mb-3">

        <label for="descripcion" class="form-label">
            Descripción
        </label>

        <textarea
            id="descripcion"
            name="descripcion"
            rows="5"
            class="form-control @error('descripcion') is-invalid @enderror"
            required
        >{{ old('descripcion', $plan->descripcion) }}</textarea>

        @error('descripcion')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

    {{-- Ubicación --}}

    <div class="mb-3">

        <label for="ubicacion" class="form-label">
            Ubicación
        </label>

        <input
            type="text"
            id="ubicacion"
            name="ubicacion"
            class="form-control @error('ubicacion') is-invalid @enderror"
            value="{{ old('ubicacion', $plan->ubicacion) }}"
            placeholder="Ej: Arrecife, Lanzarote"
            required
        >

        @error('ubicacion')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

    {{-- Fecha y hora --}}

    <div class="mb-3">

        <label for="fecha" class="form-label">
            Fecha y hora
        </label>

        <input
            type="datetime-local"
            id="fecha"
            name="fecha"
            class="form-control @error('fecha') is-invalid @enderror"
            value="{{ old('fecha', $plan->fecha->format('Y-m-d\TH:i')) }}"
            min="{{ now()->addDay()->startOfDay()->format('Y-m-d\TH:i') }}"
            required
        >

        @error('fecha')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

    {{-- Imagen --}}

    <div class="mb-3">

        <label for="imagen" class="form-label">
            Imagen (opcional)
        </label>

        <input
            type="file"
            id="imagen"
            name="imagen"
            class="form-control @error('imagen') is-invalid @enderror"
            accept="image/*"
        >

        @error('imagen')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

        @if ($plan->imagen)

            <div class="mt-3">

                <p>Imagen actual:</p>

                <img
                    src="{{ asset('storage/' . $plan->imagen) }}"
                    alt="{{ $plan->nombre }}"
                    style="max-width: 200px; height: auto; border-radius: 5px;"
                >

                <div class="form-check mt-2">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="eliminar_imagen"
                        id="eliminar_imagen"
                        value="1"
                    >

                    <label
                        class="form-check-label"
                        for="eliminar_imagen"
                    >
                        Eliminar imagen actual
                    </label>

                </div>

            </div>

        @endif

    </div>

    <button type="submit"
            class="btn btn-danger">
        Actualizar Plan
    </button>

    <a href="{{ route('myplans.index') }}"
       class="btn btn-secondary ms-2">
        Cancelar
    </a>

</form>

</div>

@endsection
