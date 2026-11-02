<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodStock extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['blood_type_full', 'status_level'];

    public function getBloodTypeFullAttribute(): string
    {
        return $this->blood_type . $this->rhesus;
    }

    public function getStatusLevelAttribute(): string
    {
        if ($this->total_units <= ($this->min_threshold / 2)) {
            return 'critical'; // Merah
        }
        if ($this->total_units <= $this->min_threshold) {
            return 'warning'; // Kuning
        }
        return 'safe'; // Hijau
    }
}

