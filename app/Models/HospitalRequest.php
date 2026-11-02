<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'action_at' => 'datetime',
    ];

    protected $appends = ['blood_type_full'];

    public function getBloodTypeFullAttribute(): string
    {
        return $this->blood_type . $this->rhesus;
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}

