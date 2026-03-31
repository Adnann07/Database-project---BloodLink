<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodRequest extends Model
{
    protected $fillable = [
        'hospital_id',
        'request_id',
        'urgency',
        'blood_type',
        'volume_ml',
        'patient_details',
        'medical_notes',
        'required_date',
        'required_time',
        'status',
        'matched_donor_id',
        'matched_at',
        'fulfilled_at',
    ];

    protected $casts = [
        'required_date' => 'date',
        'required_time' => 'datetime',
        'matched_at' => 'datetime',
        'fulfilled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hospital_id');
    }

    public function matchedDonor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matched_donor_id');
    }

    public function generateRequestId(): string
    {
        return 'REQ-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
