<?php

namespace Tests\Unit;

use App\Comment;
use App\Post;
use App\Report;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /*     public function test_user_relation()
    {
        $comment = factory(Comment::class)
            ->make([
                'user_id' => factory(User::class)->create(),
            ]);

        $this->assertNotNull($comment->user);
    }
 */
    /* public function test_post_relation()
    {
        $comment = factory(Comment::class)
            ->make([
                'user_id' => factory(User::class)->create(),
                'post_id' => factory(Post::class)->create([
                    'user_id' => factory(User::class)->create()
                ]),
            ]);

        $this->assertNotNull($comment->post);
    }

    public function test_reports_relation()
    {
        $comment = factory(Comment::class)
            ->create([
                'user_id' => factory(User::class)->create(),
                'post_id' => factory(Post::class)->create([
                    'user_id' => factory(User::class)->create()
                ]),
            ]);

        $comment->reports()->saveMany(factory(Report::class, 10)->make([
            'user_id' => factory(User::class)->create()
        ]));

        $this->assertNotNull($comment->post);
    } */
}
