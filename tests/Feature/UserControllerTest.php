<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_index()
    {
        User::factory()->active()->create();

        Passport::actingAs(User::factory()->active()->create(), ['admin']);
		$this->get('api/admin/user')
			->assertOk()
			->assertJsonStructure([
				'ids',
				'data', 
			]);
    }

    public function test_show()
    {
        $user = User::factory()->active()->create();

        Passport::actingAs($user, [User::TYPES[$user->type]]);

        $this->get('api/user/' . $user->id)->assertOk()->assertJsonStructure(['data' => [
            'id',
            'username',
            'status',
            'type'
        ]]);
    }

    public function test_show_not_found()
    {
        $user = User::factory()->active()->create();

        Passport::actingAs($user, [User::TYPES[$user->type]]);

        $this->get('api/user/1000')->assertNotFound();
    }

    public function test_ban()
    {
        $admin = User::factory()->active()->create(['type' => User::TYPES['admin']]);
        $user = User::factory()->active()->create();

        Passport::actingAs($admin, [User::TYPES[$admin->type]]);

        $this->post('api/admin/ban/user/' . $user->id)->assertOk();
    }

    public function test_active()
    {
        $admin = User::factory()->active()->create(['type' => User::TYPES['admin']]);
        $user = User::factory()->active()->create(['status' => User::STATUS['ACTIVE']]);

        Passport::actingAs($admin, [User::TYPES[$admin->type]]);

        $this->post('api/admin/active/user/' . $user->id)->assertOk();
    }

    public function test_update()
    {
        $user = User::factory()->active()->create();

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
        $user = User::factory()->active()->create();

        Passport::actingAs($user, [User::TYPES[$user->type]]);

        $this->delete('api/user')->assertOk();
    }
}
