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

    /**
     * Set admin user type
     * @return UserFactory
     */
    public function admin()
    {
        return $this->state([
            'type' => User::TYPES['admin']
        ]);
    }

    /**
     * Set regular user type
     * @return UserFactory
     */
    public function user()
    {
        return $this->state([
            'type' => User::TYPES['user']
        ]);
    }

    /**
     * Set active user status
     * @return UserFactory
     */
    public function active()
    {
        return $this->state([
            'status' => User::STATUS['ACTIVE']
        ]);
    }

    /**
     * Set banned user status
     * @return UserFactory
     */
    public function banned()
    {
        return $this->state([
            'status' => User::STATUS['BANNED']
        ]);
    }

    /**
     * Set deleted user
     * @return UserFactory
     */
    public function deleted()
    {
        return $this->state(function (array $attributes) {
            return [
                'deleted_at' => $this->faker->dateTime
            ];
        });
    }

    /**
     * Factory configurations
     * @return UserFactory
     */
    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $user->profile()->save(Profile::factory()->make());
        });
    }
}
