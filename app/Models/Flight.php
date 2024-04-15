<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flight extends Model
{
    protected $fillable = [
        'flight_number',
        'departure_location',
        'departure_time',
        'arrival_location',
        'arrival_time',
        'block_hours',
        'flight_duration',
        'night_duration',
        'activity_duration',
        'extension_duration',
        'passengers_on_flight',
        'aircraft_registration_number'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
