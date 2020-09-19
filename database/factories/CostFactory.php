<?php

namespace Database\Factories;

use App\Models\{
    Cost,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Passport\Passport;

class CostFactory extends Factory
{
    protected $model = Cost::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'price' => $this->faker->randomFloat(2),
            'comment' => $this->faker->boolean() ? $this->faker->realText(128) : null,
            'currency' => $this->faker->randomElement([0, 1])
        ];
    }

    public function configure()
    {
        return $this->afterMakingState(function (Cost $cost) {
            Passport::actingAs(User::factory()->create());
        });
    }
}
