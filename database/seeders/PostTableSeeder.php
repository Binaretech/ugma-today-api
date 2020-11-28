<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    User,
    Post,
    Comment,
};

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
	{
		User::active()->get()->each(function(User $user) {
			$quantity = rand(1,10);
		
			$user->posts()->saveMany(Post::factory()->times($quantity)->make([
				'type' => Post::TYPES['DRAFT'],
			]));

			$user->posts()->saveMany(Post::factory()->times($quantity)->make([
				'type' => Post::TYPES['REGULAR'],
			]));
		});

		User::active()->where('type', User::TYPES['admin'])->each(function(User $user){
			$user->posts()->saveMany(Post::factory()->times(10)->make([
				'type' => Post::TYPES['DRAFT'],
			]));

			$user->posts()->saveMany(Post::factory()->times(10)->make([
				'type' => Post::TYPES['NEWS'],
			]));

		});
    }
}
