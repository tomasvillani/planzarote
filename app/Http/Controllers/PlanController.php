<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->input('q');

        $planes = Plan::with('user')
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where(function ($q) use ($query) {
                    $q->where('nombre', 'like', '%' . $query . '%')
                      ->orWhere('descripcion', 'like', '%' . $query . '%')
                      ->orWhere('ubicacion', 'like', '%' . $query . '%');
                });
            })
            ->where('fecha', '>=', now())
            ->orderBy('fecha', 'asc')
            ->paginate(12);

        return view('plans.index', [
            'planes' => $planes,
            'query' => $query,
            'hasFilter' => !empty($query),
        ]);
    }

    public function myPlans(Request $request): View
    {
        $planes = Plan::where('user_id', $request->user()->id)
            ->orderBy('fecha', 'asc')
            ->paginate(12);

        return view('myplans.index', [
            'planes' => $planes,
        ]);
    }

    /**
     * Mostrar el formulario para crear un nuevo plan.
     */
    public function create(): View
    {
        return view('myplans.create');
    }

    /**
     * Guardar un nuevo plan.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'required',
                'string',
            ],

            'ubicacion' => [
                'required',
                'string',
                'max:255',
            ],

            'fecha' => [
                'required',
                'date',
                'after:' . now()->endOfDay(),
            ],

            'imagen' => [
                'nullable',
                'image',
                'max:2048',
            ],
        ]);

        $imagen = null;

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('planes', 'public');
        }

        Plan::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'ubicacion' => $request->ubicacion,
            'fecha' => $request->fecha,
            'imagen' => $imagen,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('plans.myplans')
            ->with('success', 'El plan se ha publicado correctamente.');
    }
}