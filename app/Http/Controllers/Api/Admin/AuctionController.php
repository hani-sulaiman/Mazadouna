<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
class AuctionController extends Controller
{
    public function pendingAuctions()
    {
        $auctions = Auction::where('status', 'pending')->get();
        return response()->json($auctions);
    }
    public function approveAuction($id)
    {
        $auction = Auction::find($id);

        if (!$auction) {
            return response()->json(['message' => 'Auction not found.'], 404);
        }

        if ($auction->status !== 'pending') {
            return response()->json(['message' => 'This auction has already been processed.'], 400);
        }

        $auction->status = 'approved';
        $auction->save();

        return response()->json(['message' => 'Auction approved successfully.']);
    }
    public function rejectAuction($id)
    {
        $auction = Auction::find($id);

        if (!$auction) {
            return response()->json(['message' => 'Auction not found.'], 404);
        }

        if ($auction->status !== 'pending') {
            return response()->json(['message' => 'This auction has already been processed.'], 400);
        }

        $auction->status = 'rejected';
        $auction->save();

        return response()->json(['message' => 'Auction rejected successfully.']);
    }
}
