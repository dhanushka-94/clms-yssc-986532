<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ClubSettings;

class ClubSettingsController extends Controller
{
    public function index()
    {
        $clubSettings = ClubSettings::first();
        return view('settings.club', compact('clubSettings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
        ]);

        $clubSettings = ClubSettings::first();
        if (!$clubSettings) {
            $clubSettings = new ClubSettings();
        }

        $clubSettings->fill($request->only([
            'name',
            'email',
            'phone',
            'address',
            'registration_number',
            'tax_number',
        ]));
        
        $clubSettings->save();

        return redirect()->route('settings.club')
            ->with('success', 'Club details have been updated successfully.');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            // Get or create club settings
            $clubSettings = ClubSettings::firstOrCreate(
                [],
                ['name' => config('app.name')] // Set a default name if creating new record
            );
            
            // Delete old logo if exists
            if ($clubSettings->logo_path) {
                if (file_exists(public_path('images/' . $clubSettings->logo_path))) {
                    unlink(public_path('images/' . $clubSettings->logo_path));
                }
            }

            // Store new logo
            $logoPath = 'club-logo.' . $request->file('logo')->extension();
            $request->file('logo')->move(public_path('images'), $logoPath);
            
            // Update club settings with new logo path
            $clubSettings->logo_path = $logoPath;
            $clubSettings->save();

            return redirect()->route('settings.club')
                ->with('success', 'Club logo has been updated successfully.');
        }

        return redirect()->route('settings.club')
            ->with('error', 'Failed to upload logo.');
    }

    public function deleteLogo()
    {
        $clubSettings = ClubSettings::first();
        
        if ($clubSettings && $clubSettings->logo_path) {
            if (file_exists(public_path('images/' . $clubSettings->logo_path))) {
                unlink(public_path('images/' . $clubSettings->logo_path));
            }
            $clubSettings->logo_path = null;
            $clubSettings->save();
            
            return redirect()->route('settings.club')
                ->with('success', 'Club logo has been removed successfully.');
        }

        return redirect()->route('settings.club')
            ->with('error', 'No logo found to delete.');
    }

    public function updateDefaultSignature(Request $request)
    {
        $request->validate([
            'default_signature' => 'nullable|file|mimes:png|max:2048',
            'default_signatory_name' => 'nullable|string|max:255',
            'default_signatory_designation' => 'nullable|string|max:255',
        ]);

        $clubSettings = ClubSettings::first();
        if (!$clubSettings) {
            $clubSettings = new ClubSettings();
        }

        // Handle signature file upload if present
        if ($request->hasFile('default_signature')) {
            // Delete old signature if exists
            if ($clubSettings->default_signature) {
                Storage::disk('public')->delete($clubSettings->default_signature);
            }

            // Store new signature
            $signaturePath = $request->file('default_signature')->store('signatures', 'public');
            $clubSettings->default_signature = $signaturePath;
        }

        // Handle signatory details if present
        if ($request->filled('default_signatory_name') || $request->filled('default_signatory_designation')) {
            $clubSettings->default_signatory_name = $request->default_signatory_name;
            $clubSettings->default_signatory_designation = $request->default_signatory_designation;
        }

        $clubSettings->save();

        return redirect()->route('settings.club')
            ->with('success', 'Default signature settings have been updated successfully.');
    }

    public function deleteDefaultSignature()
    {
        $clubSettings = ClubSettings::first();
        
        if ($clubSettings && $clubSettings->default_signature) {
            Storage::disk('public')->delete($clubSettings->default_signature);
            $clubSettings->default_signature = null;
            $clubSettings->default_signatory_name = null;
            $clubSettings->default_signatory_designation = null;
            $clubSettings->save();
            
            return redirect()->route('settings.club')
                ->with('success', 'Default signature has been removed successfully.');
        }

        return redirect()->route('settings.club')
            ->with('error', 'No default signature found to delete.');
    }
} 