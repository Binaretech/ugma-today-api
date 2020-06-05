<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     title="Profile model",
 *     description="Profile model",
 * )
 */
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
