<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\NewsIndexResource;
use App\Http\Resources\PostResource;
use App\Models\Like;
use App\Traits\TransactionTrait;
use App\Exceptions\Exception;

class PostController extends Controller
{
	use TransactionTrait;

	public function index_post(Request $request)
	{
		$request->validate(Post::FILTER_RULES);

		$query = Post::when(
			$request->has(['withDeleted', 'deletedOnly']),
			function ($query) {
				return $query->withTrashed();
			}
		)
			->when($request->has('deletedOnly'), function ($query) {
				return $query->whereNotNull('deleted_at');
			})
			->when($request->get('title'), function ($query, $title) {
				return $query->where('title', 'LIKE', "%$title%");
			})
			->where('type', Post::TYPES['REGULAR'])
			->orderBy('created_at', 'DESC');


		// TODO add an aproppiate resource
		return $query->paginate($request->pagination ?? 10);
	}


	public function index_news(Request $request)
	{
		$request->validate(Post::FILTER_RULES);

		$query = Post::when(
			$request->has(['withDeleted', 'deletedOnly']),
			function ($query) {
				return $query->withTrashed();
			}
		)
			->when($request->has('deletedOnly'), function ($query) {
				return $query->whereNotNull('deleted_at');
			})
			->when($request->get('title'), function ($query, $title) {
				return $query->where('title', 'LIKE', "%$title%");
			})
			->where('type', Post::TYPES['NEWS'])
			->orderBy('created_at', 'DESC');

		return (NewsIndexResource::collection($query->paginate($request->pagination ?? 10)))->resource;
	}

	public function show_news($id) {
		$post = Post::with(['user', 'user.profile'])->news()->where('id', $id)->first();

		if(!$post) {
			throw new Exception(trans('exception.PostController.not_found_news'), 404);
		}

		return new PostResource($post);	
	}

	public function like_post(Request $request, $id) {
		$post = Post::find($id);

		if(!$post) {
			throw new Exception(trans('exception.PostController.not_found_post'), 404);
		}

		$user = $request->user();

		if($post->likes()->where('user_id', $user->id)->exists()) {
			throw new Exception(trans('exception.PostController.already_liked'), 400);
		}

		$post->likes()->save(new Like(['user_id' => $user->id]));

        return response()->json(['message' => trans('responses.success')], 201);
	}

	public function unlike_post(Request $request, $id) {
		$post = Post::find($id);

		if(!$post) {
			throw new Exception(trans('exception.PostController.not_found_post'), 404);
		}

		$user = $request->user();

		$like =	$post->likes()->where(['user_id' => $user->id])->first();

		if(!$like) {
			throw new Exception(trans('exception.PostController.already_unliked'), 400);
		}

		if(!$like->delete()) {
			throw new Exception(trans('exception.internal_error'));
		}

		return response()->json(['message' => trans('responses.success')], 200);
	}
}
