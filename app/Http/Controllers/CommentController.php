<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
	public function index(Request $request, Post $post)
	{
		return CommentResource::collection($post->comments()->orderBy('created_at', 'ASC')->paginate($request->pagination ?? 10))->resource;
	}

	public function index_replies(Request $request, Comment $comment)
	{
		return CommentResource::collection($comment->replies()->orderBy('created_at', 'ASC')->paginate($request->pagination ?? 10))->resource;
	}

	public function store(Request $request, Post $post)
	{
		$request_data = $request->validate(Comment::STORE_RULES);
		$comment = $post->comments()->save(new Comment(array_merge(
			$request_data,
			[
				'user_id' => $request->user()->id
			]
		)));

		if (!$comment) {
			throw new Exception(trans('exception.CommentController.store'));
		}

		return new CommentResource($comment);
	}

	public function reply(Request $request, Comment $comment)
	{
		$request_data = $request->validate(Comment::STORE_RULES);

		$reply = $comment->replies()->save(new Comment(array_merge(
			$request_data,
			[
				'user_id' => $request->user()->id,
				'post_id' => $comment->post_id,
			]
		)));

		if (!$reply) {
			throw new Exception(trans('exception.CommentController.store'));
		}

		return new CommentResource($reply);
	}

	public function like(Request $request, Comment $comment)
	{
		$user = $request->user();

		if ($comment->likes()->where('user_id', $user->id)->exists()) {
			throw new Exception(trans('exception.CommentController.already_liked'), 400);
		}

		$comment->likes()->save(new Like(['user_id' => $user->id]));

		return response()->json(['message' => trans('responses.success')], 201);
	}

	public function unlike(Request $request, Comment $comment)
	{
		$user = $request->user();

		$like =	$comment->likes()->where(['user_id' => $user->id])->first();

		if (!$like) {
			throw new Exception(trans('exception.CommentController.already_unliked'), 400);
		}

		if (!$like->delete()) {
			throw new Exception(trans('exception.internal_error'));
		}

		return response()->json(['message' => trans('responses.success')], 200);
	}
}
