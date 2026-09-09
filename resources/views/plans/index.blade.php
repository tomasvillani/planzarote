@extends('layouts.layout')

@section('title', 'Planes')

@section('content')

    <div class="container mt-4 mb-5">

        {{-- Buscador --}}
        <form id="searchForm"
              action="{{ url('/plans') }}"
              method="GET"
              class="d-flex justify-content-center">

            <input id="searchInput"
                   class="form-control me-2"
                   type="search"
                   name="q"
                   placeholder="¿Qué te apetece hacer hoy?"
                   aria-label="Buscar"
                   style="max-width: 500px;"
                   value="{{ old('q', $query) }}">

            <button class="btn btn-danger" type="submit">
                Buscar
            </button>
        </form>

    </div>

    {{-- Errores --}}
    @if($errors->any())
        <div class="container">
            <div class="alert alert-danger text-center">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif


    @if ($planes->count() > 0)

        <div class="container">

            <div class="row">

                @foreach ($planes as $plan)

                    <div class="col-12 col-sm-6 col-md-3 mb-4">

                        <div class="card h-100 shadow-sm">

                            {{-- Imagen --}}
                            <div style="height: 150px; overflow: hidden; display: flex; align-items: center; justify-content: center;">

                                <img src="{{ $plan->imagen
                                    ? asset('storage/' . $plan->imagen)
                                    : asset('img/default-plan.png') }}"
                                     alt="{{ $plan->nombre }}"
                                     style="height: 100%; width: 100%; object-fit: cover;">

                            </div>

                            {{-- Información --}}
                            <div class="card-body d-flex flex-column">

                                <h5 class="card-title text-center">
                                    {{ $plan->nombre }}
                                </h5>

                                <p class="card-text mb-2 text-center">
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $plan->ubicacion }}
                                </p>

                                <p class="card-text mb-2 text-center text-muted"
                                   style="font-size: 0.9rem;">

                                    <i class="bi bi-calendar-event"></i>
                                    {{ $plan->fecha->format('d/m/Y') }}

                                </p>

                                <p class="card-text mb-3 text-center text-muted"
                                   style="font-size: 0.9rem;">

                                    <i class="bi bi-clock"></i>
                                    {{ $plan->fecha->format('H:i') }}

                                </p>

                                <div class="mt-auto text-center">

                                    <a href="{{ route('plans.show', $plan) }}"
                                       class="btn btn-outline-danger btn-sm">
                                        Ver más
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

            {{-- Paginación --}}
            <div class="d-flex justify-content-center mt-4">

                {{ $planes->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}

            </div>

        </div>

    @else

        <div class="container mt-5">

            @if ($hasFilter)

                <p class="text-center fs-4">
                    No se encontraron planes para esta búsqueda.
                </p>

            @else

                <p class="text-center fs-4">
                    No se encontraron planes publicados.
                </p>

            @endif

        </div>

    @endif


    <script>
        document.getElementById('searchForm').addEventListener('submit', function(e) {

            const query = document.getElementById('searchInput').value.trim();

            if (!query) {
                e.preventDefault();
            }

        });
    </script>

@endsection