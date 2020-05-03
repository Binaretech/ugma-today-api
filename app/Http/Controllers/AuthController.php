<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Profile;
use App\Traits\TransactionTrait;
use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use TransactionTrait;

    public function register(Request $request)
    {
        $request_data = $request->validate(User::REGISTER_RULES);

        $user = null;

        self::transaction(function () use ($request_data, &$user) {
            $user = User::create(array_merge($request_data, ['type' => User::TYPES['USER']]));
            $user->profile()->save(new Profile($request_data));
        });

        return new AuthResource($user);
    }

    public function login(Request $request)
    {
        $request_data = $request->validate(User::LOGIN_RULES);
        $user = User::where('username', $request_data['username'])->first();

        if (password_verify($request_data['password'], $user->password)) {
            return new AuthResource($user);
        }

        throw new AuthenticationException();
    }
}
