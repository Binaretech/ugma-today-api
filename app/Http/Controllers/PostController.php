<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Post,
    Like,
    User,
};
use App\Http\Resources\NewsIndexResource;
use App\Http\Resources\PostResource;
use App\Traits\TransactionTrait;
use App\Exceptions\Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

    public function show_news($id)
    {
        $post = Post::with(['user', 'user.profile'])->news()->where('id', $id)->first();

        if (!$post) {
            throw new Exception(trans('exception.PostController.not_found_news'), 404);
        }

        return new PostResource($post);
    }

    public function like_post(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            throw new Exception(trans('exception.PostController.not_found_post'), 404);
        }

        $user = $request->user();

        if ($post->likes()->where('user_id', $user->id)->exists()) {
            throw new Exception(trans('exception.PostController.already_liked'), 400);
        }

        $post->likes()->save(new Like(['user_id' => $user->id]));

        return response()->json(['message' => trans('responses.success')], 201);
    }

    public function unlike_post(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            throw new Exception(trans('exception.PostController.not_found_post'), 404);
        }

        $user = $request->user();

        $like = $post->likes()->where(['user_id' => $user->id])->first();

        if (!$like) {
            throw new Exception(trans('exception.PostController.already_unliked'), 400);
        }

        if (!$like->delete()) {
            throw new Exception(trans('exception.internal_error'));
        }

        return response()->json(['message' => trans('responses.success')], 200);
    }

    public function store_news(Request $request)
    {
        $validated_fields = $request->validate(Post::store_post_rules());

        $user = Auth::user();

        $post = new Post(
            array_merge(
                $validated_fields,
                [
                    'id' => Post::generate_id($user->id),
                    'user_id' => $user->id,
                    'created_at' => Carbon::now(),
                ]
            )
        );

        if ($user->type !== User::TYPES['admin']) throw new Exception(trans('exception.invalid_user_rights'), 401);

        if (!$post->save()) throw new Exception(trans('exception.error_saving'), 500);

        return response()->json(['message' => trans('responses.success')]);
    }

    public function update_news(Request $request, $id)
    {
        $request->request->add(['id' => $id]);
        $request->validate(Post::update_post_rules());

        $news = Post::find($request->id);
        $request->request->remove('id');
        $news->fill($request->all());

        if (!$news->save())
            throw new Exception(trans('exception.internal_error'));

        return response()->json([
            'message' => trans('responses.success')
        ]);
    }

    public function delete_news(Request $request, $id)
    {
        $request->request->add(['id' => $id]);
        $request->validate(Post::delete_post_rules());

        $news = Post::find($request->id);

        if (!$news->delete())
            throw new Exception(trans('exception.internal_error'));

        return response()->json([
            'message' => trans('responses.success'),
        ]);
    }
}
