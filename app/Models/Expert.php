<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Expert extends Model
{
    use HasApiTokens,HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consultations()
    {
        return $this->belongsToMany(Consultation::class);
    }

    public function availabletimes()
    {
        return $this->hasMany(Availabletime::class);
    }
}
