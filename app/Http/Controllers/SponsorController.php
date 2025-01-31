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
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'sponsorship_type' => 'nullable|string|max:255',
            'sponsorship_amount' => 'required|numeric|min:0',
            'sponsorship_start_date' => 'nullable|date',
            'sponsorship_end_date' => 'nullable|date|after_or_equal:sponsorship_start_date',
            'status' => 'nullable|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|max:2048',
            'notes' => 'nullable|string'
        ]);

        $sponsor = new Sponsor($validated);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures/sponsors', 'public');
            $sponsor->profile_picture = $path;
        }

        // Set default status if not provided
        if (!isset($validated['status'])) {
            $sponsor->status = 'active';
        }

        $sponsor->save();

        // Handle attachments after saving to get the sponsor ID
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $sponsor->storeAttachment($file);
            }
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
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'sponsorship_type' => 'nullable|string|max:255',
            'sponsorship_amount' => 'required|numeric',
            'sponsorship_start_date' => 'nullable|date',
            'sponsorship_end_date' => 'nullable|date|after_or_equal:sponsorship_start_date',
            'status' => 'nullable|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
            'delete_attachments.*' => 'nullable|string',
            'notes' => 'nullable|string'
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
            foreach ($request->delete_attachments as $path) {
                $sponsor->deleteAttachment($path);
            }
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $sponsor->storeAttachment($file);
            }
        }

        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = $sponsor->status ?? 'active';
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
