<?php

namespace Tests\Unit;

use App\Comment;
use App\Feedback;
use App\Like;
use App\Post;
use App\Report;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function test_profile_relation()
    {
        $user = factory(User::class)->create();
        $this->assertNotNull($user->profile);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_post_relation()
    {
        $user = factory(User::class)->create();
        $user->posts()->saveMany(factory(Post::class, 10)->make());

        $this->assertNotEmpty($user->posts);
        $this->assertCount(10, $user->posts);
    }

    public function test_comments_relation()
    {
        $user = factory(User::class)->create();

        factory(Post::class)->create([
            'user_id' => factory(User::class)->create(),
        ])->each(function (Post $post) use ($user) {
            $post->comments()->saveMany(factory(Comment::class, 10)->make([
                'user_id' => $user->id,
            ]));
        });

        $this->assertNotEmpty($user->comments);
        $this->assertCount(10, $user->comments);
    }

    public function test_likes_relation()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)
            ->create(['user_id' => factory(User::class)->create()]);

        $post->likes()
            ->save(new Like([
                'user_id' => $user->id,
            ]));

        $this->assertNotEmpty($user->likes);
        $this->assertCount(1, $user->likes);
    }

    public function test_made_reports_relation()
    {
        $user = factory(User::class)->create();

        factory(Report::class, 10)->make(['user_id' => $user->id])
            ->each(function (Report $report) {
                switch ($this->faker->numberBetween(0, 2)) {
                    case 0:
                        factory(Post::class)
                            ->create(['user_id' => factory(User::class)->create()])
                            ->reports()->save($report);
                        break;
                    case 1:
                        factory(Comment::class)
                            ->create([
                                'user_id' => factory(User::class)->create(),
                                'post_id' => factory(Post::class)
                                    ->create(['user_id' => factory(User::class)->create()])
                            ])
                            ->reports()->save($report);
                        break;
                    default:
                        factory(User::class)
                            ->create()
                            ->reports()->save($report);
                        break;
                }
            });


        $this->assertNotEmpty($user->made_reports);
        $this->assertCount(10, $user->made_reports);
    }

    public function test_reports_relation()
    {
        $user = factory(User::class)->create();

        factory(Report::class, 10)->make(['user_id' => $user->id])
            ->each(function (Report $report) use ($user) {
                $user->reports()->save($report);
            });


        $this->assertNotEmpty($user->reports);
        $this->assertCount(10, $user->reports);
    }

    public function test_feedback_relation()
    {
        $user = factory(User::class)->create();

        $user->feedback()->saveMany(factory(Feedback::class, 10)->create());

        $this->assertNotEmpty($user->feedback);
        $this->assertCount(10, $user->feedback);
    }
}
