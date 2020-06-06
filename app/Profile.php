<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     title="Profile model",
 *     description="Profile model",
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="lastname", type="string"),
 *     @OA\Property(property="email", type="string"),
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
