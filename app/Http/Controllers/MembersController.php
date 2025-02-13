<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    public function show(Member $member)
    {
        $totalIncome = $member->financialTransactions()
            ->where('type', 'income')
            ->where('status', 'completed')
            ->sum('amount');

        $totalExpenses = $member->financialTransactions()
            ->where('type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');

        return view('members.show', compact('member', 'totalIncome', 'totalExpenses'));
    }
} 