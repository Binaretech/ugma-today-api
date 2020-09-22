<?php

namespace Database\Seeders;

use App\Models\{
    Advice,
    User
};
use Illuminate\Database\Seeder;
use Laravel\Passport\Passport;

class AdviceSeeder extends Seeder
{
    public function __construct()
    {
        $this->users = User::admin()->active()->get();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->users->each(function (User $user) {
            Passport::actingAs($user);
            Advice::factory()->times(20)->create();
        });
    }
}
