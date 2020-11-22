<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
	
	public function index_post(Request $request)
   	{
		$request->validate(Post::FILTER_RULES);

        $query = Post::when(
            $request->has(['withDeleted', 'deletedOnly']),
            function ($query) {
                return $query->withTrashed();
            })
            ->when($request->has('deletedOnly'), function ($query) {
                return $query->whereNotNull('deleted_at');
			})
			->when($request->get('title'), function($query, $title) {
				return $query->where('title', 'LIKE', "%$title%");
			})
			->where('type', Post::TYPES['REGULAR'])
			->orderBy('created_at', 'DESC');

		
		return (PostResource::collection($query->paginate($request->pagination?? 10)))->resource;
	}

	
	public function index_news(Request $request)
   	{
		$request->validate(Post::FILTER_RULES);

        $query = Post::when(
            $request->has(['withDeleted', 'deletedOnly']),
            function ($query) {
                return $query->withTrashed();
            })
            ->when($request->has('deletedOnly'), function ($query) {
                return $query->whereNotNull('deleted_at');
            })
            ->when($request->get('title'), function($query, $title) {
				return $query->where('title', 'LIKE', "%$title%");
			})
			->where('type', Post::TYPES['NEWS'])
			->orderBy('created_at', 'DESC');

		
		return (PostResource::collection($query->paginate($request->pagination?? 10)))->resource;
	}   

	

	public function index_admin(Request $request)
   	{
		$request->validate(array_merge(Post::FILTER_RULES, ['type' => 'sometimes|min:0|max:2']));

		$query = Post::when(
			$request->has(['withDeleted', 'deletedOnly']),
			function ($query) {
				return $query->withTrashed();
			})
			->when($request->has('deletedOnly'), function ($query) {
				return $query->whereNotNull('deleted_at');
			})
			->when($request->get('title'), function($query, $title) {
				return $query->where('title', 'LIKE', "%$title%");
			})
			->when($request->has('type'), function ($query) use ($request) {
				return $query->where('type', $request->input('type'));
			});

		return PostResource::collection($query->paginate($request->pagination?? 10));
	}   
}
