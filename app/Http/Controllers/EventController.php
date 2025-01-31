<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Player;
use App\Models\Staff;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::with(['players'])
            ->latest('start_time')
            ->paginate(10);

        $upcomingEvents = Event::where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->count();

        $completedEvents = Event::where('status', 'completed')->count();

        return view('events.index', compact('events', 'upcomingEvents', 'completedEvents'));
    }

    public function create(): View
    {
        $players = Player::where('status', 'active')->get();
        $staff = \App\Models\Staff::where('status', 'active')->get();
        $members = \App\Models\Member::where('status', 'active')->get();
        
        // Add debugging
        \Log::info('Create Event Data:', [
            'players_count' => $players->count(),
            'staff_count' => $staff->count(),
            'members_count' => $members->count()
        ]);
        
        return view('events.create', compact('players', 'staff', 'members'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:match,practice,meeting',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'location' => 'nullable|string|max:255',
                'opponent' => 'nullable|required_if:type,match|string|max:255',
                'venue' => 'nullable|required_if:type,match|in:home,away,neutral',
                'meeting_link' => 'nullable|url|max:255',
                'players' => 'nullable|array',
                'players.*' => 'exists:players,id',
                'staff' => 'nullable|array',
                'staff.*' => 'exists:staff,id',
                'members' => 'nullable|array',
                'members.*' => 'exists:members,id',
                'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            ]);

            $event = Event::create($validated);

            // Create attendance records for selected players
            if ($request->has('players')) {
                foreach ($request->players as $playerId) {
                    $event->attendances()->create([
                        'attendee_type' => Player::class,
                        'attendee_id' => $playerId,
                        'status' => 'absent'
                    ]);
                }
            }

            // Create attendance records for selected staff
            if ($request->has('staff')) {
                foreach ($request->staff as $staffId) {
                    $event->attendances()->create([
                        'attendee_type' => \App\Models\Staff::class,
                        'attendee_id' => $staffId,
                        'status' => 'absent'
                    ]);
                }
            }

            // Create attendance records for selected members
            if ($request->has('members')) {
                foreach ($request->members as $memberId) {
                    $event->attendances()->create([
                        'attendee_type' => \App\Models\Member::class,
                        'attendee_id' => $memberId,
                        'status' => 'absent'
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('events.show', $event)
                ->with('success', 'Event created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create event. ' . $e->getMessage());
        }
    }

    public function show(Event $event): View
    {
        $event->load(['attendances.attendee']);
        
        $attendanceStats = [
            'players' => $event->getAttendanceStatsByType('players'),
            'staff' => $event->getAttendanceStatsByType('staff'),
            'members' => $event->getAttendanceStatsByType('members'),
        ];

        return view('events.show', compact('event', 'attendanceStats'));
    }

    public function edit(Event $event): View
    {
        // Eager load relationships
        $event->load(['players', 'staff', 'members']);
        
        // Get active attendees
        $players = Player::where('status', 'active')->get();
        $staff = Staff::where('status', 'active')->get();
        $members = Member::where('status', 'active')->get();
        
        // Get selected IDs
        $selectedPlayers = $event->players->pluck('id')->toArray();
        $selectedStaff = $event->staff->pluck('id')->toArray();
        $selectedMembers = $event->members->pluck('id')->toArray();
        
        // Add debugging
        \Log::info('Edit Event Data:', [
            'event_id' => $event->id,
            'players_count' => $players->count(),
            'staff_count' => $staff->count(),
            'members_count' => $members->count(),
            'selected_players' => $selectedPlayers,
            'selected_staff' => $selectedStaff,
            'selected_members' => $selectedMembers
        ]);
        
        return view('events.edit', compact('event', 'players', 'staff', 'members', 
            'selectedPlayers', 'selectedStaff', 'selectedMembers'));
    }

    public function update(Request $request, Event $event)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:match,practice,meeting',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'location' => 'nullable|string|max:255',
                'opponent' => 'nullable|required_if:type,match|string|max:255',
                'venue' => 'nullable|required_if:type,match|in:home,away,neutral',
                'meeting_link' => 'nullable|url|max:255',
                'players' => 'nullable|array',
                'players.*' => 'exists:players,id',
                'staff' => 'nullable|array',
                'staff.*' => 'exists:staff,id',
                'members' => 'nullable|array',
                'members.*' => 'exists:members,id',
                'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            ]);

            $event->update($validated);

            // Handle Players
            $currentPlayers = $event->attendances()
                ->where('attendee_type', Player::class)
                ->pluck('attendee_id')
                ->toArray();
            
            $newPlayers = $request->input('players', []);
            
            // Remove players not in new list
            $event->attendances()
                ->where('attendee_type', Player::class)
                ->whereIn('attendee_id', array_diff($currentPlayers, $newPlayers))
                ->delete();
            
            // Add new players
            foreach (array_diff($newPlayers, $currentPlayers) as $playerId) {
                $event->attendances()->create([
                    'attendee_type' => Player::class,
                    'attendee_id' => $playerId,
                    'status' => 'absent'
                ]);
            }

            // Handle Staff
            $currentStaff = $event->attendances()
                ->where('attendee_type', Staff::class)
                ->pluck('attendee_id')
                ->toArray();
            
            $newStaff = $request->input('staff', []);
            
            // Remove staff not in new list
            $event->attendances()
                ->where('attendee_type', Staff::class)
                ->whereIn('attendee_id', array_diff($currentStaff, $newStaff))
                ->delete();
            
            // Add new staff
            foreach (array_diff($newStaff, $currentStaff) as $staffId) {
                $event->attendances()->create([
                    'attendee_type' => Staff::class,
                    'attendee_id' => $staffId,
                    'status' => 'absent'
                ]);
            }

            // Handle Members
            $currentMembers = $event->attendances()
                ->where('attendee_type', Member::class)
                ->pluck('attendee_id')
                ->toArray();
            
            $newMembers = $request->input('members', []);
            
            // Remove members not in new list
            $event->attendances()
                ->where('attendee_type', Member::class)
                ->whereIn('attendee_id', array_diff($currentMembers, $newMembers))
                ->delete();
            
            // Add new members
            foreach (array_diff($newMembers, $currentMembers) as $memberId) {
                $event->attendances()->create([
                    'attendee_type' => Member::class,
                    'attendee_id' => $memberId,
                    'status' => 'absent'
                ]);
            }

            DB::commit();

            return redirect()->route('events.show', $event)
                ->with('success', 'Event updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update event. ' . $e->getMessage());
        }
    }

    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return redirect()->route('events.index')
                ->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete event. ' . $e->getMessage());
        }
    }
}
