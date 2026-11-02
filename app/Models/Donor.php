<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Donor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'last_donation_date' => 'date',
        'verified_at' => 'datetime',
        'weight' => 'decimal:2',
        'hemoglobin' => 'decimal:1',
    ];

    protected $appends = ['blood_type_full', 'age', 'days_since_last_donation', 'is_eligible_to_donate'];

    public function getBloodTypeFullAttribute(): string
    {
        return $this->blood_type . $this->rhesus;
    }

    public function getAgeAttribute(): int
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : 0;
    }

    public function getDaysSinceLastDonationAttribute(): ?int
    {
        if (!$this->last_donation_date) {
            return null;
        }
        return (int) Carbon::parse($this->last_donation_date)->diffInDays(now());
    }

    public function getIsEligibleToDonateAttribute(): bool
    {
        if ($this->verification_status === 'suspended') {
            return false;
        }
        if (!$this->last_donation_date) {
            return true;
        }
        return $this->days_since_last_donation >= 60;
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DonorHistory::class)->latest('donation_date');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function bloodBags(): HasMany
    {
        return $this->hasMany(BloodBag::class);
    }
}

