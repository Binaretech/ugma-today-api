<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;

class PasswordReset extends Model
{
    use HasFactory;

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
