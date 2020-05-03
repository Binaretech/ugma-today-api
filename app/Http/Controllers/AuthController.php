<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Mail\PasswordResetMail;
use App\PasswordReset;
use App\Profile;
use App\Traits\TransactionTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

        if (!password_verify($request_data['password'], $user->password)) {
            throw new AuthenticationException(trans('exception.login'));
        }

        return new AuthResource($user);
    }

    public function password_reset_email(Request $request)
    {
        $request_data = $request->validate(User::PASSWORD_RESET_RULES);

        $user = User::join('profiles', 'profiles.user_id', 'users.id')
            ->where('profiles.email', $request_data['email'])
            ->first();

        $token = base64_encode($user->id . password_hash(time() . rand(-99999, 99999), PASSWORD_DEFAULT) . uniqid());
        $expire = Carbon::now()->addHours(2);
        $user->password_reset()->save(new PasswordReset([
            'token' => $token,
            'expire_at' => $expire
        ]));

        Mail::to($user->profile->email)->queue(new PasswordResetMail($token, $expire));

        return response()->json([
            'message' => trans('responses.password_reset'),
        ]);
    }

    public function reset_password(Request $request)
    {
        $request_data = $request->validate(User::reset_rules());

        $user = User::join('password_resets', 'password_resets.user_id', 'users.id')
            ->where('password_resets.token', $request_data['token'])->first();

        $user->password = $request_data['password'];
        $user->save();

        return response()->json([
            'message' => trans('responses.reset_password'),
        ]);
    }
}
