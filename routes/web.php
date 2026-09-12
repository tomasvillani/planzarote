<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipanteController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('about');
});


// Planes públicos

Route::get('/plans', [PlanController::class, 'index'])
    ->name('plans.index');


// Rutas para usuarios autenticados

Route::middleware('auth')->group(function () {

    // Perfil

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    // Dashboard

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');


    // Mis planes

    Route::get('/myplans', [PlanController::class, 'myPlans'])
        ->name('myplans.index');

        
    // Mis participaciones
    Route::get('/myparticipations', [ParticipanteController::class, 'myParticipations'])
    ->name('plans.myparticipations');


    // Crear planes

    Route::get('/plans/create', [PlanController::class, 'create'])
        ->name('plans.create');

    Route::post('/plans', [PlanController::class, 'store'])
        ->name('plans.store');


    // Editar planes

    Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])
        ->name('plans.edit');

    Route::patch('/plans/{plan}', [PlanController::class, 'update'])
        ->name('plans.update');


    // Eliminar planes

    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])
        ->name('plans.destroy');


    // Participación en planes

    Route::post('/plans/{plan}/join', [ParticipanteController::class, 'store'])
        ->name('plans.join');

    Route::delete('/plans/{plan}/leave', [ParticipanteController::class, 'destroy'])
        ->name('plans.leave');


    // Participantes

    Route::get('/plans/{plan}/participants', [ParticipanteController::class, 'index'])
        ->name('plans.participants');
});

Route::get('/plans/{plan}', [PlanController::class, 'show'])
    ->name('plans.show');


require __DIR__.'/auth.php';