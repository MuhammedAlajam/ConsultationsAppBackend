<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availabletime extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $guarded=[];
    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }
}
