<?php

namespace Database\Factories;

use App\Models\{
    PasswordReset,
    User,
};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasswordResetFactory extends Factory
{
    protected $model = PasswordReset::class;

    public function definition()
    {
        return [
            'token' => base64_encode($this->faker->realText(12)),
            'user_id' => User::factory()->create(),
            'expire_at' => Carbon::now()->addHours(2),
        ];
    }
}


    