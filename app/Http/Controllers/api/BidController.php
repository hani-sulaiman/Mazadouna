<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auction;
use App\Models\AuctionUser;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    public function placeBid(Request $request, $id)
    {
        // Get the current authenticated user
        $user = Auth::user();
    
        // Retrieve the auction by ID
        $auction = Auction::findOrFail($id);
    
        // Check if the auction is "live" based on start and end dates
        $currentDateTime = now();
        if ($currentDateTime < $auction->start_date || $currentDateTime > $auction->end_date) {
            return response()->json(['error' => 'This auction is not currently live.'], 403);
        }
    
        // Ensure the user is not the auction owner
        if ($auction->user_id === $user->id) {
            return response()->json(['error' => 'Auction owners cannot place bids.'], 403);
        }
    
        // Check if the user has joined the auction as a bidder
        $auctionUser = AuctionUser::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->first();
    
        if (!$auctionUser || $auctionUser->status !== 'bidder') {
            return response()->json(['error' => 'You must join the auction as a bidder to place a bid.'], 403);
        }
    
        // Get the last bid made in the auction
        $lastBid = Bid::whereHas('auctionUser', function ($query) use ($auction) {
            $query->where('auction_id', $auction->id);
        })->orderBy('bid_date', 'desc')->first();
    
        // Prevent consecutive bids by the same user
        if ($lastBid && $lastBid->auctionUser->user_id === $user->id) {
            return response()->json(['error' => 'You cannot place consecutive bids. Please wait for another user to bid.'], 403);
        }
    
        // Calculate the minimum bid amount based on the current price and minimum increment
        $minBid = $auction->current_price + $auction->min_increment;
    
        // Validate the bid amount
        $request->validate([
            'bid_value' => "required|numeric|min:$minBid",
        ]);
    
        // Retrieve the bid value
        $bidValue = $request->input('bid_value');
    
        // Create the new bid entry
        $bid = Bid::create([
            'auction_user_id' => $auctionUser->id,
            'bid_value' => $bidValue,
            'bid_date' => now(),
        ]);
    
        // Update the auction’s current price to reflect the new bid
        $auction->update(['current_price' => $bidValue]);
    
        return response()->json(['message' => 'Bid placed successfully', 'bid' => $bid], 200);
    }
    
    
}
