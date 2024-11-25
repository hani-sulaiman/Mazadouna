<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getUserDashboardStats(Request $request)
    {
        $user = Auth::user();

        $auctionsSeen = Auction::where('user_id', $user->id)->sum('view_count'); // Assuming `view_count` column exists
        $auctionsWon = Winner::where('user_id', $user->id)->count();
        $auctionsCreated = $user->auctions()->count(); // Changed from `createdAuctions` to `auctions`
        $auctionsSold = $user->auctions()->whereHas('winner')->count(); // Changed from `createdAuctions` to `auctions`

        return response()->json([
            'auctions_seen' => $auctionsSeen,
            'auctions_won' => $auctionsWon,
            'auctions_created' => $auctionsCreated,
            'auctions_sold' => $auctionsSold,
        ]);
    }


    public function getUserOrders()
    {
        $user = Auth::user();

        // Fetch recent orders of the user's auctions that have been won
        $orders = $user->auctions()
            ->whereHas('winner') // Ensure the auction has a winner
            ->with(['winner', 'order']) // Load related winner and order details
            ->get()
            ->map(function ($auction) {
                $order = $auction->order;
                return [
                    'name' => $auction->name,
                    'payment_id' => $order->transaction_id ?? 'N/A',
                    'order_id' => $order->id ?? 'N/A',
                    'winner_name'=> $order->winner->name,
                    'winner_email'=> $order->winner->email,
                    'price' => $auction->ended_price?? 'N/A',
                    'payment_link' => $order->payment_link ?? 'N/A',
                    'payment_status' => $order->status ?? 'N/A',
                    'created_at' => $order->created_at->format('d/m/Y'),
                    'updated_at' => $order->updated_at->format('d/m/Y'),
                ];
            });

        return response()->json($orders);
    }
}
