<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonorProfile extends Model
{
    protected $fillable = [
        'user_id', 
        'blood_group', 
        'date_of_birth', 
        'gender',
        'weight_kg'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}