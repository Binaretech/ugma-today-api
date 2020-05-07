<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'username', 'password', 'type'
    ];

    protected $hidden = [
        'password',
    ];

    public const TYPES = [
        0 => 'USER',
        1 => 'ADMIN',
        'USER' => 0,
        'ADMIN' => 1,
    ];

    public const STATUS = [
        0 => 'ACTIVE',
        1 => 'BANNED',
        'ACTIVE' => 0,
        'BANNED' => 1,
    ];

    public const REGISTER_RULES = [
        'username' => 'required|unique:users|min:3|max:40',
        'password' => 'required|min:6|max:45',
        'name' => 'required|min:2|max:50',
        'lastname' => 'required|min:2|max:50',
        'email' => 'required|unique:profiles|email:rfc,dns'
    ];

    public const LOGIN_RULES = [
        'username' => 'required|exists:users',
        'password' => 'required|min:6|max:45',
    ];

    public const PASSWORD_RESET_RULES = [
        'email' => 'required|exists:profiles',
    ];

    public const FILTER_RULES = [
        'type' => 'sometimes|required|numeric|between:0,1',
        'status' => 'sometimes|required|numeric|between0,1',
        'with_deleted' => 'sometimes|required|boolean',
        'deleted_only' => 'sometimes|required|boolean',
    ];

    public const UPDATE_RULES = [
        'username' => 'sometimes|unique:users|min:3|max:40',
        'password' => 'sometimes|min:6|max:45',
        'name' => 'sometimes|min:2|max:50',
        'lastname' => 'sometimes|min:2|max:50',
        'email' => 'sometimes|unique:profiles|email:rfc,dns'
    ];

    public static function reset_rules()
    {
        return [
            'token' => ['required', 'exists:password_resets', function ($attribute, $value, $fail) {
                if (optional(PasswordReset::where('token', $value)->first())->expire_at < Carbon::now()) {
                    $fail(trans('validation.expired', ['attribute' => $attribute]));
                }
            }],
            'password' => ['required', 'min:6', 'max:45']
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', User::STATUS['ACTIVE']);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function password_reset()
    {
        return $this->hasOne(PasswordReset::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function made_reports()
    {
        return $this->hasMany(Report::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function file()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
