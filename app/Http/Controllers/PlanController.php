<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Participante;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
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

    public function create(): View
    {
        return view('myplans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'ubicacion' => ['required', 'string', 'max:255'],
            'fecha' => [
                'required',
                'date',
                'after:' . now()->endOfDay(),
            ],
            'imagen' => ['nullable', 'image', 'max:2048'],
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
            ->route('myplans.index')
            ->with('success', 'El plan se ha publicado correctamente.');
    }

    public function show(Plan $plan): View
    {
        $yaParticipa = false;

        if (auth()->check()) {
            $yaParticipa = Participante::where('user_id', auth()->id())
                ->where('plan_id', $plan->id)
                ->exists();
        }

        return view('plans.show', [
            'plan' => $plan,
            'yaParticipa' => $yaParticipa,
        ]);
    }

    public function edit(Plan $plan): View
    {
        if ($plan->user_id !== auth()->id()) {
            abort(403);
        }

        return view('myplans.edit', [
            'plan' => $plan,
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        if ($plan->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'ubicacion' => ['required', 'string', 'max:255'],
            'fecha' => [
                'required',
                'date',
                'after:' . now()->endOfDay(),
            ],
            'imagen' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->has('eliminar_imagen') && $plan->imagen) {
            Storage::disk('public')->delete($plan->imagen);
            $plan->imagen = null;
        }

        if ($request->hasFile('imagen')) {
            if ($plan->imagen) {
                Storage::disk('public')->delete($plan->imagen);
            }

            $plan->imagen = $request->file('imagen')->store('planes', 'public');
        }

        $plan->nombre = $request->nombre;
        $plan->descripcion = $request->descripcion;
        $plan->ubicacion = $request->ubicacion;
        $plan->fecha = $request->fecha;

        $plan->save();

        return redirect()
            ->route('myplans.index')
            ->with('success', 'El plan se ha actualizado correctamente.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->user_id !== auth()->id()) {
            abort(403);
        }

        $plan->delete();

        return redirect()
            ->route('myplans.index')
            ->with('success', 'El plan se ha eliminado correctamente.');
    }
}