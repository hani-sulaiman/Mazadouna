<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auction;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Winner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class AdminDashboardController extends Controller
{
    public function getStatistics()
    {
        $adminId = Auth::id(); // Assumes admin is authenticated

        // Daily Views: Sum of `view_count` across all auctions today
        $dailyViews = Auction::whereDate('updated_at', Carbon::today())
            ->sum('view_count');

        // Winning Auctions: Count of winners table rows
        $winningAuctions = Winner::count();

        // Monthly Auctions: Count of auctions created in the current month
        $monthlyAuctions = Auction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Earnings: Sum of transactions' `pay_amount` for admin
        $earning = Transaction::where('receiver_id', $adminId)
            ->sum('pay_amount');

        return response()->json([
            'daily_views' => $dailyViews,
            'winning_auctions' => $winningAuctions,
            'monthly_auctions' => $monthlyAuctions,
            'earning' => $earning,
        ]);
    }
    public function getRecentOrders()
    {
        // Fetch recent orders with transaction details and user information
        $orders = Order::with(['transaction', 'winner'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'user_name' => $order->winner ? $order->winner->name : 'Unknown',
                    'price' => $order->transaction ? $order->transaction->pay_amount : 'N/A',
                    'payment_status' => $order->transaction ? $order->transaction->transaction_status : 'N/A',
                    'status' => $order->status,
                ];
            });

        return response()->json(['orders' => $orders]);
    }

    public function getRecentUsers()
    {
        // Fetch the latest 5 registered users
        $users = User::latest()->limit(5)->get(['id', 'name', 'email', 'created_at']);

        return response()->json(['users' => $users]);
    }

}
