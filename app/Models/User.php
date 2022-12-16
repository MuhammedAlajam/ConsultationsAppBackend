<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;
    protected $guarded = [];

    public function expert()
    {
        return $this->hasOne(Expert::class, 'id', 'expert_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites',  'user_id', 'fav_id');
    }
}
