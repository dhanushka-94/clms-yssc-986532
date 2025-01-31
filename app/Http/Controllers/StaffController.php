<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(): View
    {
        $staff = Staff::orderBy('created_at', 'desc')->paginate(10);
        return view('staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nic' => 'nullable|string|max:12',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'joined_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'status' => 'nullable|string|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Create staff without files first
            $staff = Staff::create($validated);

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                try {
                    $path = $request->file('profile_picture')->store('profile-pictures/staff', 'public');
                    if ($path) {
                        $staff->profile_picture = $path;
                        $staff->save();
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
                        $path = $file->store('attachments/staff/' . $staff->id, 'public');
                        if ($path) {
                            $attachmentPaths[] = $path;
                        }
                    }
                    if (!empty($attachmentPaths)) {
                        $staff->attachments = $attachmentPaths;
                        $staff->save();
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to upload attachments: ' . $e->getMessage());
                    // Continue without attachments
                }
            }

            DB::commit();
            return redirect()->route('staff.index')
                ->with('success', 'Staff member created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Staff creation failed: ' . $e->getMessage());
            
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
                ->with('error', 'Failed to create staff member. ' . $e->getMessage());
        }
    }

    public function show(Staff $staff): View
    {
        return view('staff.show', compact('staff'));
    }

    public function edit(Staff $staff): View
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nic' => 'nullable|string|max:12',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'joined_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'status' => 'nullable|string|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|max:2048',
            'delete_attachments.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture
                if ($staff->profile_picture) {
                    Storage::disk('public')->delete($staff->profile_picture);
                }
                $path = $request->file('profile_picture')->store('profile-pictures/staff', 'public');
                $validated['profile_picture'] = $path;
            }

            // Handle attachment deletions
            if ($request->has('delete_attachments')) {
                $currentAttachments = $staff->attachments ?? [];
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
                    $path = $file->store('attachments/staff/' . $staff->id, 'public');
                    if ($path) {
                        $newAttachments[] = $path;
                    }
                }
                $currentAttachments = $staff->attachments ?? [];
                $validated['attachments'] = array_merge($currentAttachments, $newAttachments);
            }

            $staff->update($validated);

            DB::commit();
            return redirect()->route('staff.index')
                ->with('success', 'Staff member updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Staff update failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to update staff member. ' . $e->getMessage());
        }
    }

    public function destroy(Staff $staff)
    {
        try {
            DB::beginTransaction();

            // Delete profile picture
            if ($staff->profile_picture) {
                Storage::disk('public')->delete($staff->profile_picture);
            }

            // Delete all attachments
            if ($staff->attachments) {
                foreach ($staff->attachments as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            $staff->delete();

            DB::commit();
            return redirect()->route('staff.index')
                ->with('success', 'Staff member deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Staff deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete staff member. ' . $e->getMessage());
        }
    }
}
