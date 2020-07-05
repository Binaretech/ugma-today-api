<?php

namespace Tests\Unit;

use App\Comment;
use App\Like;
use App\Post;
use App\Report;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relation()
    {
        $comment = factory(Comment::class)
            ->make([
                'user_id' => factory(User::class)->create(),
            ]);

        $this->assertNotNull($comment->user);
        $this->assertDatabaseHas('users', ['id' => $comment->user->id]);
    }

    public function test_post_relation()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create([
            'user_id' => $user->id
        ]);

        $comment = factory(Comment::class)
            ->create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);

        $this->assertNotNull($comment->post);
        $this->assertDatabaseHas('posts', ['id' => $comment->post->id]);
    }

    public function test_reports_relation()
    {
        $user = factory(User::class)->create();
        $comment = factory(Comment::class)
            ->create([
                'user_id' => $user->id,
                'post_id' => factory(Post::class)->create([
                    'user_id' => factory(User::class)->create()
                ]),
            ]);

        $comment->reports()->saveMany(factory(Report::class, 10)->make([
            'user_id' => factory(User::class)->create()
        ]));

        $this->assertNotNull($comment->post);
        $this->assertDatabaseHas('reports', [
            'reportable_id' => $comment->id,
            'reportable_type' => Comment::class
        ]);
    }

    public function test_likes_relation()
    {
        $user = factory(User::class)->create();

        $comment = factory(Comment::class)
            ->create([
                'user_id' => $user->id,
                'post_id' => factory(Post::class)->create([
                    'user_id' => factory(User::class)->create()
                ]),
            ]);

        $comment->likes()
            ->save(new Like([
                'user_id' => factory(User::class)->create()->id,
            ]));

        $this->assertNotEmpty($comment->likes);
        $this->assertCount(1, $comment->likes);
    }

    public function test_answer_to_and_replies_relations()
    {
        $post = factory(Post::class)->create([
            'user_id' => factory(User::class)->create()
        ]);

        $comment = factory(Comment::class)
            ->create([
                'user_id' => factory(User::class)->create(),
                'post_id' => $post->id,
            ]);

        $replyComment = factory(Comment::class)
            ->create([
                'user_id' => factory(User::class)->create(),
                'post_id' => $post->id,
            ]);

        $comment->replies()
            ->save($replyComment);

        $this->assertNotEmpty($comment->replies);
    }
}
