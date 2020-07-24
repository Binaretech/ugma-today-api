<?php

namespace Tests\Feature;

use App\Cost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index()
    {
        Passport::actingAs(factory(User::class)->create(), ['admin']);
        factory(Cost::class, 10)->create();

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
        Passport::actingAs(factory(User::class)->create(), ['admin']);
        factory(Cost::class, 10)->create();

        Passport::actingAs(factory(User::class)->create(), ['admin']);

        $this->get('/api/admin/cost')
            ->assertOk()
            ->assertJsonStructure(['data' => [
                [
                    'id',
                    'modified_by' => [
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
        Passport::actingAs(factory(User::class)->make(), ['user']);

        $this->get('/api/admin/cost')
            ->assertForbidden();
    }

    public function test_store_cost()
    {
        Passport::actingAs(factory(User::class)->create(), ['admin']);

        $response = $this->post('api/admin/cost', [
            'name' => $this->faker->name,
            'price' => (string) $this->faker->numberBetween(1, 99999999999999),
            'currency' => $this->faker->numberBetween(0, 1),
            'comment' => $this->faker->realText(128),
        ])->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'comment',
                'price',
                'currency'
            ]
        ]);
    }
}
