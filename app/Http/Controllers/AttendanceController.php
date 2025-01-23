<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Player;
use App\Models\Staff;
use App\Models\Member;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    protected $attendeeTypes = [
        'players' => Player::class,
        'staff' => Staff::class,
        'members' => Member::class,
    ];

    public function index(): View
    {
        // Group events by type
        $eventsByType = [
            'match' => Event::where('type', 'match')->with(['attendances'])->orderBy('start_time', 'desc')->get(),
            'practice' => Event::where('type', 'practice')->with(['attendances'])->orderBy('start_time', 'desc')->get(),
            'meeting' => Event::where('type', 'meeting')->with(['attendances'])->orderBy('start_time', 'desc')->get(),
        ];

        // Calculate stats for each event type
        $stats = [
            'match' => [
                'total' => $eventsByType['match']->count(),
                'present_rate' => Attendance::whereHas('event', fn($q) => $q->where('type', 'match'))->where('status', 'present')->count(),
                'absent_rate' => Attendance::whereHas('event', fn($q) => $q->where('type', 'match'))->where('status', 'absent')->count(),
            ],
            'practice' => [
                'total' => $eventsByType['practice']->count(),
                'present_rate' => Attendance::whereHas('event', fn($q) => $q->where('type', 'practice'))->where('status', 'present')->count(),
                'absent_rate' => Attendance::whereHas('event', fn($q) => $q->where('type', 'practice'))->where('status', 'absent')->count(),
            ],
            'meeting' => [
                'total' => $eventsByType['meeting']->count(),
                'present_rate' => Attendance::whereHas('event', fn($q) => $q->where('type', 'meeting'))->where('status', 'present')->count(),
                'absent_rate' => Attendance::whereHas('event', fn($q) => $q->where('type', 'meeting'))->where('status', 'absent')->count(),
            ],
            'total_players' => Player::where('status', 'active')->count(),
            'total_staff' => Staff::where('status', 'active')->count(),
            'total_members' => Member::where('status', 'active')->count(),
        ];

        return view('attendances.index', compact('eventsByType', 'stats'));
    }

    public function create(Request $request, Event $event): View
    {
        $type = $request->query('type', 'players');
        $attendeeClass = $this->attendeeTypes[$type] ?? Player::class;
        
        $attendees = $attendeeClass::where('status', 'active')->get();
        $existingAttendances = $event->attendances()
            ->where('attendee_type', $attendeeClass)
            ->with('attendee')
            ->get();

        return view('attendances.create', compact('event', 'attendees', 'existingAttendances', 'type'));
    }

    public function store(Request $request, Event $event)
    {
        $type = $request->input('type', 'players');
        $attendeeClass = $this->attendeeTypes[$type] ?? Player::class;

        $validated = $request->validate([
            'attendances' => ['required', 'array'],
            'attendances.*.attendee_id' => ['required', 'exists:'.strtolower(class_basename($attendeeClass)).'s,id'],
            'attendances.*.status' => ['required', 'in:present,absent,late,excused'],
            'attendances.*.check_in_time' => ['nullable', 'date_format:H:i'],
            'attendances.*.check_out_time' => ['nullable', 'date_format:H:i', 'after:attendances.*.check_in_time'],
            'attendances.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated['attendances'] as $attendance) {
            $event->attendances()->updateOrCreate(
                [
                    'attendee_type' => $attendeeClass,
                    'attendee_id' => $attendance['attendee_id'],
                ],
                [
                    'status' => $attendance['status'],
                    'check_in_time' => $attendance['check_in_time'],
                    'check_out_time' => $attendance['check_out_time'],
                    'remarks' => $attendance['remarks'],
                ]
            );
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Attendance recorded successfully.');
    }

    public function edit(Request $request, Event $event): View
    {
        $type = $request->query('type', 'players');
        $attendeeClass = $this->attendeeTypes[$type] ?? Player::class;
        
        $attendees = $attendeeClass::where('status', 'active')->get();
        $existingAttendances = $event->attendances()
            ->where('attendee_type', $attendeeClass)
            ->with('attendee')
            ->get();

        return view('attendances.edit', compact('event', 'attendees', 'existingAttendances', 'type'));
    }

    public function update(Request $request, Event $event)
    {
        $type = $request->input('type', 'players');
        $attendeeClass = $this->attendeeTypes[$type] ?? Player::class;

        $validated = $request->validate([
            'attendances' => ['required', 'array'],
            'attendances.*.attendee_id' => ['required', 'exists:'.strtolower(class_basename($attendeeClass)).'s,id'],
            'attendances.*.status' => ['required', 'in:present,absent,late,excused'],
            'attendances.*.check_in_time' => ['nullable', 'date_format:H:i'],
            'attendances.*.check_out_time' => ['nullable', 'date_format:H:i', 'after:attendances.*.check_in_time'],
            'attendances.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated['attendances'] as $attendance) {
            $event->attendances()->updateOrCreate(
                [
                    'attendee_type' => $attendeeClass,
                    'attendee_id' => $attendance['attendee_id'],
                ],
                [
                    'status' => $attendance['status'],
                    'check_in_time' => $attendance['check_in_time'],
                    'check_out_time' => $attendance['check_out_time'],
                    'remarks' => $attendance['remarks'],
                ]
            );
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Attendance updated successfully.');
    }

    public function report(Request $request, Event $event): View
    {
        $type = $request->query('type', 'players');
        $attendeeClass = $this->attendeeTypes[$type] ?? Player::class;
        
        $attendances = $event->attendances()
            ->where('attendee_type', $attendeeClass)
            ->with('attendee')
            ->get();

        $stats = $event->getAttendanceStatsByType($type);

        return view('attendances.report', compact('event', 'attendances', 'stats', 'type'));
    }

    public function bulkUpdate(Request $request, Event $event)
    {
        $type = $request->input('type', 'players');
        $attendeeClass = $this->attendeeTypes[$type] ?? Player::class;

        $validated = $request->validate([
            'status' => ['required', 'in:present,absent,late,excused'],
            'attendee_ids' => ['required', 'array'],
            'attendee_ids.*' => ['exists:'.strtolower(class_basename($attendeeClass)).'s,id'],
        ]);

        $event->attendances()
            ->where('attendee_type', $attendeeClass)
            ->whereIn('attendee_id', $validated['attendee_ids'])
            ->update(['status' => $validated['status']]);

        return back()->with('success', 'Attendance status updated successfully.');
    }

    public function exportPdf(Event $event)
    {
        // Implement PDF export logic
    }

    public function exportExcel(Event $event)
    {
        // Implement Excel export logic
    }
}
