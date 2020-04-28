<?php

namespace Tests\Unit;

use App\Comment;
use App\Like;
use App\Post;
use App\Report;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relation()
    {
        $posts = factory(User::class)
            ->create()->posts()->save(factory(Post::class)->make());

        $this->assertNotEmpty($posts);
        $this->assertNotNull($posts->user);
    }

    public function test_comments_relation()
    {
        $post = factory(Post::class)
            ->create(['user_id' => factory(User::class)->create()]);

        $post->comments()
            ->saveMany(factory(Comment::class, 10)
                ->make(['user_id' => factory(User::class)->create()]));

        $this->assertNotEmpty($post->comments);
        $this->assertCount(10, $post->comments);
    }

    public function test_likes_relation()
    {
        $post = factory(Post::class)
            ->create(['user_id' => factory(User::class)->create()]);

        $post->likes()
            ->save(new Like([
                'user_id' => factory(User::class)->create()->id,
            ]));

        $this->assertNotEmpty($post->likes);
        $this->assertCount(1, $post->likes);
    }

    public function test_reports_relation()
    {
        $post = factory(Post::class)->create([
            'user_id' => factory(User::class)->create()
        ]);

        factory(Report::class, 10)->make(['user_id' => factory(User::class)->create()])
            ->each(function (Report $report) use ($post) {
                $post->reports()->save($report);
            });


        $this->assertNotEmpty($post->reports);
        $this->assertCount(10, $post->reports);
    }
}
