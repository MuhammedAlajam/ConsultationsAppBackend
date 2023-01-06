<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;


class User extends Model implements Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,AuthenticableTrait;

    public $timestamps = false;
    protected $guarded = [];

    public function expert()
    {
        return $this->hasOne(Expert::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function bookedtimes()
    {
        return $this->hasMany(Bookedtime::class);
    }
}
