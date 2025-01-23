<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'profile_picture' => 'nullable|image|max:1024', // max 1MB
        ]);

        try {
            DB::beginTransaction();

            // Handle profile picture upload
            $profilePicturePath = null;
            if ($request->hasFile('profile_picture')) {
                $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', 'public');
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'profile_picture' => $profilePicturePath,
            ]);

            // Attach roles
            $user->roles()->attach($request->roles);

            DB::commit();

            return redirect()->route('users.show', $user)
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create user. ' . $e->getMessage());
        }
    }

    public function show(User $user): View
    {
        $user->load('roles');
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'profile_picture' => 'nullable|image|max:1024', // max 1MB
        ]);

        try {
            DB::beginTransaction();

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', 'public');
                $user->profile_picture = $profilePicturePath;
            }

            // Update user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            // Sync roles
            $user->roles()->sync($request->roles);

            DB::commit();

            return redirect()->route('users.show', $user)
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update user. ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            // Delete profile picture
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Delete user
            $user->delete();

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete user. ' . $e->getMessage());
        }
    }
} 