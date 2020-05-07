<?php

namespace App\Http\Controllers;

use App\Exceptions\DatabaseException;
use App\Http\Resources\UserResource;
use App\Traits\TransactionTrait;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use TransactionTrait;

    public function __construct()
    {
        $this->middleware('scope:ADMIN')->only(['index', 'ban', 'active']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->validate(User::FILTER_RULES);
        $query = new User();

        if (isset($filters['with_deleted']) || isset($filters['deleted_only'])) {
            $query->withTrashed();
        }

        if (isset($filters['deleted_only'])) {
            $query->whereNotNull('deleted_at');
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return UserResource::collection($query->paginate($request->pagination ?? 10));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user->load('profile'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request_data = $request->validate(User::UPDATE_RULES);
        $user = $request->user();

        self::transaction(function () use ($request_data, $user) {
            $user->update($request_data);
            $user->profile->update($request_data);
        });

        return response()->json(['message' => trans('responses.success')]);
    }

    public function ban(User $user)
    {
        $user->status = User::STATUS['BANNED'];
        if (!$user->save()) {
            throw new DatabaseException(trans('exception.internal_error'), 500);
        }

        return response()->json(['message' => trans('responses.success')]);
    }

    public function active(User $user)
    {
        $user->status = User::STATUS['ACTIVE'];
        if (!$user->save()) {
            throw new DatabaseException(trans('exception.internal_error'), 500);
        }

        return response()->json(['message' => trans('responses.success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->user()->delete();
        return response()->json(['message' => trans('responses.success')]);
    }
}
