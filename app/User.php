<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

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
        'email' => 'required|email:rfc,dns'
    ];

    public const LOGIN_RULES = [
        'username' => 'required|exists:users',
        'password' => 'required|min:6|max:45',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
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
