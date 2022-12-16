<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'expert_id');
    }

    public function consultations()
    {
        return $this->belongsToMany(Consultation::class, 'consultation_expert', 'consultation_id', 'expert_id');
    }
}
