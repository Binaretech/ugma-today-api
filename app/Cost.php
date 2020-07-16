<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cost extends Model
{

    public const CURRENCIES = [
        'Bs' => 0,
        'USD' => 1,
        0 => 'Bs',
        1 => 'USD',
    ];

    protected static function booted()
    {
        static::creating(function (Cost $cost) {
            $cost->modifier_user_id = Auth::user()->id;
        });
    }

    public const STORE_RULES = [
        'name' => 'required|string|min:1|max:128|unique:costs',
        'cost' => 'required|numeric|min:1|max:19',
        'currency' => 'required|numeric|min:0|max:1',
        'comment' => 'required|string|max:128',
    ];

    public function modified_by()
    {
        return $this->belongsTo(User::class, 'modifier_user_id');
    }
}
