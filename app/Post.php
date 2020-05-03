<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    const TYPES = [
        0 => 'DRAFT',
        1 => 'PUBLISHED',
        'DRAFT' => 0,
        'PUBLISHED' => 1
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
