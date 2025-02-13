<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Player::query();

        // Handle search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('player_id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('nic', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('position', 'like', '%' . $searchTerm . '%')
                  ->orWhere('jersey_number', 'like', '%' . $searchTerm . '%')
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%' . $searchTerm . '%']);
            });
        }

        // Handle status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Handle sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Define allowed sort fields
        $allowedSorts = [
            'id' => 'player_id',
            'name' => 'first_name',
            'position' => 'position',
            'jersey' => 'jersey_number',
            'status' => 'status',
            'contract' => 'contract_amount',
            'created_at' => 'created_at'
        ];

        // Apply sort if it's allowed
        if (array_key_exists($sortField, $allowedSorts)) {
            $query->orderBy($allowedSorts[$sortField], $sortDirection);
        }

        // Always include a secondary sort by ID to ensure consistent ordering
        $query->orderBy('id', 'desc');

        // Get paginated results
        $players = $query->paginate(10)->withQueryString();

        return view('players.index', compact('players'));
    }

    public function create(): View
    {
        return view('players.create');
    }

    protected function getValidationRules($player = null): array
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nic' => 'nullable|string|max:12',
            'ffsl_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'jersey_number' => 'nullable|string|max:10',
            'date_of_birth' => 'nullable|date',
            'joined_date' => 'nullable|date',
            'contract_amount' => 'nullable|numeric|min:0',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'status' => 'nullable|string|in:active,injured,suspended,inactive',
            'achievements' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|max:2048'
        ];

        if (config('club.features.player_login')) {
            if (!$player) {
                $rules['email'] = 'nullable|string|email|max:255|unique:users';
                $rules['password'] = 'nullable|string|min:8|confirmed';
            } else {
                $rules['email'] = [
                    'nullable',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore(optional($player->user)->id)
                ];
                $rules['password'] = 'nullable|string|min:8|confirmed';
            }
        }

        return $rules;
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->getValidationRules());

        try {
            DB::beginTransaction();

            // Create player without files first
            $player = Player::create($validated);

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                try {
                    $path = $request->file('profile_picture')->store('profile-pictures/players', 'public');
                    if ($path) {
                        $player->profile_picture = $path;
                        $player->save();
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to upload profile picture: ' . $e->getMessage());
                    // Continue without profile picture
                }
            }

            // Handle attachments
            if ($request->hasFile('attachments')) {
                try {
                    $attachmentPaths = [];
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('attachments/players/' . $player->id, 'public');
                        if ($path) {
                            $attachmentPaths[] = $path;
                        }
                    }
                    if (!empty($attachmentPaths)) {
                        $player->attachments = $attachmentPaths;
                        $player->save();
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to upload attachments: ' . $e->getMessage());
                    // Continue without attachments
                }
            }

            // Create user account if player login is enabled and email is provided
            if (config('club.features.player_login') && $request->filled('email')) {
                try {
                    $user = User::create([
                        'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                        'email' => $validated['email'],
                        'password' => Hash::make($validated['password'] ?? str_random(12)),
                    ]);

                    $player->user()->associate($user);
                    $player->save();

                    // Assign player role
                    $playerRole = Role::where('name', 'player')->first();
                    if ($playerRole) {
                        $user->roles()->attach($playerRole);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to create user account: ' . $e->getMessage());
                    // Continue without user account
                }
            }

            DB::commit();
            return redirect()->route('players.index')
                ->with('success', 'Player created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Player creation failed: ' . $e->getMessage());
            
            // Clean up any uploaded files
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            if (isset($attachmentPaths)) {
                foreach ($attachmentPaths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create player. ' . $e->getMessage());
        }
    }

    public function show(Player $player): View
    {
        // Load the player's financial transactions
        $player->load(['user', 'financialTransactions' => function ($query) {
            $query->where('status', 'completed')
                  ->orderBy('transaction_date', 'desc')
                  ->orderBy('created_at', 'desc');
        }]);

        // Calculate totals using query builder for better performance
        $totals = DB::table('financial_transactions')
            ->where(function($query) {
                $query->where('transactionable_type', 'player')
                      ->orWhere('transactionable_type', 'App\\Models\\Player');
            })
            ->where('transactionable_id', $player->id)
            ->where('status', 'completed')
            ->selectRaw('
                COALESCE(SUM(CASE WHEN type = "income" THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END), 0) as total_expenses
            ')
            ->first();

        $totalIncome = $totals->total_income ?? 0;
        $totalExpenses = $totals->total_expenses ?? 0;

        return view('players.show', compact('player', 'totalIncome', 'totalExpenses'));
    }

    public function edit(Player $player): View
    {
        return view('players.edit', compact('player'));
    }

    public function update(Request $request, Player $player)
    {
        $validated = $request->validate($this->getValidationRules($player));

        try {
            DB::beginTransaction();

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
                    Storage::disk('public')->delete($path);
                    $currentAttachments = array_diff($currentAttachments, [$path]);
                }
                $validated['attachments'] = array_values($currentAttachments);
            }

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                $newAttachments = [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments/players/' . $player->id, 'public');
                    if ($path) {
                        $newAttachments[] = $path;
                    }
                }
                $currentAttachments = $player->attachments ?? [];
                $validated['attachments'] = array_merge($currentAttachments, $newAttachments);
            }

            // Update user account if player login is enabled and email is provided
            if (config('club.features.player_login') && $request->filled('email')) {
                $user = $player->user;
                if ($user) {
                    $user->name = $validated['first_name'] . ' ' . $validated['last_name'];
                    $user->email = $validated['email'];
                    if (!empty($validated['password'])) {
                        $user->password = Hash::make($validated['password']);
                    }
                    $user->save();
                }
            }

            $player->update($validated);

            DB::commit();
            return redirect()->route('players.index')
                ->with('success', 'Player updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Player update failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to update player. ' . $e->getMessage());
        }
    }

    public function destroy(Player $player)
    {
        try {
            DB::beginTransaction();

            // Delete profile picture
            if ($player->profile_picture) {
                Storage::disk('public')->delete($player->profile_picture);
            }

            // Delete all attachments
            if ($player->attachments) {
                foreach ($player->attachments as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            $player->delete();

            DB::commit();
            return redirect()->route('players.index')
                ->with('success', 'Player deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Player deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete player. ' . $e->getMessage());
        }
    }
}
