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

	public function test_show_news() {
		$user = User::factory()->create();
		$post = $user->posts()->save(Post::factory()->make(['type' => Post::TYPES['NEWS']]));
		
		$this->get('api/news/'. $post->id)
			->assertOk()
			->assertJson([
				'data' => [
					'id' => $post->id,
					'title' => $post->title,
					'content' => $post->content,
					'type' => Post::TYPES['NEWS'],
					'user'=> [
						'id' => $post->user->id,
						'status' => $post->user->status,
						'type' => $post->user->type,
						'username' => $post->user->username,
						'profileImage' => $post->user->profile_image->url,
						'profile' => [
							'email' => $post->user->profile->email,
							'fullname' => $post->user->profile->name.' '.$post->user->profile->lastname,
							'name' => $post->user->profile->name,
							'lastname' =>  $post->user->profile->lastname,
						]
					]
				]
			]);				
	}

	public function test_like_cost() {
		$post = User::factory()->create()->posts()->save(Post::factory()->make());
		Passport::actingAs(User::factory()->create(), ['user']);

		$this->post('api/post/like/'. $post->id)
		   ->assertCreated();
	}
	
	public function test_fail_like_cost() {
		$post = User::factory()->create()->posts()->save(Post::factory()->make());
		Passport::actingAs(User::factory()->create(), ['user']);

		$this->post('api/post/like/'. $post->id)
	   ->assertCreated();
	
		$this->post('api/post/like/'. $post->id)
	   ->assertStatus(400);
	}
}
