<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\NewsIndexResource;
use App\Http\Resources\PostResource;
use Exception;

class PostController extends Controller
{

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
}
