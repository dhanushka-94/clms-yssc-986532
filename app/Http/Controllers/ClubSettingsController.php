<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ClubSettings;

class ClubSettingsController extends Controller
{
    public function index()
    {
        return view('settings.club');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            // Get club settings
            $clubSettings = ClubSettings::first();
            
            // Delete old logo if exists
            if ($clubSettings && $clubSettings->logo_path) {
                Storage::disk('public')->delete($clubSettings->logo_path);
            }

            // Store new logo
            $logoPath = 'logos/club-logo.png';
            Storage::disk('public')->put($logoPath, file_get_contents($request->file('logo')));
            
            // Update club settings with new logo path
            if (!$clubSettings) {
                $clubSettings = new ClubSettings();
            }
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
            Storage::disk('public')->delete($clubSettings->logo_path);
            $clubSettings->logo_path = null;
            $clubSettings->save();
            
            return redirect()->route('settings.club')
                ->with('success', 'Club logo has been removed successfully.');
        }

        return redirect()->route('settings.club')
            ->with('error', 'No logo found to delete.');
    }

    public function updateFeatures(Request $request)
    {
        $memberLogin = $request->has('member_login');
        $staffLogin = $request->has('staff_login');
        $playerLogin = $request->has('player_login');
        
        // Update the configuration file
        $configPath = config_path('club.php');
        $config = include $configPath;
        
        $config['features']['member_login'] = $memberLogin;
        $config['features']['staff_login'] = $staffLogin;
        $config['features']['player_login'] = $playerLogin;
        
        // Write the updated configuration back to the file
        file_put_contents($configPath, '<?php return ' . var_export($config, true) . ';');
        
        return back()->with('success', 'System access settings updated successfully.');
    }
} 