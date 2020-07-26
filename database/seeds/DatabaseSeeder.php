<?php

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
            CostTableSeeder::class,
            AdviceSeeder::class
        ]);
        $time = $time->diffForHumans(Carbon::now());

        echo "\nTime since seeders started: $time\n\n";
    }
}
