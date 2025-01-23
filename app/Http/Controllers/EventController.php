<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Player;
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
        return view('events.create', compact('players'));
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
                'meeting_link' => 'nullable|required_if:type,meeting|url|max:255',
                'players' => 'nullable|array',
                'players.*' => 'exists:players,id',
            ]);

            $event = Event::create($validated);

            // Create attendance records for selected players
            if ($request->has('players')) {
                foreach ($request->players as $playerId) {
                    $event->attendances()->create([
                        'attendee_type' => Player::class,
                        'attendee_id' => $playerId,
                        'status' => 'absent' // Default status
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
        $players = Player::where('status', 'active')->get();
        $selectedPlayers = $event->players->pluck('id')->toArray();
        
        return view('events.edit', compact('event', 'players', 'selectedPlayers'));
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
                'meeting_link' => 'nullable|required_if:type,meeting|url|max:255',
                'players' => 'nullable|array',
                'players.*' => 'exists:players,id',
            ]);

            $event->update($validated);

            // Update player attendance records
            if ($request->has('players')) {
                $currentPlayers = $event->attendances()
                    ->where('attendee_type', Player::class)
                    ->pluck('attendee_id')
                    ->toArray();
                $newPlayers = $request->players;

                // Remove players not in the new list
                $playersToRemove = array_diff($currentPlayers, $newPlayers);
                if (!empty($playersToRemove)) {
                    $event->attendances()
                        ->where('attendee_type', Player::class)
                        ->whereIn('attendee_id', $playersToRemove)
                        ->delete();
                }

                // Add new players
                $playersToAdd = array_diff($newPlayers, $currentPlayers);
                foreach ($playersToAdd as $playerId) {
                    $event->attendances()->create([
                        'attendee_type' => Player::class,
                        'attendee_id' => $playerId,
                        'status' => 'absent' // Default status
                    ]);
                }
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
