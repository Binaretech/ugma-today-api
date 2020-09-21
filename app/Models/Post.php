<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function (Post $post) {
            $post->id = self::generate_id($post->user_id);
        });
    }

    const TYPES = [
        0 => 'DRAFT',
        1 => 'PUBLISHED',
        'DRAFT' => 0,
        'PUBLISHED' => 1
    ];

    protected $fillable = [
        'title', 'content', 'user_id', 'type'
    ];

    protected $keyType = 'string';

    public $incrementing = false;


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

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public static function generate_id(int $user_id)
    {
        $count = (optional(Post::selectRaw('COUNT(*) as count')->where('user_id', $user_id)->first())->count ?: 0) + 1;
        return "$user_id-$count";
    }
}
