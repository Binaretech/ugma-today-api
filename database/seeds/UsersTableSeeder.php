<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->states([
            'admin',
            'active',
        ])->create();

        factory(User::class, 20)->states([
            'user',
            'active',
        ])->create();

        factory(User::class, 20)->states([
            'user',
            'banned'
        ])->create();
    }
}
