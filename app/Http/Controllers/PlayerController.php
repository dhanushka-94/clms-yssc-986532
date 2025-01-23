<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    public function index(): View
    {
        $players = Player::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('players.index', compact('players'));
    }

    public function create(): View
    {
        return view('players.create');
    }

    protected function getValidationRules($player = null): array
    {
        $rules = [
            'profile_picture' => 'nullable|image|max:1024',
            'ffsl_number' => ['nullable', 'string', 'max:50', Rule::unique('players')->ignore($player)],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nic' => ['required', 'string', 'max:12', Rule::unique('players')->ignore($player)],
            'phone' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'position' => 'required|string|max:50',
            'jersey_number' => ['required', 'integer', 'min:1', 'max:99', Rule::unique('players')->ignore($player)],
            'date_of_birth' => 'required|date',
            'joined_date' => 'required|date',
            'contract_amount' => 'required|numeric|min:0',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after:contract_start_date',
            'achievements' => 'nullable|string',
            'status' => 'required|in:active,injured,suspended,inactive',
        ];

        if (config('club.features.player_login')) {
            // For new player
            if (!$player) {
                $rules['email'] = 'required|string|email|max:255|unique:users';
                $rules['password'] = 'required|string|min:8|confirmed';
            } 
            // For existing player
            else {
                $rules['email'] = 'required|string|email|max:255|unique:users,email,' . optional($player->user)->id;
                $rules['password'] = 'nullable|string|min:8|confirmed';
            }
        }

        return $rules;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nic' => 'required|string|max:255|unique:players',
            'ffsl_number' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'jersey_number' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'joined_date' => 'required|date',
            'contract_amount' => 'required|numeric',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date',
            'status' => 'required|in:active,injured,suspended,inactive',
            'achievements' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
        ]);

        $player = new Player($validated);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures/players', 'public');
            $player->profile_picture = $path;
        }

        $player->save();

        // Handle attachments after saving to get the player ID
        if ($request->hasFile('attachments')) {
            $attachmentPaths = $player->storeAttachments($request->file('attachments'));
            $player->attachments = $attachmentPaths;
            $player->save();
        }

        return redirect()->route('players.index')
            ->with('success', 'Player created successfully.');
    }

    public function show(Player $player): View
    {
        $player->load(['user', 'financialTransactions' => function ($query) {
            $query->latest()->take(5);
        }]);

        return view('players.show', compact('player'));
    }

    public function edit(Player $player): View
    {
        return view('players.edit', compact('player'));
    }

    public function update(Request $request, Player $player)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nic' => 'required|string|max:255|unique:players,nic,' . $player->id,
            'ffsl_number' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'jersey_number' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'joined_date' => 'required|date',
            'contract_amount' => 'required|numeric',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date',
            'status' => 'required|in:active,injured,suspended,inactive',
            'achievements' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
            'delete_attachments.*' => 'nullable|string',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture
            if ($player->profile_picture) {
                Storage::disk('public')->delete($player->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile-pictures/players', 'public');
            $validated['profile_picture'] = $path;
        }

        // Handle attachment deletions
        if ($request->has('delete_attachments')) {
            $currentAttachments = $player->attachments ?? [];
            foreach ($request->delete_attachments as $path) {
                $player->deleteAttachment($path);
                $currentAttachments = array_diff($currentAttachments, [$path]);
            }
            $validated['attachments'] = array_values($currentAttachments);
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            $newAttachments = $player->storeAttachments($request->file('attachments'));
            $currentAttachments = $player->attachments ?? [];
            $validated['attachments'] = array_merge($currentAttachments, $newAttachments);
        }

        $player->update($validated);

        return redirect()->route('players.index')
            ->with('success', 'Player updated successfully.');
    }

    public function destroy(Player $player)
    {
        // Delete profile picture
        if ($player->profile_picture) {
            Storage::disk('public')->delete($player->profile_picture);
        }

        // Delete all attachments
        $player->deleteAllAttachments();

        $player->delete();

        return redirect()->route('players.index')
            ->with('success', 'Player deleted successfully.');
    }
}
