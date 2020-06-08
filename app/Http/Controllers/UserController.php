<?php

namespace App\Http\Controllers;

use App\Exceptions\DatabaseException;
use App\Http\Resources\UserResource;
use App\Traits\TransactionTrait;
use App\User;
use Illuminate\Http\Request;

/**
 * @authenticated
 * 
 * @group User
 * 
 * User related routes
 */
class UserController extends Controller
{
    use TransactionTrait;

    public function __construct()
    {
        $this->middleware('scope:ADMIN')->only(['index', 'ban', 'active']);
    }

    /**
     * Get users list
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * 
     * @apiResourceCollection  App\Http\Resources\UserResource
     * @apiResourceModel  App\User
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $request->validate(User::FILTER_RULES);

        $query = User::when(
            $request->has(['with_deleted', 'deleted_only']),
            function ($query) {
                return $query->withTrashed();
            }
        )
            ->when($request->has('deleted_only'), function ($query) {
                return $query->whereNotNull('deleted_at');
            })
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->input('status'));
            })
            ->when($request->has('type'), function ($query) use ($request) {
                return $query->where('type', $request->input('type'));
            });

        return UserResource::collection($query->paginate($request->pagination ?? 10));
    }

    /**
     * Display the specified user.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     * 
     * @urlParam user required User by id. Example: 2
     * @response {
        "data": {
            "id": null,
            "username": "era.hickle",
            "status": 0,
            "type": 0,
            "profile": {
                "name": "Jaeden Padberg",
                "lastname": "West",
                "email": "lia.oconnell@example.net"
                }
            }
        }
     */
    public function show(User $user)
    {
        return new UserResource($user->load('profile'));
    }

    /**
     * Update the specified user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @bodyParam username optional Change username
     * @bodyParam password optional Change password
     * @bodyParam name optional Change name
     * @bodyParam lastname optional Change lastname
     * @bodyParam email optional Change email. Example lorem@mail.com
     * 
     * @response {
     *  "message": "Success."
     * }
     */
    public function update(Request $request)
    {
        $request_data = $request->validate(User::UPDATE_RULES);
        $user = $request->user();

        self::transaction(function () use ($request_data, $user) {
            $user->update($request_data);
            $user->profile->update($request_data);
        });
    }

    /**
     * Ban user (admin only)
     *
     * @param User $user
     * @return void
     * @urlParam user required User by id. Example: 2
     * @response {
     *  "message": "Success."
     * }
     */
    public function ban(User $user)
    {
        $user->status = User::STATUS['BANNED'];
        if (!$user->save()) {
            throw new DatabaseException(trans('exception.internal_error'), 500);
        }

        return response()->json(['message' => trans('responses.success')]);
    }

    /**
     * Active user (admin only)
     *
     * @param User $user
     * @return void
     * @urlParam user required User by id. Example: 2
     * @response {
     *  "message": "Success."
     * }
     */
    public function active(User $user)
    {
        $user->status = User::STATUS['ACTIVE'];
        if (!$user->save()) {
            throw new DatabaseException(trans('exception.internal_error'), 500);
        }

        return response()->json(['message' => trans('responses.success')]);
    }

    /**
     * Remove the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @urlParam user required User by id. Example: 2
     * 
     * @response {
     *  "message": "Success."
     * }
     */
    public function destroy(Request $request)
    {
        $request->user()->delete();
        return response()->json(['message' => trans('responses.success')]);
    }
}
