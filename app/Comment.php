<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function reply()
    {
        return $this->belongsTo(Comment::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'reply_to_id');
    }
}
