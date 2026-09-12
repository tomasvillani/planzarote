@extends('layouts.layout')

@section('title', 'Participantes - ' . $plan->nombre)

@section('content')

<div class="container my-5">

    <h2 class="mb-4">
        PARTICIPANTES DE {{ $plan->nombre }}
    </h2>

    @if ($participantes->count() > 0)

        <div class="list-group">

            @foreach ($participantes as $participante)

                <div class="list-group-item d-flex align-items-center">
                    <i class="bi bi-person-circle fs-4 me-3"></i>

                    <span>
                        {{ $participante->user->name }}
                    </span>
                </div>

            @endforeach

        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $participantes->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>

    @else

        <p class="text-center fs-5 mt-5">
            Todavía no hay personas apuntadas a este plan.
        </p>

    @endif

    <div class="mt-4">
        <a href="{{ route('plans.show', $plan) }}"
           class="btn btn-secondary">
            Volver al plan
        </a>
    </div>

</div>

@endsection