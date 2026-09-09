@extends('layouts.layout')

@section('content')

<div id="planzaroteCarousel"
     class="carousel slide carousel-fixed"
     data-bs-ride="carousel">

<div class="carousel-indicators">

    <button type="button"
            data-bs-target="#planzaroteCarousel"
            data-bs-slide-to="0"
            class="active"
            aria-current="true"
            aria-label="Slide 1">
    </button>

    <button type="button"
            data-bs-target="#planzaroteCarousel"
            data-bs-slide-to="1"
            aria-label="Slide 2">
    </button>

    <button type="button"
            data-bs-target="#planzaroteCarousel"
            data-bs-slide-to="2"
            aria-label="Slide 3">
    </button>

</div>


<div class="carousel-inner">

    <!-- Slide 1 -->
    <div class="carousel-item active">

        <img src="{{ asset('img/carrusel-1.jpg') }}"
             class="d-block w-100"
             alt="Personas disfrutando de un plan">

        <div class="carousel-caption bg-dark bg-opacity-50 rounded px-3 py-2 text-center text-white"
             style="bottom: 20px;">

            <h5 class="fs-5 fs-md-4">
                Descubre tu próximo plan
            </h5>

            <p class="mb-2 fs-6 fs-md-5">
                Encuentra planes que encajen contigo y descubre nuevas formas de disfrutar Lanzarote.
            </p>

        </div>

    </div>


    <!-- Slide 2 -->
    <div class="carousel-item">

        <img src="{{ asset('img/carrusel-2.jpg') }}"
             class="d-block w-100"
             alt="Personas compartiendo un plan">

        <div class="carousel-caption bg-dark bg-opacity-50 rounded px-3 py-2 text-center text-white"
             style="bottom: 20px;">

            <h5 class="fs-5 fs-md-4">
                ¿Te apuntas?
            </h5>

            <p class="mb-2 fs-6 fs-md-5">
                Únete a planes, conoce gente nueva y comparte experiencias.
            </p>

        </div>

    </div>


    <!-- Slide 3 -->
    <div class="carousel-item">

        <img src="{{ asset('img/carrusel-3.jpg') }}"
             class="d-block w-100"
             alt="Personas creando un plan">

        <div class="carousel-caption bg-dark bg-opacity-50 rounded px-3 py-2 text-center text-white"
             style="bottom: 20px;">

            <h5 class="fs-5 fs-md-4">
                Haz que pase
            </h5>

            <p class="mb-2 fs-6 fs-md-5">
                Crea tu propio plan e invita a otros a disfrutarlo contigo.
            </p>

        </div>

    </div>

</div>


<button class="carousel-control-prev"
        type="button"
        data-bs-target="#planzaroteCarousel"
        data-bs-slide="prev">

    <span class="carousel-control-prev-icon"
          aria-hidden="true">
    </span>

    <span class="visually-hidden">
        Anterior
    </span>

</button>


<button class="carousel-control-next"
        type="button"
        data-bs-target="#planzaroteCarousel"
        data-bs-slide="next">

    <span class="carousel-control-next-icon"
          aria-hidden="true">
    </span>

    <span class="visually-hidden">
        Siguiente
    </span>

</button>

</div>

<div class="container mt-4 mb-5">

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
           style="max-width: 500px;">

    <button class="btn btn-danger"
            type="submit">
        Buscar
    </button>

</form>

</div>

<h1 class="mt-5 text-center">
    Bienvenido a PLanzarote
</h1>

<p class="text-center">
    Descubre planes, conoce gente y disfruta de la isla.
</p>

<script>
    document.getElementById('searchForm').addEventListener('submit', function(e) {

        const query = document.getElementById('searchInput').value.trim();

        if (!query) {
            e.preventDefault();
        }

    });
</script>

@endsection
