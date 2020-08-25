<?php

use App\{
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
        $this->users->each(function(User $user) {
            Passport::actingAs($user);
            factory(Advice::class, 20)->create();
        });
    }
}
