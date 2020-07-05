<?php

use Illuminate\Database\Seeder;
use App\{
    User,
    Post,
    Comment,
};

class PostTableSeeder extends Seeder
{
    public function __construct()
    {
        $this->users = User::where('status', User::STATUS['ACTIVE'])->get();
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
            factory(Post::class, $posts_quantity)->make([
                'type' => rand(0, 1)
            ])->each(function (Post $post) use ($user) {
                $post->id = Post::generate_id($user->id);
                $user->posts()->save($post);
            });
        });

        $this->users->each(function (User $user) {
            Post::where('type', Post::TYPES['PUBLISHED'])->get()
                ->each(function (Post $post) use ($user) {
                    $comments_quantity = rand(1, 3);
                    $post->comments()->saveMany(
                        factory(Comment::class, $comments_quantity)->make([
                            'user_id' => $user->id
                        ])
                    );
                });
        });
    }
}
