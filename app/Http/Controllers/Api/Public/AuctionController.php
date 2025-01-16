<?php
namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionUser;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuctionController extends Controller
{
    public function index()
    {
        $auctions = Cache::remember('approved_auctions', 60, function() {
            return Auction::where('status', 'approved')->get();
        });

        return response()->json($auctions);
    }

    public function show($id)
    {
        $auction = Cache::remember("auction_$id", 60, function() use ($id) {
            return Auction::find($id);
        });

        if (! $auction) {
            return response()->json(['message' => 'Auction not found.'], 404);
        }

        if (! in_array($auction->status, ['completed', 'approved'])) {
            return response()->json(['message' => 'This auction is not available for viewing.'], 403);
        }

        $isLive = now()->greaterThanOrEqualTo($auction->start_date);

        return response()->json([
            'auction' => $auction,
            'is_live' => $isLive,
        ]);
    }

    public function getRelatedAuctions($id)
    {
        $cacheKey = "related_auctions_$id";
        $relatedAuctions = Cache::remember($cacheKey, 60, function() use ($id) {
            $auction     = Auction::findOrFail($id);
            $categoryIds = $auction->categories->pluck('id');

            $related = Auction::where('id', '!=', $id)
                ->where('status', 'approved')
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                })
                ->orderByRaw("CASE
                    WHEN start_date > NOW() THEN start_date
                    WHEN start_date <= NOW() AND end_date >= NOW() THEN end_date
                    ELSE end_date
                    END ASC")
                ->limit(5)
                ->get();

            return $related;
        });

        if ($relatedAuctions->isEmpty()) {
            return response()->json([
                'message' => 'No related auctions found. Please check your data.',
            ]);
        }

        return response()->json([
            'related_auctions' => $relatedAuctions,
        ]);
    }

    public function getLatestApprovedAuctions()
    {
        $latestAuctions = Cache::remember('latest_auctions', 60, function() {
            return Auction::where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();
        });

        return response()->json(['latest_auctions' => $latestAuctions], 200);
    }

    public function incrementViewCount($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->increment('view_count');

        Cache::forget("auction_$id");

        return response()->json([
            'message' => 'View count incremented successfully', 
            'view_count' => $auction->view_count
        ]);
    }

    public function search(Request $request)
    {
        $query = Auction::query();
        $query->where('status', 'approved')->orWhere('status', 'completed');

        if ($request->has('scheduled') && $request->scheduled) {
            $query->where('start_date', '>', now());
        }

        if ($request->has('ended') && $request->ended) {
            $query->where('end_date', '<', now());
        }
        if ($request->has('category')) {
            $categoryId = $request->category;
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            });
        }

        if ($request->has('live') && $request->live) {
            $query->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
        }

        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        $auctions = $query->get();

        return response()->json(['auctions' => $auctions]);
    }

    public function getWinnerAndHighestBid($auctionId)
    {
        $cacheKey = "winner_highest_bid_$auctionId";
        $data = Cache::remember($cacheKey, 60, function() use ($auctionId) {
            $auction    = Auction::findOrFail($auctionId);
            $highestBid = Bid::whereHas('auctionUser', function ($query) use ($auctionId) {
                $query->where('auction_id', $auctionId);
            })
            ->orderBy('bid_value', 'desc')
            ->first();
            if ($highestBid) {
                $winner = $highestBid->auctionUser->user;
                return [
                    'winner' => [
                        'id'    => $winner->id,
                        'name'  => $winner->name,
                        'email' => $winner->email,
                    ],
                    'highest_bid' => $highestBid->bid_value,
                ];
            }
            return null;
        });

        if ($data) {
            return response()->json($data);
        }

        return response()->json([
            'message' => 'No bids found for this auction.',
        ], 404);
    }
}
