<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
    public function consultations()
    {
        return $this->belongsToMany(Consultation::class, 'consultation_expert', 'consultation_id', 'expert_id');
    }
}
