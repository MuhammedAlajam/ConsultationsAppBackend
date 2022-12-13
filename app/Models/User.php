<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    public function expert()
    {
        return $this->hasOne(Expert::class, 'id');
    }

    /*
    public function favourites()
    {
        return $this->belongsToMany(User::class, 'user_user', 'user_id', 'user_id');
    }
    */
}
