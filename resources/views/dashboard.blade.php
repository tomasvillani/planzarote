@extends('layouts.layout')

@section('title', 'Panel de Usuario')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

            <div class="card shadow border-0">

                <div class="card-header bg-danger text-white text-center">
                    <h4 class="mb-0">Panel de Usuario</h4>
                </div>

                <div class="card-body text-center">

                    <h5 class="card-title">
                        ¡Hola, {{ Auth::user()->name }}!
                    </h5>

                    <p class="card-text">
                        Bienvenido a PLanzarote. ¿Qué quieres hacer?
                    </p>

                    <div class="d-grid gap-2 d-md-flex justify-content-center mt-4 flex-wrap">

                        {{-- Explorar planes --}}
                        <a href="{{ url('/plans') }}"
                           class="btn btn-outline-danger me-md-2 mb-2">
                            Explorar Planes
                        </a>

                        {{-- Mis planes --}}
                        <a href="{{ url('/myplans') }}"
                           class="btn btn-outline-secondary me-md-2 mb-2">
                            Mis Planes
                        </a>

                        {{-- Planes en los que participo --}}
                        <a href="{{ url('/myparticipations') }}"
                           class="btn btn-outline-success me-md-2 mb-2">
                            Mis Participaciones
                        </a>

                        {{-- Perfil --}}
                        <a href="{{ url('/profile') }}"
                           class="btn btn-outline-info me-md-2 mb-2">
                            Perfil
                        </a>

                        {{-- Cerrar sesión --}}
                        <form method="POST"
                              action="{{ route('logout') }}"
                              class="d-inline">
                            @csrf

                            <button type="submit"
                                    class="btn btn-outline-danger mb-2">
                                Cerrar Sesión
                            </button>
                        </form>

                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <small class="text-muted">
                    Descubre nuevos planes, crea los tuyos y disfruta de Lanzarote.
                </small>
            </div>

        </div>
    </div>
</div>
@endsection