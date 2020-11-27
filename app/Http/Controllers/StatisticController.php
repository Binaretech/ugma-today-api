<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;

class StatisticController extends Controller
{
	public function index() {
		$users = User::selectRaw('COUNT(id) as count, \'users\' as name')
				->where('type', User::TYPES['user']);

		$admin = User::selectRaw('COUNT(id) as count, \'admins\' as name')
				->where('type', User::TYPES['admin']);


		$posts = Post::selectRaw('COUNT(id) as count, \'posts\' as name')
				->where('type', Post::TYPES['REGULAR']);	
		
		$news = Post::selectRaw('COUNT(id) as count, \'news\' as name')
				->where('type', Post::TYPES['NEWS']);
		
		return response()->json([
			'data' => $users->union($admin)->union($posts)->union($news)->get()
		]);
	}
}
