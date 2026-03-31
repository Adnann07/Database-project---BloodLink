<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodInventory extends Model
{
    protected $fillable = [
        'hospital_id',
        'blood_type',
        'volume_ml',
        'units_available',
        'last_updated',
        'storage_condition',
        'expiry_date',
    ];

    protected $casts = [
        'last_updated' => 'date',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hospital_id');
    }

    public function updateInventory(int $volumeChange, string $operation = 'add'): void
    {
        if ($operation === 'add') {
            $this->volume_ml += $volumeChange;
            $this->units_available += ceil($volumeChange / 450); // Standard unit ~450ml
        } elseif ($operation === 'subtract') {
            $this->volume_ml = max(0, $this->volume_ml - $volumeChange);
            $this->units_available = max(0, $this->units_available - ceil($volumeChange / 450));
        }
        
        $this->last_updated = now();
        $this->save();
    }
}
