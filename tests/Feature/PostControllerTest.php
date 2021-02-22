<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\{User, Post};
use Laravel\Passport\Passport;

class PostControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_index_news()
    {
        $user = User::factory()->create();
        $user->posts()->saveMany(Post::factory()->times(10)->make(['type' => Post::TYPES['NEWS']]));

        $this->get('api/news')
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'ids'
            ]);
    }

    public function test_show_news()
    {
        $user = User::factory()->create();
        $post = $user->posts()->save(Post::factory()->make(['type' => Post::TYPES['NEWS']]));

        $this->get('api/news/' . $post->id)
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'type' => Post::TYPES['NEWS'],
                    'user' => [
                        'id' => $post->user->id,
                        'status' => $post->user->status,
                        'type' => $post->user->type,
                        'username' => $post->user->username,
                        'profileImage' => $post->user->profile_image->url,
                        'profile' => [
                            'email' => $post->user->profile->email,
                            'fullname' => $post->user->profile->name . ' ' . $post->user->profile->lastname,
                            'name' => $post->user->profile->name,
                            'lastname' =>  $post->user->profile->lastname,
                        ]
                    ]
                ]
            ]);
    }

    public function test_like_cost()
    {
        $post = User::factory()->create()->posts()->save(Post::factory()->make());
        Passport::actingAs(User::factory()->create(), ['user']);

        $this->post('api/post/like/' . $post->id)
            ->assertCreated();
    }

    public function test_fail_like_cost()
    {
        $post = User::factory()->active()->create()->posts()->save(Post::factory()->make());
        Passport::actingAs(User::factory()->create(), ['user']);

        $this->post('api/post/like/' . $post->id)
            ->assertCreated();

        $this->post('api/post/like/' . $post->id)
            ->assertStatus(400);
    }

    public function test_store_news()
    {
        Passport::actingAs(User::factory()->admin()->active()->create(), ['admin']);

        $this->post('api/admin/news', [
            'title' => $this->faker->realText(20),
            'content' => $this->faker->text(100),
            'type' => Post::TYPES['NEWS'],
        ])->assertCreated()->assertJsonStructure([
            'message'
        ]);
    }

    public function test_update_news()
    {
        Passport::actingAs(User::factory()->admin()->active()->create(), ['admin']);

        $this->post('api/admin/news', [
            'title' => $this->faker->realText(20),
            'content' => $this->faker->text(100),
            'type' => Post::TYPES['NEWS'],
        ]);

        $news = Post::first();

        $this->put("api/admin/news/$news->id", [
            'title' => $this->faker->realText(20),
            'content' => $this->faker->text(100),
            'type' => Post::TYPES['NEWS'],
        ])->assertOk()->assertJsonStructure([
            'message'
        ]);
    }

    public function test_delete_news()
    {
        Passport::actingAs(User::factory()->admin()->active()->create(), ['admin']);

        $this->post('api/admin/news', [
            'title' => $this->faker->realText(20),
            'content' => $this->faker->text(100),
            'type' => Post::TYPES['NEWS'],
        ]);

        $news = Post::first();

        $this->delete("api/admin/news/$news->id")->assertOk()->assertJsonStructure([
            'message'
        ]);
    }
}
