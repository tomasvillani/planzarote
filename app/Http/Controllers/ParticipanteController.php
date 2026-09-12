<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Participante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipanteController extends Controller
{
    /**
     * Usuario: apuntarse a un plan.
     */
    public function store(Request $request, Plan $plan): RedirectResponse
    {
        $user = $request->user();

        // El creador no puede apuntarse a su propio plan.
        if ($plan->user_id === $user->id) {
            return back()->withErrors([
                'plan' => 'No puedes apuntarte a tu propio plan.'
            ]);
        }

        // Comprobar si ya está participando.
        if (Participante::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->exists()) {

            return back()->withErrors([
                'plan' => 'Ya estás participando en este plan.'
            ]);
        }

        Participante::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        return back()->with('success', 'Te has apuntado al plan correctamente.');
    }

    /**
     * Ver los participantes de un plan.
     * Solo puede acceder el creador del plan.
     */
    public function index(Plan $plan): View
    {
        if ($plan->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver los participantes de este plan.');
        }

        $participantes = Participante::with('user')
            ->where('plan_id', $plan->id)
            ->latest()
            ->paginate(12);

        return view('participantes.index', [
            'plan' => $plan,
            'participantes' => $participantes,
        ]);
    }

    /**
     * Usuario: salir de un plan.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        $participante = Participante::where('user_id', auth()->id())
            ->where('plan_id', $plan->id)
            ->first();

        if (!$participante) {
            return back()->withErrors([
                'plan' => 'No estás participando en este plan.'
            ]);
        }

        $participante->delete();

        return back()->with('success', 'Has salido del plan correctamente.');
    }

    /**
     * Usuario: ver los planes en los que participa.
     */
    public function myParticipations(Request $request): View
    {
        $participantes = Participante::with('plan')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return view('participaciones.index', [
            'participantes' => $participantes,
        ]);
    }
}