<?php

namespace Database\Seeders;

use App\Models\{
    User,
    Post,
    Comment,
    Like
};
use Illuminate\Database\Seeder;

class LikeTableSeeder extends Seeder
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
        Post::all()->each(function (Post $post) {
            $likes_quantity = rand(1, 3);
            for ($i = 0; $i < $likes_quantity; $i++) {
                $user_id = $this->users->random()->id;

                if ($post->likes()->where('user_id', $user_id)->exists()) {
                    continue;
                }

                $post->likes()->save(new Like([
                    'user_id' => $user_id
                ]));
            }
        });

        Comment::all()->each(function (Comment $comment) {
            $likes_quantity = rand(1, 3);
            for ($i = 0; $i < $likes_quantity; $i++) {
                $user_id = $this->users->random()->id;

                if ($comment->likes()->where('user_id', $user_id)->exists()) {
                    continue;
                }

                $comment->likes()->save(new Like([
                    'user_id' => $user_id
                ]));
            }
        });
    }
}
