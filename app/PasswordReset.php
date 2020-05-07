<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'token', 'expire_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
