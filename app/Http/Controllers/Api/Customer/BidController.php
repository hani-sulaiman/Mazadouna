<?php

namespace App\Http\Controllers\Api\Customer;

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
        $user = Auth::user();

        $auction = Auction::findOrFail($id);

        $currentDateTime = now();
        if ($currentDateTime < $auction->start_date || $currentDateTime > $auction->end_date) {
            return response()->json(['error' => 'This auction is not currently live.'], 403);
        }
        if ($auction->user_id === $user->id) {
            return response()->json(['error' => 'Auction owners cannot place bids.'], 403);
        }
        $auctionUser = AuctionUser::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->first();
    
        if (!$auctionUser || $auctionUser->status !== 'bidder') {
            return response()->json(['error' => 'You must join the auction as a bidder to place a bid.'], 403);
        }
    
        $lastBid = Bid::whereHas('auctionUser', function ($query) use ($auction) {
            $query->where('auction_id', $auction->id);
        })->orderBy('bid_date', 'desc')->first();
    
        if ($lastBid && $lastBid->auctionUser->user_id === $user->id) {
            return response()->json(['error' => 'You cannot place consecutive bids. Please wait for another user to bid.'], 403);
        }

        $minBid = $auction->current_price + $auction->min_increment;
    
        $request->validate([
            'bid_value' => "required|numeric|min:$minBid",
        ]);
    
        $bidValue = $request->input('bid_value');
    
        $bid = Bid::create([
            'auction_user_id' => $auctionUser->id,
            'bid_value' => $bidValue,
            'bid_date' => now(),
        ]);
    

        $auction->update(['current_price' => $bidValue]);
    
        return response()->json(['message' => 'Bid placed successfully', 'bid' => $bid], 200);
    }
    
}
