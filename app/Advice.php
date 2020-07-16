<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Advice extends Model
{

    protected static function booted()
    {
        static::creating(function (Advice $advice) {
            $advice->modifier_user_id = Auth::user()->id;
        });
    }

    public function modified_by()
    {
        return $this->belongsTo(User::class, 'modifier_user_id');
    }
}
