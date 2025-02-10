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

    protected function getTableName($attendeeClass): string
    {
        if ($attendeeClass === Staff::class) {
            return 'staff';
        }
        return strtolower(class_basename($attendeeClass)).'s';
    }

    public function index(): View
    {
        try {
            \Log::info('Starting attendance index method');
            
            // Get events by type with proper error handling and chunking
            $eventsByType = [];
            $stats = [];

            foreach (['match', 'practice', 'meeting'] as $type) {
                try {
                    \Log::info("Processing events of type: {$type}");
                    
                    $events = Event::where('type', $type)
                        ->with([
                            'attendances' => function($query) {
                                $query->select('id', 'event_id', 'attendee_type', 'attendee_id', 'status');
                            }
                        ])
                ->orderBy('start_time', 'desc')
                        ->get();

                    $eventsByType[$type] = $events;

                    // Calculate stats for each type
                    $presentCount = Attendance::whereHas('event', function($q) use ($type) {
                        $q->where('type', $type);
                    })->where('status', 'present')->count() ?? 0;

                    $absentCount = Attendance::whereHas('event', function($q) use ($type) {
                        $q->where('type', $type);
                    })->where('status', 'absent')->count() ?? 0;

                    $stats[$type] = [
                        'total' => $events->count() ?? 0,
                        'present_rate' => $presentCount,
                        'absent_rate' => $absentCount,
                    ];

                    \Log::info("Successfully processed {$type} events", [
                        'count' => $events->count(),
                        'present' => $presentCount,
                        'absent' => $absentCount
                    ]);

                } catch (\Exception $e) {
                    \Log::error("Error processing {$type} events: " . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                    $eventsByType[$type] = collect();
                    $stats[$type] = ['total' => 0, 'present_rate' => 0, 'absent_rate' => 0];
                }
            }

            // Get total counts with error handling
            try {
                \Log::info('Getting total counts');
                $stats['total_players'] = Player::where('status', 'active')->count() ?? 0;
                $stats['total_staff'] = Staff::where('status', 'active')->count() ?? 0;
                $stats['total_members'] = Member::where('status', 'active')->count() ?? 0;
            } catch (\Exception $e) {
                \Log::error("Error getting total counts: " . $e->getMessage());
                \Log::error($e->getTraceAsString());
                $stats['total_players'] = 0;
                $stats['total_staff'] = 0;
                $stats['total_members'] = 0;
            }

        return view('attendances.index', compact('eventsByType', 'stats'));

        } catch (\Exception $e) {
            \Log::error('Error in AttendanceController@index: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Return a view with empty data and error message
            return view('attendances.index', [
                'eventsByType' => [
                    'match' => collect(),
                    'practice' => collect(),
                    'meeting' => collect(),
                ],
                'stats' => [
                    'match' => ['total' => 0, 'present_rate' => 0, 'absent_rate' => 0],
                    'practice' => ['total' => 0, 'present_rate' => 0, 'absent_rate' => 0],
                    'meeting' => ['total' => 0, 'present_rate' => 0, 'absent_rate' => 0],
                    'total_players' => 0,
                    'total_staff' => 0,
                    'total_members' => 0,
                ],
            ])->with('error', 'An error occurred while loading attendance data. Please try again.');
        }
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
            'attendances.*.attendee_id' => ['required', 'exists:'.$this->getTableName($attendeeClass).',id'],
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
            'attendances.*.attendee_id' => ['required', 'exists:'.$this->getTableName($attendeeClass).',id'],
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

    public function bulkUpdate(Request $request, Event $event)
    {
        $type = $request->input('type', 'players');
        $attendeeClass = $this->attendeeTypes[$type] ?? Player::class;

        $validated = $request->validate([
            'status' => ['required', 'in:present,absent,late,excused'],
            'attendee_ids' => ['required', 'array'],
            'attendee_ids.*' => ['exists:'.$this->getTableName($attendeeClass).',id'],
        ]);

        $event->attendances()
            ->where('attendee_type', $attendeeClass)
            ->whereIn('attendee_id', $validated['attendee_ids'])
            ->update(['status' => $validated['status']]);

        return back()->with('success', 'Attendance status updated successfully.');
    }

    public function report(Request $request, Event $event = null): View
    {
        try {
            if ($event) {
                // Individual event report
                $attendanceStats = [];
                foreach (['players', 'staff', 'members'] as $type) {
                    $attendeeClass = $this->attendeeTypes[$type];
                    
                    // Base query for this attendee type
                    $query = $event->attendances()
                        ->where('attendee_type', $attendeeClass);

                    // Apply status filter
                    if ($request->filled('status')) {
                        $query->where('status', $request->status);
                    }

                    // Get total and present counts
                    $totalAttendances = (clone $query)->count();
                    $presentCount = (clone $query)->where('status', 'present')->count();

                    // Get detailed attendances with filters
                    $detailsQuery = (clone $query)->with(['attendee' => function($q) use ($request) {
                        // Apply name search if provided
                        if ($request->filled('search')) {
                            $q->where(function($q) use ($request) {
                                $q->where('first_name', 'like', '%' . $request->search . '%')
                                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
                            });
                        }
                    }]);

                    // Filter out attendances where attendee name doesn't match search
                    $details = $detailsQuery->get()->filter(function($attendance) use ($request) {
                        if (!$request->filled('search')) {
                            return true;
                        }
                        $fullName = strtolower($attendance->attendee->first_name . ' ' . $attendance->attendee->last_name);
                        return str_contains($fullName, strtolower($request->search));
                    });

                    // Only include this type if it's not filtered out by type
                    if (!$request->filled('type') || $request->type === $type) {
                        $attendanceStats[$type] = [
                            'total' => $totalAttendances,
                            'present' => $presentCount,
                            'rate' => $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 2) : 0,
                            'details' => $details
                        ];
                    }
                }

                // If type filter is applied, only keep that type
                if ($request->filled('type')) {
                    $attendanceStats = array_intersect_key($attendanceStats, [$request->type => true]);
                }

                return view('attendances.event-report', compact('event', 'attendanceStats'));
            }

            // Overall attendance report (existing code)
            $thirtyDaysAgo = now()->subDays(30);
            
            $attendanceStats = [];
            foreach (['players', 'staff', 'members'] as $type) {
                $attendeeClass = $this->attendeeTypes[$type];
                
                $totalAttendances = Attendance::where('attendee_type', $attendeeClass)
                    ->whereHas('event', function($query) use ($thirtyDaysAgo) {
                        $query->where('start_time', '>=', $thirtyDaysAgo);
                    })
                    ->count();

                $presentCount = Attendance::where('attendee_type', $attendeeClass)
                    ->where('status', 'present')
                    ->whereHas('event', function($query) use ($thirtyDaysAgo) {
                        $query->where('start_time', '>=', $thirtyDaysAgo);
                    })
                    ->count();

                $attendanceStats[$type] = [
                    'total' => $totalAttendances,
                    'present' => $presentCount,
                    'rate' => $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 2) : 0
                ];
            }

            // Get event type summary
            $eventStats = [];
            foreach (['match', 'practice', 'meeting'] as $type) {
                $totalEvents = Event::where('type', $type)
                    ->where('start_time', '>=', $thirtyDaysAgo)
                    ->count();

                $totalAttendances = Attendance::whereHas('event', function($query) use ($type, $thirtyDaysAgo) {
                    $query->where('type', $type)
                        ->where('start_time', '>=', $thirtyDaysAgo);
                })->count();

                $presentCount = Attendance::whereHas('event', function($query) use ($type, $thirtyDaysAgo) {
                    $query->where('type', $type)
                        ->where('start_time', '>=', $thirtyDaysAgo);
                })->where('status', 'present')->count();

                $eventStats[$type] = [
                    'total_events' => $totalEvents,
                    'total_attendances' => $totalAttendances,
                    'present_count' => $presentCount,
                    'attendance_rate' => $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 2) : 0
                ];
            }

            // Get recent events with attendance
            $recentEvents = Event::with(['attendances' => function($query) {
                    $query->select('id', 'event_id', 'status');
                }])
                ->where('start_time', '>=', $thirtyDaysAgo)
                ->orderBy('start_time', 'desc')
                ->take(10)
                ->get();

            return view('attendances.report', compact('attendanceStats', 'eventStats', 'recentEvents'));

        } catch (\Exception $e) {
            \Log::error('Error generating attendance report: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while generating the report.');
        }
    }

    public function export(Request $request, Event $event, string $format)
    {
        try {
            // Get filtered attendance data
            $attendanceStats = [];
            foreach (['players', 'staff', 'members'] as $type) {
                $attendeeClass = $this->attendeeTypes[$type];
                
                // Base query for this attendee type
                $query = $event->attendances()
                    ->where('attendee_type', $attendeeClass);

                // Apply status filter
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }

                // Get total and present counts
                $totalAttendances = (clone $query)->count();
                $presentCount = (clone $query)->where('status', 'present')->count();

                // Get detailed attendances with filters
                $details = $query->with('attendee')->get();

                // Only include this type if it's not filtered out by type
                if (!$request->filled('type') || $request->type === $type) {
                    $attendanceStats[$type] = [
                        'total' => $totalAttendances,
                        'present' => $presentCount,
                        'rate' => $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 2) : 0,
                        'details' => $details
                    ];
                }
            }

            // If type filter is applied, only keep that type
            if ($request->filled('type')) {
                $attendanceStats = array_intersect_key($attendanceStats, [$request->type => true]);
            }

            $data = [
                'event' => $event,
                'attendanceStats' => $attendanceStats,
                'filters' => [
                    'type' => $request->type,
                    'status' => $request->status,
                    'search' => $request->search,
                ]
            ];

            if ($format === 'pdf') {
                $pdf = \PDF::loadView('attendances.exports.pdf', $data);
                return $pdf->download("attendance-report-{$event->id}.pdf");
            } elseif ($format === 'excel') {
                return \Excel::download(new AttendanceExport($data), "attendance-report-{$event->id}.xlsx");
            }

            return back()->with('error', 'Invalid export format specified.');

        } catch (\Exception $e) {
            \Log::error('Error exporting attendance report: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while generating the export.');
        }
    }
}
