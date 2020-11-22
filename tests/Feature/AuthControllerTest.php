<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_register()
    {
        $this->post('api/register', [
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'name' => $this->faker->name,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
        ])->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'username',
                'type',
                'profile' => [
                    'name',
                    'lastname',
                    'email'
                ],
                'token'
            ]
        ]);
    }

    public function test_login_with_username()
    {
        $register = [
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'name' => $this->faker->name,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
        ];

        $this->post('api/register', $register)->assertCreated();

        $this->post('api/login', [
            'username' => $register['username'],
            'password' => $register['password'],
        ])->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'username',
                'type',
                'profile' => [
                    'name',
                    'lastname',
                    'email'
                ],
                'token'
            ]
        ]);
    }

    public function test_login_with_email()
    {
        $register = [
            'username' => $this->faker->username,
            'password' => $this->faker->password,
            'name' => $this->faker->name,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
        ];

        $this->post('api/register', $register)->assertCreated();

        $this->post('api/login', [
            'email' => $register['email'],
            'password' => $register['password'],
        ])->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'username',
                'type',
                'profile' => [
                    'name',
                    'lastname',
                    'email'
                ],
                'token'
            ]
        ]);
    }

    public function test_admin_login_with_username()
    {

        $user = User::factory()->admin()->active()->create([
            'password' => 'secret123',
        ]);

        $this->post('api/admin/login', [
            'username' => $user->username,
            'password' => 'secret123',
        ])->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'username',
                'type',
                'profile' => [
                    'name',
                    'lastname',
                    'email'
                ],
                'token'
            ]
        ]);
    }

    public function test_admin_login_with_email()
    {

        $user = User::factory()->admin()->active()->create([
            'password' => 'secret123',
        ]);

        $this->post('api/admin/login', [
            'email' => $user->profile->email,
            'password' => 'secret123',
        ])->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'username',
                'type',
                'profile' => [
                    'name',
                    'lastname',
                    'email'
                ],
                'token'
            ]
        ]);
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
        Mail::fake();

        $register = [
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'name' => $this->faker->name,
            'lastname' => $this->faker->lastName,
            'email' => 'alanbrito@gmail.com',
        ];

        $this->post('api/register', $register)->assertCreated();

        $this->json('POST', 'api/passwordReset', ['email' => $register['email']])->assertOk();

        Mail::assertQueued(PasswordResetMail::class);
    }

    public function test_reset_password()
    {
        $password_reset = PasswordReset::factory()->create([
            'expire_at' => Carbon::now()->addHour(2),
        ]);

        $this->post('api/resetPassword', [
            'token' => $password_reset->token,
            'password' => $this->faker->password
        ])->assertOk();
    }

    public function test_expired_reset_password()
    {

        $password_reset = PasswordReset::factory()->create([
            'expire_at' => Carbon::yesterday(),
        ]);

        $this->post('api/resetPassword', [
            'token' => $password_reset->token,
            'password' => $this->faker->password
        ])->assertStatus(422);
    }
}
