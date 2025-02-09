<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $query = Member::query();

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('membership_number', 'like', "%{$search}%")
                  ->orWhere('nic', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Sorting
        $sortField = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        // Map sort fields to actual database columns
        $sortFieldMap = [
            'id' => 'membership_number',
            'name' => 'first_name',
            'contact' => 'phone',
            'status' => 'status',
            'date' => 'joined_date',
            'created_at' => 'created_at'
        ];

        $sortField = $sortFieldMap[$sortField] ?? 'created_at';
        $query->orderBy($sortField, $direction);

        // Get paginated results
        $members = $query->paginate(10)->withQueryString();

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
            'nic' => 'nullable|string|max:12',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'joined_date' => 'nullable|date',
            'membership_type' => 'nullable|string|in:regular,lifetime,honorary,student',
            'designation' => 'nullable|string|max:255',
            'membership_fee' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:active,inactive,suspended',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Create member without files first
            $member = Member::create($validated);

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                try {
                    $path = $request->file('profile_picture')->store('profile-pictures/members', 'public');
                    if ($path) {
                        $member->profile_picture = $path;
                        $member->save();
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
                        $path = $file->store('attachments/members/' . $member->id, 'public');
                        if ($path) {
                            $attachmentPaths[] = $path;
                        }
                    }
                    if (!empty($attachmentPaths)) {
                        $member->attachments = $attachmentPaths;
                        $member->save();
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to upload attachments: ' . $e->getMessage());
                    // Continue without attachments
                }
            }

            DB::commit();
            return redirect()->route('members.index')
                ->with('success', 'Member created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Member creation failed: ' . $e->getMessage());
            
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
                ->with('error', 'Failed to create member. ' . $e->getMessage());
        }
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
            'nic' => 'nullable|string|max:12',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'joined_date' => 'nullable|date',
            'membership_type' => 'nullable|string|in:regular,lifetime,honorary,student',
            'designation' => 'nullable|string|max:255',
            'membership_fee' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:active,inactive,suspended',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|max:2048',
            'delete_attachments.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture
                if ($member->profile_picture) {
                    Storage::disk('public')->delete($member->profile_picture);
                }
                $path = $request->file('profile_picture')->store('profile-pictures/members', 'public');
                if (!$path) {
                    throw new \Exception('Failed to upload profile picture');
                }
                $validated['profile_picture'] = $path;
            }

            // Handle attachment deletions
            if ($request->has('delete_attachments')) {
                $currentAttachments = $member->attachments ?? [];
                foreach ($request->delete_attachments as $path) {
                    if ($member->deleteAttachment($path)) {
                        $currentAttachments = array_diff($currentAttachments, [$path]);
                    }
                }
                $validated['attachments'] = array_values($currentAttachments);
            }

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                $newAttachments = $member->storeAttachments($request->file('attachments'));
                if (!empty($newAttachments)) {
                    $currentAttachments = $member->attachments ?? [];
                    $validated['attachments'] = array_merge($currentAttachments, $newAttachments);
                }
            }

            $member->update($validated);

            DB::commit();

            return redirect()->route('members.index')
                ->with('success', 'Member updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Member update failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update member. Please try again.');
        }
    }

    public function destroy(Member $member)
    {
        try {
            DB::beginTransaction();

            // Delete profile picture
            if ($member->profile_picture) {
                Storage::disk('public')->delete($member->profile_picture);
            }

            // Delete all attachments
            $member->deleteAllAttachments();

            $member->delete();

            DB::commit();

            return redirect()->route('members.index')
                ->with('success', 'Member deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Member deletion failed: ' . $e->getMessage());
            return back()
                ->with('error', 'Failed to delete member. Please try again.');
        }
    }
}
