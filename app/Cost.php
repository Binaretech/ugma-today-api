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
            $cost->modified_by = Auth::user()->id;
        });
    }

    public function modified_by()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}
