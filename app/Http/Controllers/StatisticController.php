<?php

namespace App\Http\Controllers;

use App\Models\{
    User,
    Post,
    Comment,
    Cost
};

class StatisticController extends Controller
{
    public function index()
    {
        $users = User::selectRaw('COUNT(id) as count, \'users\' as name')
            ->where('type', User::TYPES['user']);

        $admin = User::selectRaw('COUNT(id) as count, \'admins\' as name')
            ->where('type', User::TYPES['admin']);


        $comments = Comment::selectRaw('count(posts.id) as count, \'comments\' as name')
            ->join('posts', 'comments.post_id', '=', 'posts.id')
            ->where('posts.type', Post::TYPES['NEWS']);

        $news = Post::selectRaw('count(id) as count, \'news\' as name')
            ->where('type', Post::TYPES['NEWS']);

        $costs = Cost::selectRaw('count(id) as count, \'costs\' as name');

        return response()->json([
            'data' => $users->union($admin)->union($comments)->union($news)->union($costs)->get()
        ]);
    }
}
