<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') === 'production') {
            User::factory()->admin()->active()
                ->hasProfile(1, [
                    'name' => 'Héctor',
                    'lastname' => 'Zurga',
                    'email' => 'hjzurga@gmail.com'
                ])
                ->create(['username' => 'hectorZ']);

            User::factory()->admin()->active()
                ->hasProfile(1, [
                    'name' => 'Ángel',
                    'lastname' => 'Afonso',
                    'email' => 'angelafonso60@gmail.com'
                ])
                ->create(['username' => 'angel_afonso']);


            return;
        }

        User::factory()->admin()->active()->create(['username' => 'admin']);

        User::factory()->user()->active()->create(['username' => 'student']);

        User::factory()->times(20)->user()->active()->create();

        User::factory()->times(20)->user()->banned()->create();

        User::factory()->admin()->active()->deleted()->create();

        User::factory()->times(5)->user()->active()->deleted()->create();

        User::factory()->times(5)->user()->banned()->deleted()->create();
    }
}
