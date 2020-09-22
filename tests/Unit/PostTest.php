<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\File;
use App\Models\Like;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relation()
    {
        $user = User::factory()->active()
            ->create();

        $posts = $user->posts()->save(Post::factory()->make());

        $this->assertNotEmpty($posts);
        $this->assertNotNull($posts->user);
    }

    public function test_comments_relation()
    {
        $user =  User::factory()->active()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id
        ]);

        $post->comments()
            ->saveMany(Comment::factory()->times(10)
                ->make(['user_id' => User::factory()->active()->create()]));

        $this->assertNotEmpty($post->comments);
        $this->assertCount(10, $post->comments);
    }

    public function test_likes_relation()
    {
        $user =  User::factory()->active()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id
            ]);

        $post->likes()
            ->save(new Like([
                'user_id' => User::factory()->active()->create()->id,
            ]));

        $this->assertNotEmpty($post->likes);
        $this->assertCount(1, $post->likes);
    }

    public function test_reports_relation()
    {
        $user =  User::factory()->active()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);
        $post->reports()->saveMany(
            Report::factory()->times(10)->make(['user_id' => User::factory()->active()->create()])
        );

        $this->assertNotEmpty($post->reports);
        $this->assertCount(10, $post->reports);
    }

    public function test_files_relation()
    {
        $user =  User::factory()->active()->create();

        $post = Post::factory()->create([
            'id' => $user->id,
            'user_id' => $user->id
        ]);

        $post->files()->save(File::factory()->make());

        $this->assertNotEmpty($post->files);
        $this->assertCount(1, $post->files);
    }
}
