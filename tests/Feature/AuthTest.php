<?php

namespace Tests\Feature;

use App\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_register()
    {
        $response = $this->post('api/register', [
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'name' => $this->faker->name,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
        ]);

        $response->assertCreated();
    }

    public function test_login()
    {
        $register = [
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'name' => $this->faker->name,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
        ];

        $this->post('api/register', $register);

        $this->post('api/login', [
            'username' => $register['username'],
            'password' => $register['password'],
        ])->assertOk();
    }

    public function test_fail_login()
    {
        $register = [
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'name' => $this->faker->name,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
        ];

        $this->post('api/register', $register);

        $this->post('api/login', [
            'username' => $register['username'],
            'password' => 'not the correct password',
        ])->assertUnauthorized();
    }

    public function test_password_reset_email()
    {
        $register = [
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'name' => $this->faker->name,
            'lastname' => $this->faker->lastName,
            'email' => 'alanbrito@gmail.com',
        ];

        $this->post('api/register', $register)->assertCreated();

        $this->json('POST', 'api/passwordReset', ['email' => $register['email']])->assertOk();
    }

    public function test_reset_password()
    {
        $password_reset = factory(PasswordReset::class)->create();

        $this->post('api/resetPassword', [
            'token' => $password_reset->token,
            'password' => $this->faker->password
        ])->assertOk();
    }
}
