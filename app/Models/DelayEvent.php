<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DelayEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'distance_km',
        'speed_kmh',
        'computed_eta',
        'delay_minutes',
        'notified',
        'source',
        'payload',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'computed_eta' => 'datetime',
            'reported_at' => 'datetime',
            'notified' => 'boolean',
            'payload' => 'array',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
