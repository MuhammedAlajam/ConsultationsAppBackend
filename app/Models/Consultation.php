<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Consultation extends Model
{
    use HasApiTokens,HasFactory;
    public $timestamps = false;
    protected $guarded = [];
    
    public function experts()
    {
        return $this->belongsToMany(Expert::class,'consultation_expert');
    }
}
