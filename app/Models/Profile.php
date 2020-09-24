<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;

class Profile extends Model
{
    use HasFactory;

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
