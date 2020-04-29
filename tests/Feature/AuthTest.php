<?php

namespace Tests\Feature;

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

    /**
     * A basic feature test example.
     *
     * @return void
     */
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

        $response = $this->post('api/login', [
            'username' => $register['username'],
            'password' => $register['password'],
        ]);

        $response->assertOk();
    }
}
