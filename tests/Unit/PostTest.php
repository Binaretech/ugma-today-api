<?php

namespace Tests\Unit;

use App\Comment;
use App\File;
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
        $user = factory(User::class)
            ->create();

        $posts = $user->posts()->save(factory(Post::class)->make());

        $this->assertNotEmpty($posts);
        $this->assertNotNull($posts->user);
    }

    public function test_comments_relation()
    {
        $user =  factory(User::class)->create();

        $post = factory(Post::class)->create([
            'user_id' => $user->id
        ]);

        $post->comments()
            ->saveMany(factory(Comment::class, 10)
                ->make(['user_id' => factory(User::class)->create()]));

        $this->assertNotEmpty($post->comments);
        $this->assertCount(10, $post->comments);
    }

    public function test_likes_relation()
    {
        $user =  factory(User::class)->create();
        $post = factory(Post::class)
            ->create([
                'user_id' => $user->id
            ]);

        $post->likes()
            ->save(new Like([
                'user_id' => factory(User::class)->create()->id,
            ]));

        $this->assertNotEmpty($post->likes);
        $this->assertCount(1, $post->likes);
    }

    public function test_reports_relation()
    {
        $user =  factory(User::class)->create();

        $post = factory(Post::class)->create([
            'user_id' => $user->id,
        ]);

        factory(Report::class, 10)->make(['user_id' => factory(User::class)->create()])
            ->each(function (Report $report) use ($post) {
                $post->reports()->save($report);
            });


        $this->assertNotEmpty($post->reports);
        $this->assertCount(10, $post->reports);
    }

    public function test_files_relation()
    {
        $user =  factory(User::class)->create();

        $post = factory(Post::class)->create([
            'id' => $user->id,
            'user_id' => $user->id
        ]);

        $post->files()->save(factory(File::class)->make());

        $this->assertNotEmpty($post->files);
        $this->assertCount(1, $post->files);
    }
}
