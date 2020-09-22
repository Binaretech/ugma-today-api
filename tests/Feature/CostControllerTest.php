<?php

namespace Tests\Feature;

use App\Models\Cost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index()
    {
        Passport::actingAs(User::factory()->active()->create(), ['admin']);
        Cost::factory()->times(10)->create();

        $this->get('/api/cost')
            ->assertOk()
            ->assertJsonStructure(['data' => [
                [
                    'id',
                    'name',
                    'comment',
                    'price',
                    'currency'
                ]
            ]]);
    }

    public function test_index_admin()
    {
        Passport::actingAs(User::factory()->active()->create(), ['admin']);
        Cost::factory()->times(10)->create();

        Passport::actingAs(User::factory()->active()->create(), ['admin']);

        $this->get('/api/admin/cost')
            ->assertOk()
            ->assertJsonStructure(['data' => [
                [
                    'id',
                    'modifiedBy' => [
                        'id',
                        'username',
                        'status',
                        'type',
                        'profile' => [
                            'name',
                            'lastname',
                            'email'
                        ]
                    ],
                    'name',
                    'comment',
                    'price',
                    'currency'
                ]
            ]]);
    }

    public function test_unauthorized_index_admin()
    {
        Passport::actingAs(User::factory()->active()->make(), ['user']);

        $this->get('/api/admin/cost')
            ->assertForbidden();
    }

    public function test_store()
    {
        Passport::actingAs(User::factory()->active()->create(), ['admin']);

        $this->post('api/admin/cost', [
            'name' => $this->faker->name,
            'price' => (string) $this->faker->numberBetween(1, 99999999999999),
            'currency' => $this->faker->numberBetween(0, 1),
            'comment' => $this->faker->realText(128),
        ])->assertCreated()->assertJsonStructure([
            'message'
        ]);
    }

    public function test_show()
    {
        Passport::actingAs(
            User::factory()->active()->admin()->create(),
            ['admin']
        );

        $cost = Cost::factory()->create();

        $this->get('api/cost/' . $cost->id)
            ->assertOk()->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'comment',
                    'price',
                    'currency',
                ]
            ]);
    }

    public function test_show_admin()
    {
        Passport::actingAs(
            User::factory()->active()->admin()->create(),
            ['admin']
        );

        $cost = Cost::factory()->create();

        $this->get('api/admin/cost/' . $cost->id)
            ->assertOk()->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'modifiedBy' => [
                        'id',
                        'username',
                        'status',
                        'type',
                        'profile' => [
                            'name',
                            'lastname',
                            'email'
                        ],
                    ],
                    'comment',
                    'price',
                    'currency',
                ]
            ]);
    }

    public function test_update()
    {
        Passport::actingAs(
            User::factory()->active()->admin()->create(),
            ['admin']
        );

        $cost = Cost::factory()->create();

        $this->put('api/admin/cost/' . $cost->id, [
            'name' => $this->faker->name,
        ])->assertOk()->assertJsonStructure(['message']);
    }

    public function test_destroy()
    {
        Passport::actingAs(
            User::factory()->active()->admin()->create(),
            ['admin']
        );

        $cost = Cost::factory()->create();

        $this->delete('api/admin/cost/' . $cost->id)->assertOk()->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('costs', $cost->toArray());
    }
}
