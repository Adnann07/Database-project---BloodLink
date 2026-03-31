<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use DateTimeInterface;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        "id",
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'email_verified_at',
        'email_verification_token',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    /**
     * Get the attributes that should be converted to camel case.
     *
     * @return array
     */
    public function toArray()
    {
        $data = parent::toArray();
        
        // Convert relationship keys to camelCase
        if (isset($data['donor_profile'])) {
            $data['donorProfile'] = $data['donor_profile'];
            unset($data['donor_profile']);
        }
        
        if (isset($data['hospital_profile'])) {
            $data['hospitalProfile'] = $data['hospital_profile'];
            unset($data['hospital_profile']);
        }
        
        return $data;
    }

    public function donorProfile()
    {
        return $this->hasOne(DonorProfile::class);
    }

    public function hospitalProfile()
    {
        return $this->hasOne(HospitalProfile::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'donor_id');
    }

    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class, 'hospital_id');
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class, 'donor_id');
    }

    public function volunteer()
    {
        return $this->hasOne(Volunteer::class);
    }
}