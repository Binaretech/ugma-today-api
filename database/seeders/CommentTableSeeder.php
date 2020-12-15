<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
	{
		$users = User::get();
	
		Post::get()->each(function(Post $post) use($users) {
			$quantity = rand(5, 10);
			$post->comments()->saveMany(Comment::factory()->times($quantity)->make(['user_id' => $users->random()->id]));
		});

		Comment::get()->each(function(Comment $comment) use($users){
			if(rand(0,1) === 0) return;

			$quantity = rand(2,5);
			
			$replies = Comment::factory()
				->times($quantity)
				->make([
					'user_id' => $users->random()->id,
					'post_id' => $comment->post_id,
				]);

			$comment->replies()->saveMany($replies);	
		});	
    }
}
