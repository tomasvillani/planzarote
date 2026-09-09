@extends('layouts.layout')

@section('title', 'Mis Planes')

@section('content')

<div class="container my-5">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar">
            </button>
        </div>
    @endif


    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Mis Planes</h2>

        <a href="{{ route('plans.create') }}"
           class="btn btn-danger">
            Crear Nuevo Plan
        </a>

    </div>


    @if ($planes->count() > 0)

        <div class="row">

            @foreach ($planes as $plan)

                <div class="col-12 col-sm-6 col-md-3 mb-4">

                    <div class="card h-100 shadow-sm">

                        {{-- Imagen --}}
                        @if ($plan->imagen)

                            <img src="{{ asset('storage/' . $plan->imagen) }}"
                                 class="card-img-top"
                                 alt="{{ $plan->nombre }}"
                                 style="object-fit: cover; height: 150px;">

                        @else

                            <img src="{{ asset('img/default-plan.png') }}"
                                 class="card-img-top"
                                 alt="Sin imagen"
                                 style="object-fit: cover; height: 150px;">

                        @endif


                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title text-center">
                                {{ $plan->nombre }}
                            </h5>

                            <p class="card-text text-center mb-2">
                                <i class="bi bi-geo-alt"></i>
                                {{ $plan->ubicacion }}
                            </p>

                            <p class="card-text text-center text-muted mb-3"
                               style="font-size: 0.9rem;">

                                <i class="bi bi-calendar-event"></i>
                                {{ $plan->fecha->format('d/m/Y') }}

                                <br>

                                <i class="bi bi-clock"></i>
                                {{ $plan->fecha->format('H:i') }}

                            </p>


                            <div class="mt-auto d-flex gap-2">

                                <a href="{{ route('plans.show', $plan) }}"
                                   class="btn btn-outline-danger btn-sm flex-fill">
                                    Ver
                                </a>

                                <a href="{{ route('plans.edit', $plan) }}"
                                   class="btn btn-outline-warning btn-sm flex-fill">
                                    Editar
                                </a>

                                <form action="{{ route('plans.destroy', $plan) }}"
                                      method="POST"
                                      class="flex-fill"
                                      onsubmit="return confirm('¿Estás seguro de eliminar este plan?');">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm w-100">
                                        Eliminar
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        <div class="d-flex justify-content-center mt-4">

            {{ $planes->onEachSide(1)->links('pagination::bootstrap-5') }}

        </div>


    @else

        <p class="text-center fs-5 mt-5">
            No has creado ningún plan todavía.
        </p>

        <div class="text-center">

            <a href="{{ route('plans.create') }}"
               class="btn btn-danger">
                Crear tu primer plan
            </a>

        </div>

    @endif

</div>

@endsection