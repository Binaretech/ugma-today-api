<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'name', 'lastname', 'email'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
