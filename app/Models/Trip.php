<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'train_id',
        'origin',
        'destination',
        'departure_time',
        'arrival_time',
        'route_distance_km',
        'status',
        'delay_minutes',
        'expected_departure_time',
        'expected_arrival_time',
        'last_distance_km',
        'last_speed_kmh',
        'last_reading_at',
        'last_notified_delay_minutes',
        'available_seats',
    ];

    protected function casts(): array
    {
        return [
            'departure_time' => 'datetime',
            'arrival_time' => 'datetime',
            'expected_departure_time' => 'datetime',
            'expected_arrival_time' => 'datetime',
            'last_reading_at' => 'datetime',
            'route_distance_km' => 'decimal:2',
            'last_distance_km' => 'decimal:2',
            'last_speed_kmh' => 'decimal:2',
        ];
    }

    public function train(): BelongsTo
    {
        return $this->belongsTo(Train::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function confirmedBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->where('status', 'confirmed');
    }

    public function delayEvents(): HasMany
    {
        return $this->hasMany(DelayEvent::class);
    }

    public function isDelayed(): bool
    {
        return $this->status === 'delayed';
    }

    public function effectiveArrival()
    {
        return $this->expected_arrival_time ?? $this->arrival_time;
    }

    public function effectiveDeparture()
    {
        return $this->expected_departure_time ?? $this->departure_time;
    }
}
