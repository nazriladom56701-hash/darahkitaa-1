<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonorEvent extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'event_date' => 'date',
    ];

    protected $appends = ['registered_count', 'present_count', 'passed_count'];

    public function attendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class, 'event_id');
    }

    public function getRegisteredCountAttribute(): int
    {
        return $this->attendances()->count();
    }

    public function getPresentCountAttribute(): int
    {
        return $this->attendances()->where('attendance_status', 'present')->count();
    }

    public function getPassedCountAttribute(): int
    {
        return $this->attendances()->where('screening_status', 'passed')->count();
    }

    public function bloodBags(): HasMany
    {
        return $this->hasMany(BloodBag::class, 'event_id');
    }
}
