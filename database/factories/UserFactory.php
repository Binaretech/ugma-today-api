<?php

namespace Database\Factories;

use App\Models\{
    User,
    Profile,
};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->userName,
            'email_verified_at' => now(),
            'type' => $this->faker->randomElement([User::STATUS['ACTIVE'], User::STATUS['BANNED']]),
            'status' => $this->faker->numberBetween(0, 1),
            'password' => 'secret',
            'remember_token' => Str::random(10),
        ];
    }

    public function admin()
    {
        return $this->state([
            'type' => User::TYPES['admin']
        ]);
    }

    public function user()
    {
        return $this->state([
            'type' => User::TYPES['user']
        ]);
    }

    public function active()
    {
        return $this->state([
            'status' => User::STATUS['ACTIVE']
        ]);
    }

    public function banned()
    {
        return $this->state([
            'status' => User::STATUS['BANNED']
        ]);
    }

    public function deleted()
    {
        return $this->state([
            'deleted_at' => $this->faker->dateTime
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $user->profile()->save(Profile::factory()->make());
        });
    }
}
