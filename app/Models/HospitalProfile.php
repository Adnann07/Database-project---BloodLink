<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalProfile extends Model
{
    protected $fillable = [
        'user_id', 
        'hospital_name', 
        'license_number', 
        'emergency_contact'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}