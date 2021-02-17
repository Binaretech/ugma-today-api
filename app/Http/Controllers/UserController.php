<?php

namespace App\Http\Controllers;

use App\Exceptions\DatabaseException;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use App\Traits\TransactionTrait;
use App\Models\User;
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

  /**
   * Get users list
   *
   * @param Request $request
   * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
   *
   * @apiResourceCollection  App\Http\Resources\UserResource
   * @apiResourceModel  App\Models\User
   */
  public function index(Request $request): \App\CustomClasses\LengthAwarePaginator
  {
    $request->validate(User::FILTER_RULES);

    $query = User::when(
      $request->has(['withDeleted', 'deletedOnly']),
      function ($query) {
        return $query->withTrashed();
      }
    )
      ->when($request->has('deletedOnly'), function ($query) {
        return $query->whereNotNull('deleted_at');
      })
      ->when($request->has('status'), function ($query) use ($request) {
        return $query->where('status', $request->input('status'));
      })
      ->when($request->has('type'), function ($query) use ($request) {
        return $query->where('type', $request->input('type'));
      });

    return (UserResource::collection($query->paginate($request->pagination ?? 10)))->resource;
  }

  /**
   * Display the specified user.
   *
   * @param  User  $user
   * @return \Illuminate\Http\Response
   *
   * @urlParam user required User by id. Example: 2
   * @response {
   *   "data": {
   *       "id": null,
   *       "username": "era.hickle",
   *       "status": 0,
   *       "type": 0,
   *       "profile": {
   *           "name": "Jaeden Padberg",
   *           "lastname": "West",
   *           "email": "lia.oconnell@example.net"
   *           }
   *       }
   *   }
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

    $different_username = $user->username !== $request->username;
    $different_email = $user->profile->email !== $request->email;

    if (($different_username && $request->has('username'))
      || ($different_email && $request->has('email'))
    ) {
      $errors = [];
      $username_exists = User::where('username', $request->username)->count();
      $email_exists = Profile::where('email', $request->email)->count();

      $is_username_duplicated = $different_username && $username_exists > 0;
      $is_email_duplicated = $different_email && $email_exists > 0;

      if ($is_username_duplicated)
        $errors = array_merge($errors, [
          'username' => trans('exception.username_used')
        ]);

      if ($is_email_duplicated)
        $errors = array_merge($errors, [
          'email' => trans('exception.email_used')
        ]);

      if ($is_username_duplicated || $is_email_duplicated)
        return response()->json([
          'message' => trans('exception.invalid_data'),
          'errors' => $errors,
        ], 400);
    }

    self::transaction(function () use ($request_data, $user) {
      $user->update($request_data);
      $user->profile->update($request_data);
    });

    return response()->json(['message' => trans('responses.success')]);
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
   * @return \Illuminate\Http\Response
   * @urlParam user required User by id. Example: 2
   *
   * @response {
   *  "message": "Success."
   * }
   */
  public function destroy(Request $request)
  {
    if (!$request->user()->delete()) {
      return response()->json(['message' => trans('responses.UserController.destroy')]);
    }

    return response()->json(['message' => trans('responses.success')]);
  }
}
