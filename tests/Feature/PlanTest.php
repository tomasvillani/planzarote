<?php

namespace Tests\Feature;

use App\Models\Participante;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_autenticado_puede_crear_un_plan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('plans.store'), [
            'nombre' => 'Ruta en bici',
            'descripcion' => 'Ruta por la costa de Arrecife.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $response->assertRedirect(route('myplans.index'));

        $this->assertDatabaseHas('plans', [
            'nombre' => 'Ruta en bici',
            'ubicacion' => 'Arrecife',
            'user_id' => $user->id,
        ]);
    }

    public function test_no_se_puede_crear_un_plan_sin_nombre(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('plans.store'), [
            'nombre' => '',
            'descripcion' => 'Una descripción.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $response->assertSessionHasErrors('nombre');
    }

    public function test_no_se_puede_crear_un_plan_con_fecha_pasada(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('plans.store'), [
            'nombre' => 'Plan antiguo',
            'descripcion' => 'Descripción.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->subDay()->format('Y-m-d H:i'),
        ]);

        $response->assertSessionHasErrors('fecha');
    }

    public function test_un_usuario_puede_ver_un_plan(): void
    {
        $user = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Partida de pádel',
            'descripcion' => 'Partida amistosa.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('plans.show', $plan));

        $response->assertStatus(200);
        $response->assertSee('Partida de pádel');
    }

    public function test_el_creador_puede_editar_su_plan(): void
    {
        $user = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Plan original',
            'descripcion' => 'Descripción original.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('plans.edit', $plan));

        $response->assertStatus(200);
    }

    public function test_un_usuario_no_puede_editar_el_plan_de_otro(): void
    {
        $creador = User::factory()->create();
        $otroUsuario = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Plan privado',
            'descripcion' => 'Descripción.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        $response = $this->actingAs($otroUsuario)
            ->get(route('plans.edit', $plan));

        $response->assertForbidden();
    }

    public function test_el_creador_puede_eliminar_su_plan(): void
    {
        $user = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Plan a eliminar',
            'descripcion' => 'Descripción.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('plans.destroy', $plan));

        $response->assertRedirect(route('myplans.index'));

        $this->assertDatabaseMissing('plans', [
            'id' => $plan->id,
        ]);
    }

    public function test_un_usuario_no_puede_eliminar_el_plan_de_otro(): void
    {
        $creador = User::factory()->create();
        $otroUsuario = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Plan ajeno',
            'descripcion' => 'Descripción.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        $response = $this->actingAs($otroUsuario)
            ->delete(route('plans.destroy', $plan));

        $response->assertForbidden();

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
        ]);
    }

    public function test_el_comando_elimina_los_planes_caducados(): void
    {
        $user = User::factory()->create();

        $planCaducado = Plan::create([
            'nombre' => 'Plan caducado',
            'descripcion' => 'Este plan ya ha pasado.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->subDay(),
            'user_id' => $user->id,
        ]);

        $planFuturo = Plan::create([
            'nombre' => 'Plan futuro',
            'descripcion' => 'Este plan todavía no ha pasado.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDay(),
            'user_id' => $user->id,
        ]);

        $this->artisan('plans:delete-expired')
            ->assertSuccessful();

        $this->assertDatabaseMissing('plans', [
            'id' => $planCaducado->id,
        ]);

        $this->assertDatabaseHas('plans', [
            'id' => $planFuturo->id,
        ]);
    }

    public function test_al_eliminar_un_plan_se_eliminan_sus_participantes(): void
    {
        $creador = User::factory()->create();
        $usuario = User::factory()->create();

        $plan = Plan::create([
            'nombre' => 'Plan con participantes',
            'descripcion' => 'Plan de prueba.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $creador->id,
        ]);

        Participante::create([
            'user_id' => $usuario->id,
            'plan_id' => $plan->id,
        ]);

        $this->assertDatabaseHas('participantes', [
            'user_id' => $usuario->id,
            'plan_id' => $plan->id,
        ]);

        $plan->delete();

        $this->assertDatabaseMissing('participantes', [
            'user_id' => $usuario->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_se_puede_buscar_un_plan_por_nombre(): void
    {
        $user = User::factory()->create();

        Plan::create([
            'nombre' => 'Ruta en bicicleta',
            'descripcion' => 'Un paseo por la costa.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $user->id,
        ]);

        Plan::create([
            'nombre' => 'Partida de pádel',
            'descripcion' => 'Partida amistosa.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(6),
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('plans.index', [
            'q' => 'bicicleta',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Ruta en bicicleta');
        $response->assertDontSee('Partida de pádel');
    }

    public function test_se_puede_buscar_un_plan_por_descripcion(): void
    {
        $user = User::factory()->create();

        Plan::create([
            'nombre' => 'Plan deportivo',
            'descripcion' => 'Partida de baloncesto para todos.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $user->id,
        ]);

        Plan::create([
            'nombre' => 'Tarde tranquila',
            'descripcion' => 'Paseo por la costa.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(6),
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('plans.index', [
            'q' => 'baloncesto',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Plan deportivo');
        $response->assertDontSee('Tarde tranquila');
    }

    public function test_se_puede_buscar_un_plan_por_ubicacion(): void
    {
        $user = User::factory()->create();

        Plan::create([
            'nombre' => 'Visita al Lago Verde',
            'descripcion' => 'Excursión por Lanzarote.',
            'ubicacion' => 'El Golfo',
            'fecha' => now()->addDays(5),
            'user_id' => $user->id,
        ]);

        Plan::create([
            'nombre' => 'Partida de pádel',
            'descripcion' => 'Partida amistosa.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(6),
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('plans.index', [
            'q' => 'El Golfo',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Visita al Lago Verde');
        $response->assertDontSee('Partida de pádel');
    }

    public function test_una_busqueda_sin_resultados_no_muestra_planes(): void
    {
        $user = User::factory()->create();

        Plan::create([
            'nombre' => 'Ruta en bici',
            'descripcion' => 'Ruta por la costa.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5),
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('plans.index', [
            'q' => 'xxxxxxxx',
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('Ruta en bici');
    }

    public function test_los_planes_caducados_no_aparecen_en_el_listado(): void
    {
        $user = User::factory()->create();

        Plan::create([
            'nombre' => 'Plan caducado',
            'descripcion' => 'Ya ha pasado.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->subDay(),
            'user_id' => $user->id,
        ]);

        Plan::create([
            'nombre' => 'Plan futuro',
            'descripcion' => 'Todavía disponible.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDay(),
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('plans.index'));

        $response->assertStatus(200);
        $response->assertSee('Plan futuro');
        $response->assertDontSee('Plan caducado');
    }

    public function test_la_paginacion_de_planes_funciona(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 13; $i++) {
            Plan::create([
                'nombre' => 'Plan ' . $i,
                'descripcion' => 'Descripción del plan ' . $i,
                'ubicacion' => 'Arrecife',
                'fecha' => now()->addDays($i),
                'user_id' => $user->id,
            ]);
        }

        $response = $this->get(route('plans.index'));

        $response->assertStatus(200);
        $response->assertSee('Plan 1');
        $response->assertSee('Plan 12');
    }

    public function test_se_puede_subir_una_imagen_al_crear_un_plan(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $imagen = UploadedFile::fake()->image('plan.jpg');

        $response = $this->actingAs($user)->post(route('plans.store'), [
            'nombre' => 'Plan con imagen',
            'descripcion' => 'Plan de prueba con imagen.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5)->format('Y-m-d H:i'),
            'imagen' => $imagen,
        ]);

        $response->assertRedirect(route('myplans.index'));

        $plan = Plan::first();

        $this->assertNotNull($plan->imagen);

        Storage::disk('public')->assertExists($plan->imagen);
    }

    public function test_no_se_puede_subir_un_archivo_que_no_sea_una_imagen(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $archivo = UploadedFile::fake()->create(
            'documento.pdf',
            100,
            'application/pdf'
        );

        $response = $this->actingAs($user)->post(route('plans.store'), [
            'nombre' => 'Plan con archivo incorrecto',
            'descripcion' => 'Plan de prueba.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5)->format('Y-m-d H:i'),
            'imagen' => $archivo,
        ]);

        $response->assertSessionHasErrors('imagen');
    }

    public function test_no_se_puede_subir_una_imagen_demasiado_grande(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $imagen = UploadedFile::fake()->image('plan.jpg')->size(3000);

        $response = $this->actingAs($user)->post(route('plans.store'), [
            'nombre' => 'Plan con imagen grande',
            'descripcion' => 'Plan de prueba.',
            'ubicacion' => 'Arrecife',
            'fecha' => now()->addDays(5)->format('Y-m-d H:i'),
            'imagen' => $imagen,
        ]);

        $response->assertSessionHasErrors('imagen');
    }

    public function test_un_usuario_no_autenticado_no_puede_crear_un_plan(): void
    {
        $response = $this->get(route('plans.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_un_usuario_no_autenticado_no_puede_acceder_a_sus_planes(): void
    {
        $response = $this->get(route('myplans.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_un_usuario_no_autenticado_no_puede_acceder_a_sus_participaciones(): void
    {
        $response = $this->get(route('plans.myparticipations'));

        $response->assertRedirect(route('login'));
    }

    public function test_un_usuario_autenticado_puede_acceder_a_sus_planes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('myplans.index'));

        $response->assertStatus(200);
    }

    public function test_un_usuario_autenticado_puede_acceder_a_sus_participaciones(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('plans.myparticipations'));

        $response->assertStatus(200);
    }

    public function test_un_usuario_no_autenticado_puede_ver_los_planes(): void
    {
        $response = $this->get(route('plans.index'));

        $response->assertStatus(200);
    }
}