<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class AuctionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'starting_price' => 'required|numeric',
            'description' => 'nullable|string',
            'thumbnail_image' => 'required', 
            'more_images.*' => 'required', 
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'min_increment' => 'required|numeric',
            'shipping_details' => 'required|string',
            'product_type' => 'required|string',
            'tags' => 'nullable|array',
            'categories' => 'required|array|min:1', 
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $thumbnailPath = $request->file('thumbnail_image')->move(public_path('images'), uniqid() . '_' . $request->file('thumbnail_image')->getClientOriginalName());
        $thumbnailUrl = '/images/' . basename($thumbnailPath);

        $moreImages = [];
        if ($request->hasFile('more_images')) {
            foreach ($request->file('more_images') as $image) {
                $path = $image->move(public_path('images'), uniqid() . '_' . $image->getClientOriginalName());
                $moreImages[] = '/images/' . basename($path);
            }
        }

        $auction = Auction::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'starting_price' => $request->starting_price,
            'description' => $request->description,
            'thumbnail_image' => $thumbnailUrl,
            'more_images' => $moreImages,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'min_increment' => $request->min_increment,
            'shipping_details' => $request->shipping_details,
            'product_type' => $request->product_type,
            'tags' => $request->tags,
            'status' => 'pending',
        ]);


        $auction->categories()->attach($request->categories);

        return response()->json($auction, 201);
    }
    public function update(Request $request, $id)
    {
        $auction = Auction::find($id);
        if (!$auction || $auction->user_id !== Auth::id()) {
            return response()->json(['message' => 'Auction not found or unauthorized.'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'starting_price' => 'required|numeric',
            'description' => 'nullable|string',
            'thumbnail_image' => 'nullable|image',
            'more_images.*' => 'nullable|image',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'min_increment' => 'required|numeric',
            'shipping_details' => 'required|string',
            'product_type' => 'required|string',
            'tags' => 'nullable|array',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('thumbnail_image')) {
            if ($auction->thumbnail_image && file_exists(public_path($auction->thumbnail_image))) {
                unlink(public_path($auction->thumbnail_image));
            }
            $thumbnailPath = $request->file('thumbnail_image')->move(public_path('images'), uniqid() . '_' . $request->file('thumbnail_image')->getClientOriginalName());
            $auction->thumbnail_image = '/images/' . basename($thumbnailPath);
        }
        $moreImages = [];
        if ($request->hasFile('more_images')) {
            if ($auction->more_images) {
                foreach ($auction->more_images as $oldImage) {
                    if (file_exists(public_path($oldImage))) {
                        unlink(public_path($oldImage));
                    }
                }
            }
            foreach ($request->file('more_images') as $image) {
                $path = $image->move(public_path('images'), uniqid() . '_' . $image->getClientOriginalName());
                $moreImages[] = '/images/' . basename($path);
            }
            $auction->more_images = $moreImages;
        }
        $auction->update([
            'name' => $request->name,
            'starting_price' => $request->starting_price,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'min_increment' => $request->min_increment,
            'shipping_details' => $request->shipping_details,
            'product_type' => $request->product_type,
            'tags' => $request->tags,
        ]);
        $auction->categories()->sync($request->categories);

        return response()->json($auction);
    }
    public function destroy($id)
    {
        $auction = Auction::find($id);

        if (!$auction) {
            return response()->json(['message' => 'Auction not found.'], 404);
        }

        if (now()->greaterThanOrEqualTo($auction->start_date) || $auction->status === 'completed') {
            return response()->json(['message' => 'Cannot delete auction after it has started or completed.'], 403);
        }

        if ($auction->thumbnail_image && file_exists(public_path($auction->thumbnail_image))) {
            unlink(public_path($auction->thumbnail_image));
        }

        if ($auction->more_images) {
            foreach ($auction->more_images as $image) {
                if (file_exists(public_path($image))) {
                    unlink(public_path($image));
                }
            }
        }

        $auction->delete();

        return response()->json(['message' => 'Auction and its images deleted successfully.']);
    }
    public function getUserAuctions()
    {
        $userId = Auth::id();
        $auctions = Auction::with('categories')->where('user_id', $userId)->get();

        return response()->json(['auctions' => $auctions]);
    }
    public function getUserAuction($id)
    {
        $userId = Auth::id(); 
        $auction = Auction::with('categories')
            ->where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$auction) {
            return response()->json(['message' => 'Auction not found or not accessible'], 404);
        }

        return response()->json(['auction' => $auction]);
    }
    public function getLiveAuctionDetails($id)
    {
        $user = Auth::user();

        $auction = Auction::with('categories')->findOrFail($id);

        $isLive = now()->between($auction->start_date, $auction->end_date);
        $hasJoined = AuctionUser::where('auction_id', $id)
            ->where('user_id', $user->id)
            ->exists();

        $highestBid = $auction->bids()->max('bid_value') ?? $auction->starting_price;

        return response()->json([
            'auction' => $auction,
            'is_live' => $isLive,
            'has_joined' => $hasJoined,
            'current_price' => $highestBid,
        ]);
    }
    public function getBidsAndViewers($id)
    {
        $auction = Auction::with('bids.auctionUser.user')->findOrFail($id);

        $bids = $auction->bids()
            ->with('auctionUser.user:id,name')
            ->orderBy('bid_date', 'desc')
            ->get()
            ->map(function ($bid) {
                return [
                    'username'  => $bid->auctionUser->user->name,
                    'bid_value' => $bid->bid_value,
                    'bid_date'  => $bid->bid_date,
                ];
            });

        $viewersCount = AuctionUser::where('auction_id', $auction->id)->count();

        return response()->json([
            'bids'          => $bids,
            'viewers_count' => $viewersCount,
        ]);
    }
    public function joinAuction(Request $request, $id)
    {
        $auction = Auction::find($id);

        if (!$auction) {
            return response()->json(['message' => 'Auction not found.'], 404);
        }

        if ($auction->status !== 'approved') {
            return response()->json(['message' => 'This auction is not available for joining.'], 403);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $auctionUser = AuctionUser::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->first();

        if ($auctionUser) {
            $auctionUser->status = $request->status;
            $auctionUser->save();

            return response()->json([
                'message' => 'Updated status successfully to ' . $request->status,
                'auction_user' => $auctionUser
            ]);
        } else {
            $auctionUser = AuctionUser::create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'status' => 'bidder',
                'join_date' => now(),
            ]);

            return response()->json([
                'message' => 'Joined auction successfully as ' . $request->status,
                'auction_user' => $auctionUser
            ]);
        }
    }
}
