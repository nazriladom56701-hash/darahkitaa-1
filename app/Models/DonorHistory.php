<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorHistory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'donation_date' => 'date',
        'hemoglobin' => 'decimal:1',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(DonorEvent::class, 'event_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}

