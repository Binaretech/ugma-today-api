<?php

namespace Database\Factories;

use App\Models\Advice;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdviceFactory extends Factory
{
    protected $model = Advice::class;

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
