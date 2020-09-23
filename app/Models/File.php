<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;

class File extends Model
{
    use HasFactory;

    public function fileable()
    {
        return $this->morphTo();
    }
}
