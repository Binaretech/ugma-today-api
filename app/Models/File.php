<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;

class File extends Model
{
    use HasFactory;

	public const TYPES = [
		0 => 'PROFILE_IMAGE',
		'PROFILE_IMAGE' => 0,
	];

    public function fileable()
    {
        return $this->morphTo();
	}

	public function scopeProfileImage($query) {
		return $query->where('type', File::TYPES['PROFILE_IMAGE']);
	}
}
