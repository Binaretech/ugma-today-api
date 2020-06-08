<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index()
    {
        factory(User::class, 10)->create();

        Passport::actingAs(factory(User::class)->create(), ['ADMIN']);
        $this->get('api/user')->assertOk()->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_show()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, [User::TYPES[$user->type]]);

        $this->get('api/user/' . $user->id)->assertOk()->assertJsonStructure(['data']);
    }

    public function test_show_not_found()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, [User::TYPES[$user->type]]);

        $this->get('api/user/1000')->assertNotFound();
    }

    public function test_ban()
    {
        $admin = factory(User::class)->create(['type' => User::TYPES['ADMIN']]);
        $user = factory(User::class)->create();

        Passport::actingAs($admin, [User::TYPES[$admin->type]]);

        $this->post('api/ban/user/' . $user->id)->assertOk();
    }

    public function test_active()
    {
        $admin = factory(User::class)->create(['type' => User::TYPES['ADMIN']]);
        $user = factory(User::class)->create(['status' => User::STATUS['ACTIVE']]);

        Passport::actingAs($admin, [User::TYPES[$admin->type]]);

        $this->post('api/active/user/' . $user->id)->assertOk();
    }

    public function test_update()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, [User::TYPES[$user->type]]);

        $this->put('api/user', [
            'username' => $this->faker->userName,
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'lastname' => $this->faker->lastName
        ])->assertOk();
    }

    public function test_destroy()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, [User::TYPES[$user->type]]);

        $this->delete('api/user')->assertOk();
    }
}
