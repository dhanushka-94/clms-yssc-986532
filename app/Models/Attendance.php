<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'attendee_type',
        'attendee_id',
        'status',
        'check_in_time',
        'check_out_time',
        'remarks'
    ];

    protected $casts = [
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i'
    ];

    protected $dates = [
        'check_in_time',
        'check_out_time'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function attendee(): MorphTo
    {
        return $this->morphTo();
    }

    // Helper method to safely get formatted time
    public function getFormattedTime($field)
    {
        try {
            return $this->{$field} ? $this->{$field}->format('H:i') : null;
        } catch (\Exception $e) {
            \Log::error("Error formatting {$field}: " . $e->getMessage());
            return null;
        }
    }
}
