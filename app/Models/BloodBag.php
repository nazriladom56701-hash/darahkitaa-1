<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodBag extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'collection_date' => 'date',
        'expiry_date' => 'date',
    ];

    protected $appends = ['blood_type_full'];

    public function getBloodTypeFullAttribute(): string
    {
        return $this->blood_type . $this->rhesus;
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(DonorEvent::class, 'event_id');
    }
}

