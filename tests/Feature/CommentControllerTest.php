<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
	use DatabaseTransactions, WithFaker;

	public function test_store()
	{
		$post = User::factory()->create()->posts()->save(Post::factory()->make());

		$test_user = User::factory()->create();

		$data = [
			'comment' => $this->faker->text
		];

		Passport::actingAs($test_user, ['user']);

		$this->post('api/comment/' . $post->id, $data)
			->assertCreated();

		$post->refresh();

		$this->assertCount(1, $post->comments);
	}

	public function test_index()
	{
		$post = User::factory()->create()->posts()->save(Post::factory()->make());
		$post->comments()->saveMany(Comment::factory()->times(10)->make(['user_id' => User::factory()->create()->id]));

		$this->get('api/comment/' . $post->id)
			->assertOk()
			->assertJsonStructure(['comments' => ['ids', 'data'], 'replies']);
	}

	public function test_like()
	{
		$post = User::factory()->create()->posts()->save(Post::factory()->make());
		$comment = $post->comments()->save(Comment::factory()->make([
			'comment' => $this->faker->text,
			'user_id' => $post->user_id,
			'post_id' => $post->id,
		]));


		$test_user = User::factory()->create();
		Passport::actingAs($test_user, ['user']);

		$this->post('api/comment/like/' . $comment->id)
			->assertCreated();

		$comment->refresh();

		$this->assertCount(1, $comment->likes);
		$this->assertEquals($comment->likes[0]->user_id, $test_user->id);
	}

	public function test_unlike()
	{
		$post = User::factory()->create()->posts()->save(Post::factory()->make());
		$comment = $post->comments()->save(Comment::factory()->make([
			'comment' => $this->faker->text,
			'user_id' => $post->user_id,
			'post_id' => $post->id,
		]));

		$test_user = User::factory()->create();

		$comment->likes()->save(new Like(['user_id' => $test_user->id]));

		Passport::actingAs($test_user, ['user']);

		$this->post('api/comment/unlike/' . $comment->id)
			->assertOk();

		$comment->refresh();

		$this->assertCount(0, $comment->likes);
	}
}
