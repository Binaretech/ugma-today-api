<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
	public function store(Request $request, Post $post) {
		$request_data = $request->validate(Comment::STORE_RULES);
		$comment = $post->comments()->save(new Comment(array_merge(
			$request_data, [
				'user_id' => $request->user()->id
			]
		)));

		if(!$comment) {
			throw new Exception(trans('exception.CommentController.store'));
		}

		return new CommentResource($comment);
	}	
}
