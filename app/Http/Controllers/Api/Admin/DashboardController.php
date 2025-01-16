<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Winner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStatistics()
    {
        $adminId = Auth::id(); 
        $dailyViews = Auction::whereDate('updated_at', Carbon::today())
            ->sum('view_count');
        $winningAuctions = Winner::count();
        $monthlyAuctions = Auction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $earning = Transaction::where('receiver_id', $adminId)
            ->sum('pay_amount');

        return response()->json([
            'daily_views' => $dailyViews,
            'winning_auctions' => $winningAuctions,
            'monthly_auctions' => $monthlyAuctions,
            'earning' => $earning,
        ]);
    }

    public function getRecentUsers()
    {
        $users = User::latest()->limit(5)->get(['id', 'name', 'email', 'created_at']);

        return response()->json(['users' => $users]);
    }
}
