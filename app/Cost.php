<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    public const CURRENCIES = [
        'Bs' => 0,
        'USD' => 1,
        0 => 'Bs',
        1 => 'USD',
    ];

    public function modified_by()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}
