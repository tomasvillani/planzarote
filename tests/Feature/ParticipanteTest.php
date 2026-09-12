<?php

namespace Tests\Feature;

use App\Models\Participante;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipanteTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_puede_apuntarse_a_un_plan(): void
    {
        $creador = User::factory()->create();
        $usuario = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Partida de pádel',
            'descripcion' => 'Partida amistosa.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        $response = $this->actingAs($usuario)
            ->post(route('plans.join', $plan));

        $response->assertRedirect();

        $this->assertDatabaseHas('participantes', [
            'user_id' => $usuario->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_un_usuario_no_puede_apuntarse_dos_veces_al_mismo_plan(): void
    {
        $creador = User::factory()->create();
        $usuario = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Ruta en bici',
            'descripcion' => 'Ruta por Arrecife.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        Participante::create([
            'user_id' => $usuario->id,
            'plan_id' => $plan->id,
        ]);

        $response = $this->actingAs($usuario)
            ->post(route('plans.join', $plan));

        $response->assertSessionHasErrors('plan');

        $this->assertDatabaseCount('participantes', 1);
    }

    public function test_el_creador_no_puede_apuntarse_a_su_propio_plan(): void
    {
        $creador = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Ruta por La Geria',
            'descripcion' => 'Visita a La Geria.',
            'ubicacion' => 'La Geria',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        $response = $this->actingAs($creador)
            ->post(route('plans.join', $plan));

        $response->assertSessionHasErrors('plan');

        $this->assertDatabaseMissing('participantes', [
            'user_id' => $creador->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_un_usuario_puede_salir_de_un_plan(): void
    {
        $creador = User::factory()->create();
        $usuario = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Partida de baloncesto',
            'descripcion' => 'Partido amistoso.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        Participante::create([
            'user_id' => $usuario->id,
            'plan_id' => $plan->id,
        ]);

        $response = $this->actingAs($usuario)
            ->delete(route('plans.leave', $plan));

        $response->assertRedirect();

        $this->assertDatabaseMissing('participantes', [
            'user_id' => $usuario->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_un_usuario_no_puede_salir_de_un_plan_en_el_que_no_participa(): void
    {
        $creador = User::factory()->create();
        $usuario = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Tarde en El Charco',
            'descripcion' => 'Paseo por El Charco.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        $response = $this->actingAs($usuario)
            ->delete(route('plans.leave', $plan));

        $response->assertSessionHasErrors('plan');
    }

    public function test_el_creador_puede_ver_los_participantes_de_su_plan(): void
    {
        $creador = User::factory()->create();
        $usuario = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Visita al Lago Verde',
            'descripcion' => 'Excursión por El Golfo.',
            'ubicacion' => 'El Golfo',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        Participante::create([
            'user_id' => $usuario->id,
            'plan_id' => $plan->id,
        ]);

        $response = $this->actingAs($creador)
            ->get(route('plans.participants', $plan));

        $response->assertStatus(200);
        $response->assertSee($usuario->name);
    }

    public function test_un_usuario_no_puede_ver_los_participantes_de_un_plan_ajeno(): void
    {
        $creador = User::factory()->create();
        $otroUsuario = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Ruta de senderismo',
            'descripcion' => 'Ruta por Lanzarote.',
            'ubicacion' => 'Lanzarote',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        $response = $this->actingAs($otroUsuario)
            ->get(route('plans.participants', $plan));

        $response->assertForbidden();
    }

    public function test_un_usuario_no_autenticado_no_puede_apuntarse(): void
    {
        $creador = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Ruta en bici',
            'descripcion' => 'Ruta por la costa.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        $response = $this->post(route('plans.join', $plan));

        $response->assertRedirect(route('login'));

        $this->assertDatabaseCount('participantes', 0);
    }
}