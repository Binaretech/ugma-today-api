<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;
use Illuminate\Support\Facades\Auth;

class Advice extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saving(function (Advice $advice) {
            $advice->modifier_user_id = Auth::user()->id;
        });
    }

    public function modified_by()
    {
        return $this->belongsTo(User::class, 'modifier_user_id');
    }
}
