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
        factory(User::class)->states([
            'admin',
            'active',
        ])->create(['username' =>  'mari_conazo']);

        factory(User::class, 20)->states([
            'user',
            'active',
        ])->create();

        factory(User::class, 20)->states([
            'user',
            'banned'
        ])->create();

        factory(User::class)->states([
            'admin',
            'active',
            'deleted',
        ])->create();

        factory(User::class, 5)->states([
            'user',
            'active',
            'deleted',
        ])->create();

        factory(User::class, 5)->states([
            'user',
            'banned',
            'deleted',
        ])->create();
        User::factory()->times(1)->create(['username' => 'mari_conazo']);
    }
}
