<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Mail\PasswordResetMail;
use App\Models\PasswordReset;
use App\Models\Profile;
use App\Models\User;
use App\Traits\TransactionTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\Exception;

/**
 * @group Auth
 * 
 * Authentication related routes
 */
class AuthController extends Controller
{
    use TransactionTrait;

    /**
     * Register new user
     *
     * @param Request $request
     * @return UserResource
     * 
     * @throws Exception on creation failure
     */
    public function register(Request $request)
    {
        $request_data = $request->validate(User::REGISTER_RULES);

        $user = new User();

        self::transaction(function () use ($request_data, &$user) {
            $user = User::create(array_merge($request_data, ['type' => User::TYPES['user']]));
            $user->profile()->save(new Profile($request_data));
        });

		return new AuthResource($user, $user->createToken('asd', [], $request_data['password'])->accessToken);
    }

    /**
     * Authenticate user
     *
     * @param Request $request
     * @return UserResource
     * 
     * @throws Exception on creation failure
     * 
     */
    public function login(Request $request)
    {
        $request_data = $request->validate(User::LOGIN_RULES);

        $user = User::get_by_username_or_email($request, $request->routeIs('/api/admin/login'));
		
		if (!password_verify($request_data['password'], $user->password)) {
			throw new Exception(trans('exception.login'));
        }

		return new AuthResource($user, $user->createToken('asd', [], $request_data['password'])->accessToken);
    }

    /**
     * Send email to recover password
     *
     * @param Request $request
     * @return UserResource
     * 
     * @throws Exception on creation failure
     * 
     */
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


    /**
     * Reset password
     *
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     * 
     * @throws Exception on creation failure
     */
    public function reset_password(Request $request)
    {
        $request_data = $request->validate(User::reset_rules());

        self::transaction(function () use ($request_data) {
            $password_reset = PasswordReset::where('token', $request_data['token'])->first();

            $user = $password_reset->user;
            $user->password = $request_data['password'];
            $user->save();

            $password_reset->delete();
        });

        return response()->json([
            'message' => trans('responses.reset_password'),
        ]);
    }

    /*
     * Logout the current user session 
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::user()->token()->revoke();
        return response()->json([
            'message' => trans('responses.AuthController.logout.success')
        ]);
    }
}
