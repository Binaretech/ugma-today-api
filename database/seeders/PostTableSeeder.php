<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    User,
    Post,
    Comment,
};

class PostTableSeeder extends Seeder
{
    public function __construct()
    {
        $this->users = User::active()->get();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->users->random(10)->each(function (User $user) {
            $posts_quantity = rand(1, 3);
            Post::factory()->times($posts_quantity)->make([
                'type' => rand(0, 1)
            ])->each(function (Post $post) use ($user) {
                $post->id = Post::generate_id($user->id);
                $user->posts()->save($post);
            });
        });

        $this->users->each(function (User $user) {
            $posts = Post::type(Post::TYPES['PUBLISHED']);
            $posts->get()
                ->each(function (Post $post) use ($user) {
                    $comments_quantity = rand(1, 3);
                    $post->comments()->saveMany(
                        Comment::factory()->times($comments_quantity)->make([
                            'user_id' => $user->id
                        ])
                    );
                });
        });

        $comments = Comment::get();
        $comments->random((count($comments) / 2))->each(function (Comment $comment) {
            $other_comment = Comment::where('post_id', $comment->post_id)
                ->where('id', '<>', $comment->id)
                ->first();
            if ($other_comment) {
                $comment->reply_to_id = $other_comment->id;
                $comment->save();
            }
        });
    }
}
