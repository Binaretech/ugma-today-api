<?php

namespace Tests\Feature;

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
        $this->get('/api/cost')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_index_admin()
    {
        Passport::actingAs(factory(User::class)->make(), ['admin']);

        $this->get('/api/admin/cost')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_unauthorized_index_admin()
    {
        Passport::actingAs(factory(User::class)->make(), ['user']);

        $this->get('/api/admin/cost')
            ->assertForbidden();
    }
}
