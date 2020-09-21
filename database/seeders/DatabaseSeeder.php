<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();

        $this->call([
            UsersTableSeeder::class,
            PostTableSeeder::class,
            LikeTableSeeder::class,
            // CostTableSeeder::class,
            // AdviceSeeder::class
        ]);

        $time = Carbon::now()->diffForHumans($time);

        echo "\nTime since seeders started: $time\n\n";
    }
}
