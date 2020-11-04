<?php

namespace Database\Seeders;

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
        User::factory()->admin()->active()->create(['username' => 'admin']);

        User::factory()->times(20)->user()->active()->create();

        User::factory()->times(20)->user()->banned()->create();

        User::factory()->admin()->active()->deleted()->create();

        User::factory()->times(5)->user()->active()->deleted()->create();

        User::factory()->times(5)->user()->banned()->deleted()->create();
    }
}
