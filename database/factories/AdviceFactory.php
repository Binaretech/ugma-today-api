<?php

namespace Database\Factories;

use App\Models\Advice;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdviceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Advice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(128),
            'content' => $this->faker->realText(500),
            'show_at' => $this->faker->boolean() ? $this->faker->dateTime() : null,
            'expire_at' => $this->faker->boolean() ? $this->faker->dateTime() : null,
        ];
    }
}
