<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Models\Model;

class Cost extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'currency',
        'price',
        'comment'
    ];

    public const CURRENCIES = [
        'Bs' => 0,
        'USD' => 1,
        0 => 'Bs',
        1 => 'USD',
    ];

    protected static function booted()
    {
        static::saving(function (Cost $cost) {
            $cost->modifier_user_id = Auth::user()->id;
        });
    }

    public const ID_RULE = [
        'id' => 'required|exists:costs',
    ];

    public const STORE_RULES = [
        'name' => 'required|string|min:1|max:128|unique:costs',
        'price' => 'required|string|min:1|max:19|regex:/^\d+(.[0-9]{0,2})?$/',
        'currency' => 'required|numeric|min:0|max:1',
        'comment' => 'sometimes|string|max:128',
    ];

    public const UPDATE_RULES = [
        'name' => 'sometimes|string|min:1|max:128|unique:costs',
        'price' => 'sometimes|string|min:1|max:19|regex:/^\d+(.[0-9]{0,2})?$/',
        'currency' => 'sometimes|numeric|min:0|max:1',
        'comment' => 'sometimes|string|max:128',
    ];

    public function modified_by()
    {
        return $this->belongsTo(User::class, 'modifier_user_id');
    }
}
