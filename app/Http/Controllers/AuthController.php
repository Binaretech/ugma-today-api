<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Mail\PasswordResetMail;
use App\PasswordReset;
use App\Profile;
use App\Traits\TransactionTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
     * 
     * @bodyParam username string required New username in the app.
     * @bodyParam password string required For authenticate with the app.
     * @bodyParam name string required User's first name. Example: lorem@gmail.com
     * @bodyParam lastname string required User's last name.
     * @bodyParam email email required User's email.
     * @bodyParam withTimestamps optional Show records timestamps
     * @response {
     * "data": {
     *     "id": 73,
     *     "username": "alanbrito",
     *     "type": 0,
     *     "profile": {
     *         "user_id": 73,
     *         "name": "Alan",
     *         "lastname": "Brito",
     *         "email": "alanbrito@gmail.com"
     *     },
     *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMDEyNDRjMTExNjMwMTJmMTQxOWRjNjYxZGRiOGVmZGE1MjFlZjA3NTI5MGE2OWFjNjBlYTRiOGZmMjljYTIwNWYxOTg0ODZiOWVmMTgwNzUiLCJpYXQiOjE1OTE0NjM4MjgsIm5iZiI6MTU5MTQ2MzgyOCwiZXhwIjoxNjIyOTk5ODI4LCJzdWIiOiI3MyIsInNjb3BlcyI6WyJVU0VSIl19.cjYa0EOWRdakcdzBrLb2GF5ytFHYyKz9opCD1NJlt-l9Bw5xjrv2ShI8n2IchuvqUjT6LKb_GjdhcMNg4nWebkj5BTOn-Owj30qg61Mob_wZ1Q2otnJiSHoPgZHnHoAip6SdTlkYBPMtRIwBI1fp1ZBtwaquIKx2hrWYI3V5T6ZTAqeBo_72trW3fVARkMDXav_d3Kji-rrtiv02XgMQXmEWs0eA0ujXFjapi5VoR-nYxnzP3FlBnmtiTpM6mjV9f5Dr7385rBFGGb-c30eR46IeEkUwaH4ejEEPp6zsqYsnRftDn0O0XvW3vlvraRJDU9KHusA3HAzk7vqD9agquZAqGvEqm7pi5KdwLUIjCkekSCdYSSnGSvbQQXT8KBjhghQdem0fnkE8eULI73Zv1YQxLpY6PIVEoM4uGeVx1U3UMmKhgCIwyugJ6h1ypt55hves6zh8aYV8EYF3vDi3uFCkkXk731psrt9nihTQxMcilXY3Bz5SIqb6-Txk72SwoTrRqb1ZCI8a7VB5mYTkw_xaOPB3visszQCFOUVPXIliJRZ7nrHj9ANUCbrgqyqbFirkyXCwi6pD_Lh-0d2i_4IJoilFQfAvzmTIUj8FCa9KuPqIHdIaseULZjIHnrOD3ZYpX5rfUJDBoutVBEBfckw9Lof-mfQlsaWCsamGt20"
     *  }
     * }
     */
    public function register(Request $request)
    {
        $request_data = $request->validate(User::REGISTER_RULES);

        $user = new User();

        self::transaction(function () use ($request_data, &$user) {
            $user = User::create(array_merge($request_data, ['type' => User::TYPES['USER']]));
            $user->profile()->save(new Profile($request_data));
        });

        return new AuthResource($user);
    }

    /**
     * Authenticate user
     *
     * @param Request $request
     * @return UserResource
     * 
     * @throws Exception on creation failure
     * 
     * @bodyParam username string required New username in the app.
     * @bodyParam password string required For authenticate with the app.
     * @bodyParam withTimestamps optional Show records timestamps
     * @response {
     * "data": {
     *     "id": 73,
     *     "username": "alanbrito",
     *     "type": 0,
     *     "profile": {
     *         "user_id": 73,
     *         "name": "Alan",
     *         "lastname": "Brito",
     *         "email": "alanbrito@gmail.com"
     *     },
     *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMDEyNDRjMTExNjMwMTJmMTQxOWRjNjYxZGRiOGVmZGE1MjFlZjA3NTI5MGE2OWFjNjBlYTRiOGZmMjljYTIwNWYxOTg0ODZiOWVmMTgwNzUiLCJpYXQiOjE1OTE0NjM4MjgsIm5iZiI6MTU5MTQ2MzgyOCwiZXhwIjoxNjIyOTk5ODI4LCJzdWIiOiI3MyIsInNjb3BlcyI6WyJVU0VSIl19.cjYa0EOWRdakcdzBrLb2GF5ytFHYyKz9opCD1NJlt-l9Bw5xjrv2ShI8n2IchuvqUjT6LKb_GjdhcMNg4nWebkj5BTOn-Owj30qg61Mob_wZ1Q2otnJiSHoPgZHnHoAip6SdTlkYBPMtRIwBI1fp1ZBtwaquIKx2hrWYI3V5T6ZTAqeBo_72trW3fVARkMDXav_d3Kji-rrtiv02XgMQXmEWs0eA0ujXFjapi5VoR-nYxnzP3FlBnmtiTpM6mjV9f5Dr7385rBFGGb-c30eR46IeEkUwaH4ejEEPp6zsqYsnRftDn0O0XvW3vlvraRJDU9KHusA3HAzk7vqD9agquZAqGvEqm7pi5KdwLUIjCkekSCdYSSnGSvbQQXT8KBjhghQdem0fnkE8eULI73Zv1YQxLpY6PIVEoM4uGeVx1U3UMmKhgCIwyugJ6h1ypt55hves6zh8aYV8EYF3vDi3uFCkkXk731psrt9nihTQxMcilXY3Bz5SIqb6-Txk72SwoTrRqb1ZCI8a7VB5mYTkw_xaOPB3visszQCFOUVPXIliJRZ7nrHj9ANUCbrgqyqbFirkyXCwi6pD_Lh-0d2i_4IJoilFQfAvzmTIUj8FCa9KuPqIHdIaseULZjIHnrOD3ZYpX5rfUJDBoutVBEBfckw9Lof-mfQlsaWCsamGt20"
     *  }
     * }
     */
    public function login(Request $request)
    {
        $request_data = $request->validate(User::LOGIN_RULES);
        $user = User::where('username', $request_data['username'])->first();

        if (!password_verify($request_data['password'], $user->password)) {
            throw new AuthenticationException(trans('exception.login'));
        }

        return new AuthResource($user);
    }

    /**
     * Send email to recover password
     *
     * @param Request $request
     * @return UserResource
     * 
     * @throws Exception on creation failure
     * 
     * @bodyParam email required Password recovery email address
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
     * 
     * @bodyParam token required Password recovery token
     * @bodyParam password required New password
     */
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
