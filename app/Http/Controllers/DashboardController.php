<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Member;
use App\Models\Player;
use App\Models\Sponsor;
use App\Models\Staff;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'members_count' => Member::count(),
            'staff_count' => Staff::count(),
            'players_count' => Player::count(),
            'sponsors_count' => Sponsor::count(),
            'total_balance' => BankAccount::sum('current_balance'),
            'active_players' => Player::where('status', 'active')->count(),
            'active_sponsors' => Sponsor::where('status', 'active')->count(),
            'bank_accounts' => BankAccount::where('status', 'active')->get()
        ];

        return view('dashboard', compact('stats'));
    }
}
