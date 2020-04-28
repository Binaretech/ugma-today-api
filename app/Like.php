<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likeable()
    {
        return $this->morphTo();
    }
}
