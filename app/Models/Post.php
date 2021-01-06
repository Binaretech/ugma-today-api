<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    protected $appends = ['liked_by_user'];

    protected static function booted()
    {
        static::creating(function (Post $post) {
            $post->id = self::generate_id($post->user_id);
        });
    }

    const TYPES = [
        0 => 'DRAFT',
        1 => 'REGULAR',
        2 => 'NEWS',
        'DRAFT'   => 0,
        'REGULAR' => 1,
        'NEWS'    => 2,
    ];

    public const FILTER_RULES = [
        'title'          => 'sometimes|string',
        'withDeleted' => 'sometimes|boolean',
        'deletedOnly' => 'sometimes|boolean',
    ];


    protected $fillable = [
        'title', 'content', 'user_id', 'type'
    ];

    protected $keyType = 'string';

    public $incrementing = false;


    public static function store_post_rules()
    {
        return [
            'title' => 'required|string|min:3',
            'content' => 'required|string|min:10',
            'type' => 'required|in:' . join(',', self::TYPES),
        ];
    }

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

    public function getLikedByUserAttribute()
    {
        $user = Auth::guard('api')->user();

        if (!$user) return false;

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }


    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRegular($query)
    {
        return $query->where('posts.type', Post::TYPES['REGULAR']);
    }

    public function scopeNews($query)
    {
        return $query->where('posts.type', Post::TYPES['NEWS']);
    }

    public static function generate_id(int $user_id)
    {
        $count = (optional(Post::selectRaw('COUNT(*) as count')->where('user_id', $user_id)->first())->count ?: 0) + 1;
        return "$user_id-$count";
    }
}
