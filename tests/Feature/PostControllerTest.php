<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\{User, Post};
use Laravel\Passport\Passport;

class PostControllerTest extends TestCase
{
	use DatabaseTransactions;
	
	public function test_index_posts() {
		$user = User::factory()->create();
		$user->posts()->saveMany(Post::factory()->times(10)->make(['type' => Post::TYPES['REGULAR']]));
	
		$this->get('api/post')
			->assertOk()
			->assertJsonStructure([
				'data',
				'ids'
			]);
	}

	public function test_index_news() {
		$user = User::factory()->create();
		$user->posts()->saveMany(Post::factory()->times(10)->make(['type' => Post::TYPES['NEWS']]));
	
		$this->get('api/news')
			->assertOk()
			->assertJsonStructure([
				'data',
				'ids'
			]);
	}

	public function test_admin_index_posts() {
		$user = User::factory()->create();

		$user->posts()->saveMany(Post::factory()->times(10)->make(['type' => Post::TYPES['NEWS']]));	
		$user->posts()->saveMany(Post::factory()->times(10)->make(['type' => Post::TYPES['REGULAR']]));

		$admin = User::factory()->create(['type' => User::TYPES['admin']]);

		Passport::actingAs($admin, ['admin']);

		$this->get('api/news')
			->assertOk()
			->assertJsonStructure([
				'data',
				'ids'
			]);
	}

}
