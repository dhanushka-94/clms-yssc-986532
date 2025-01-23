<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(): View
    {
        $members = Member::orderBy('created_at', 'desc')->paginate(10);
        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nic' => 'required|string|max:255|unique:members',
            'phone' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'joined_date' => 'required|date',
            'membership_fee' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
        ]);

        $member = new Member($validated);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures/members', 'public');
            $member->profile_picture = $path;
        }

        $member->save();

        // Handle attachments after saving to get the member ID
        if ($request->hasFile('attachments')) {
            $attachmentPaths = $member->storeAttachments($request->file('attachments'));
            $member->attachments = $attachmentPaths;
            $member->save();
        }

        return redirect()->route('members.index')
            ->with('success', 'Member created successfully.');
    }

    public function show(Member $member): View
    {
        return view('members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nic' => 'required|string|max:255|unique:members,nic,' . $member->id,
            'phone' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'joined_date' => 'required|date',
            'membership_fee' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
            'delete_attachments.*' => 'nullable|string',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture
            if ($member->profile_picture) {
                Storage::disk('public')->delete($member->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile-pictures/members', 'public');
            $validated['profile_picture'] = $path;
        }

        // Handle attachment deletions
        if ($request->has('delete_attachments')) {
            $currentAttachments = $member->attachments ?? [];
            foreach ($request->delete_attachments as $path) {
                $member->deleteAttachment($path);
                $currentAttachments = array_diff($currentAttachments, [$path]);
            }
            $validated['attachments'] = array_values($currentAttachments);
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            $newAttachments = $member->storeAttachments($request->file('attachments'));
            $currentAttachments = $member->attachments ?? [];
            $validated['attachments'] = array_merge($currentAttachments, $newAttachments);
        }

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        // Delete profile picture
        if ($member->profile_picture) {
            Storage::disk('public')->delete($member->profile_picture);
        }

        // Delete all attachments
        $member->deleteAllAttachments();

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Member deleted successfully.');
    }
}
