<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relation()
    {
        $comment = Comment::factory()
            ->make([
                'user_id' => User::factory()->create(),
            ]);

        $this->assertNotNull($comment->user);
        $this->assertDatabaseHas('users', ['id' => $comment->user->id]);
    }

    public function test_post_relation()
    {
        $user = User::factory()->active()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id
        ]);

        $comment = Comment::factory()
            ->create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);

        $this->assertNotNull($comment->post);
        $this->assertDatabaseHas('posts', ['id' => $comment->post->id]);
    }

    public function test_reports_relation()
    {
        $user = User::factory()->active()->create();
        $comment = Comment::factory()
            ->create([
                'user_id' => $user->id,
                'post_id' => Post::factory()->create([
                    'user_id' => User::factory()->active()->create()
                ]),
            ]);

        $comment->reports()->saveMany(Report::factory()->times(10)->make([
            'user_id' => User::factory()->active()->create()
        ]));

        $this->assertNotNull($comment->post);
        $this->assertDatabaseHas('reports', [
            'reportable_id' => $comment->id,
            'reportable_type' => Comment::class
        ]);
    }

    public function test_likes_relation()
    {
        $user = User::factory()->active()->create();

        $comment = Comment::factory()
            ->create([
                'user_id' => $user->id,
                'post_id' => Post::factory()->create([
                    'user_id' => User::factory()->active()->create()
                ]),
            ]);

        $comment->likes()
            ->save(new Like([
                'user_id' => User::factory()->active()->create()->id,
            ]));

        $this->assertNotEmpty($comment->likes);
        $this->assertCount(1, $comment->likes);
    }

    public function test_replies_and_reply_relations()
    {
        $post = Post::factory()->create([
            'user_id' => User::factory()->active()->create()
        ]);

        $comment = Comment::factory()
            ->create([
                'user_id' => User::factory()->active()->create(),
                'post_id' => $post->id,
            ]);

        $replyComment = Comment::factory()
            ->create([
                'user_id' => User::factory()->active()->create(),
                'post_id' => $post->id,
            ]);

        $comment->replies()
            ->save($replyComment);

        $this->assertNotEmpty($comment->replies);
        $this->assertNotNull($replyComment->reply);
    }
}
