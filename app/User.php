<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

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
}
