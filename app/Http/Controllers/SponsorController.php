<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function index(): View
    {
        $sponsors = Sponsor::orderBy('created_at', 'desc')->paginate(10);
        return view('sponsors.index', compact('sponsors'));
    }

    public function create(): View
    {
        return view('sponsors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'sponsorship_type' => 'required|string|max:255',
            'sponsorship_amount' => 'required|numeric',
            'sponsorship_start_date' => 'required|date',
            'sponsorship_end_date' => 'required|date|after:sponsorship_start_date',
            'status' => 'required|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
        ]);

        $sponsor = new Sponsor($validated);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures/sponsors', 'public');
            $sponsor->profile_picture = $path;
        }

        $sponsor->save();

        // Handle attachments after saving to get the sponsor ID
        if ($request->hasFile('attachments')) {
            $attachmentPaths = $sponsor->storeAttachments($request->file('attachments'));
            $sponsor->attachments = $attachmentPaths;
            $sponsor->save();
        }

        return redirect()->route('sponsors.index')
            ->with('success', 'Sponsor created successfully.');
    }

    public function show(Sponsor $sponsor): View
    {
        return view('sponsors.show', compact('sponsor'));
    }

    public function edit(Sponsor $sponsor): View
    {
        return view('sponsors.edit', compact('sponsor'));
    }

    public function update(Request $request, Sponsor $sponsor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'sponsorship_type' => 'required|string|max:255',
            'sponsorship_amount' => 'required|numeric',
            'sponsorship_start_date' => 'required|date',
            'sponsorship_end_date' => 'required|date|after:sponsorship_start_date',
            'status' => 'required|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
            'delete_attachments.*' => 'nullable|string',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture
            if ($sponsor->profile_picture) {
                Storage::disk('public')->delete($sponsor->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile-pictures/sponsors', 'public');
            $validated['profile_picture'] = $path;
        }

        // Handle attachment deletions
        if ($request->has('delete_attachments')) {
            $currentAttachments = $sponsor->attachments ?? [];
            foreach ($request->delete_attachments as $path) {
                $sponsor->deleteAttachment($path);
                $currentAttachments = array_diff($currentAttachments, [$path]);
            }
            $validated['attachments'] = array_values($currentAttachments);
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            $newAttachments = $sponsor->storeAttachments($request->file('attachments'));
            $currentAttachments = $sponsor->attachments ?? [];
            $validated['attachments'] = array_merge($currentAttachments, $newAttachments);
        }

        $sponsor->update($validated);

        return redirect()->route('sponsors.index')
            ->with('success', 'Sponsor updated successfully.');
    }

    public function destroy(Sponsor $sponsor)
    {
        // Delete profile picture
        if ($sponsor->profile_picture) {
            Storage::disk('public')->delete($sponsor->profile_picture);
        }

        // Delete all attachments
        $sponsor->deleteAllAttachments();

        $sponsor->delete();

        return redirect()->route('sponsors.index')
            ->with('success', 'Sponsor deleted successfully.');
    }
}
