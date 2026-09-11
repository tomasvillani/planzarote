@extends('layouts.layout')

@section('title', $plan->nombre)

@section('content')

<div class="container my-5">

@if($errors->any())
    <div class="alert alert-danger text-center">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

<div class="row">

    <div class="col-md-5">

        <img src="{{ $plan->imagen
            ? asset('storage/' . $plan->imagen)
            : asset('img/default-plan.png') }}"
             alt="{{ $plan->nombre }}"
             class="img-fluid rounded">

    </div>

    <div class="col-md-7 d-flex flex-column justify-content-between">

        <div>

            <h2>{{ $plan->nombre }}</h2>

            <p>
                <i class="bi bi-person-circle"></i>
                <strong>Creado por:</strong>
                {{ $plan->user->name }}
            </p>

            <p>
                <i class="bi bi-geo-alt"></i>
                <strong>Ubicación:</strong>
                {{ $plan->ubicacion }}
            </p>

            <p>
                <i class="bi bi-calendar-event"></i>
                <strong>Fecha:</strong>
                {{ $plan->fecha->format('d/m/Y') }}
            </p>

            <p>
                <i class="bi bi-clock"></i>
                <strong>Hora:</strong>
                {{ $plan->fecha->format('H:i') }}
            </p>

            <p>
                <strong>Descripción:</strong><br>
                {{ $plan->descripcion ?? 'Sin descripción disponible.' }}
            </p>

            @if ($plan->participantes->count() > 0)
                <p>
                    <i class="bi bi-people"></i>
                    <strong>Participantes:</strong>
                    Se han apuntado {{ $plan->participantes->count() }} personas.
                </p>
            @endif

        </div>

        <div class="mt-4">

            @auth

                @if (auth()->id() === $plan->user_id)

                    <a href="{{ route('plans.participants', $plan) }}"
                    class="btn btn-secondary btn-lg w-100 mb-2">
                        Ver participantes
                    </a>

                @else

                    @if ($yaParticipa)

                        <button type="button"
                                class="btn btn-success btn-lg w-100 mb-2"
                                disabled>
                            Ya estás apuntado
                        </button>

                        <form action="{{ route('plans.leave', $plan) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-outline-danger btn-lg w-100"
                                    onclick="return confirm('¿Quieres salir de este plan?');">
                                Salir del plan
                            </button>
                        </form>

                    @else

                        <form action="{{ route('plans.join', $plan) }}"
                            method="POST">
                            @csrf

                            <button type="submit"
                                    class="btn btn-danger btn-lg w-100">
                                ¡Me apunto!
                            </button>
                        </form>

                    @endif

                @endif

            @else

                <a href="{{ route('login') }}"
                class="btn btn-danger btn-lg w-100">
                    ¡Me apunto!
                </a>

            @endauth

        </div>

    </div>

</div>

</div>

@endsection
