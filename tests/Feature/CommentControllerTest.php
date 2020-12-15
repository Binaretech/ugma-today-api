<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
	use DatabaseTransactions, WithFaker;
	
	public function test_store() {
		$post = User::factory()->create()->posts()->save(Post::factory()->make());
		
		$test_user = User::factory()->create();

		$data = [
			'comment' => $this->faker->text
		];

		Passport::actingAs($test_user, ['user']);

		$this->post('api/post/'.$post->id.'/comment', $data)
			->assertCreated();

		$post->refresh();

		$this->assertCount(1, $post->comments);	
	}
}
