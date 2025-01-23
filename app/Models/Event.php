<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasAttachments;

class Event extends Model
{
    use HasFactory, HasAttachments;

    protected $fillable = [
        'title',
        'description',
        'type',
        'start_time',
        'end_time',
        'location',
        'opponent',
        'venue',
        'meeting_link',
        'status',
        'attachments'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'attachments' => 'array'
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function players()
    {
        return $this->morphedByMany(Player::class, 'attendee', 'attendances')
            ->withPivot('status', 'check_in_time', 'check_out_time', 'remarks')
            ->withTimestamps();
    }

    public function staff()
    {
        return $this->morphedByMany(Staff::class, 'attendee', 'attendances')
            ->withPivot('status', 'check_in_time', 'check_out_time', 'remarks')
            ->withTimestamps();
    }

    public function members()
    {
        return $this->morphedByMany(Member::class, 'attendee', 'attendances')
            ->withPivot('status', 'check_in_time', 'check_out_time', 'remarks')
            ->withTimestamps();
    }

    public function getAttendeesByType(string $type)
    {
        return match ($type) {
            'players' => $this->players,
            'staff' => $this->staff,
            'members' => $this->members,
            default => collect(),
        };
    }

    public function getAttendanceStatsByType(string $type)
    {
        $attendees = $this->getAttendeesByType($type);
        
        return [
            'total' => $attendees->count(),
            'present' => $attendees->wherePivot('status', 'present')->count(),
            'absent' => $attendees->wherePivot('status', 'absent')->count(),
            'late' => $attendees->wherePivot('status', 'late')->count(),
            'excused' => $attendees->wherePivot('status', 'excused')->count(),
        ];
    }
}
