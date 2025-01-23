<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'nic' => 'required|string|max:255|unique:staff',
            'phone' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'joined_date' => 'required|date',
            'salary' => 'required|numeric',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after:contract_start_date',
            'status' => 'required|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
        ]);

        $staff = new Staff($validated);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures/staff', 'public');
            $staff->profile_picture = $path;
        }

        $staff->save();

        // Handle attachments after saving to get the staff ID
        if ($request->hasFile('attachments')) {
            $attachmentPaths = $staff->storeAttachments($request->file('attachments'));
            $staff->attachments = $attachmentPaths;
            $staff->save();
        }

        return redirect()->route('staff.index')
            ->with('success', 'Staff member created successfully.');
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
            'nic' => 'required|string|max:255|unique:staff,nic,' . $staff->id,
            'phone' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'joined_date' => 'required|date',
            'salary' => 'required|numeric',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after:contract_start_date',
            'status' => 'required|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
            'delete_attachments.*' => 'nullable|string',
        ]);

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
                $staff->deleteAttachment($path);
                $currentAttachments = array_diff($currentAttachments, [$path]);
            }
            $validated['attachments'] = array_values($currentAttachments);
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            $newAttachments = $staff->storeAttachments($request->file('attachments'));
            $currentAttachments = $staff->attachments ?? [];
            $validated['attachments'] = array_merge($currentAttachments, $newAttachments);
        }

        $staff->update($validated);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        // Delete profile picture
        if ($staff->profile_picture) {
            Storage::disk('public')->delete($staff->profile_picture);
        }

        // Delete all attachments
        $staff->deleteAllAttachments();

        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }
}
