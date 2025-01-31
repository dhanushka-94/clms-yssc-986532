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
        $attendeeClass = match ($type) {
            'players' => Player::class,
            'staff' => Staff::class,
            'members' => Member::class,
            default => null,
        };

        if (!$attendeeClass) {
            return [
                'total' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
            ];
        }

        $stats = $this->attendances()
            ->where('attendee_type', $attendeeClass)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) as excused
            ')
            ->first()
            ->toArray();
        
        return [
            'total' => (int) $stats['total'],
            'present' => (int) $stats['present'],
            'absent' => (int) $stats['absent'],
            'late' => (int) $stats['late'],
            'excused' => (int) $stats['excused'],
        ];
    }
}
