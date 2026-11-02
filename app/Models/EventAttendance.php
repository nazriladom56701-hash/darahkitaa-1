<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'attended_at' => 'datetime',
        'hemoglobin' => 'decimal:1',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(DonorEvent::class, 'event_id');
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}
