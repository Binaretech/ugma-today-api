<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->realText($this->faker->numberBetween(50, 128)),
            'content' => $this->faker->realText(1000),
            'type' => $this->faker->numberBetween(0, 2),
        ];
    }
}
    