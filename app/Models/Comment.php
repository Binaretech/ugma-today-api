<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id', 'post_id', 'comment'
	];

	public const STORE_RULES = [
		'comment' => 'required|string|min:1|max:500',
	];

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

	public function reply_to()
	{
		return $this->belongsTo(Comment::class, 'reply_to_id');
	}

	public function replies()
	{
		return $this->hasMany(Comment::class, 'reply_to_id');
	}

	public function getLikedByUserAttribute()
	{
		$user = Auth::guard('api')->user();

		if (!$user) return false;

		return $this->likes()->where('user_id', $user->id)->exists();
	}
}
