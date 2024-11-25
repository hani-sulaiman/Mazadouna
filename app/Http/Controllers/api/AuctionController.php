<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auction;
use App\Models\AuctionUser;
use App\Models\Bid;
use App\Models\Winner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AuctionController extends Controller
{

    // عرض قائمة المزادات (فقshط المزادات الموافق عليها)
    public function index()
    {
        // جلب المزادات التي حالتها "approved" فقط
        $auctions = Auction::where('status', 'approved')->get();
        return response()->json($auctions);
    }
    // في AuctionController.php
    public function getUserAuctions()
    {
        $userId = Auth::id();
        $auctions = Auction::with('categories')->where('user_id', $userId)->get();

        return response()->json(['auctions' => $auctions]);
    }
    // عرض تفاصيل مزاد محدد
    public function getUserAuction($id)
    {
        $userId = Auth::id(); // Get the ID of the authenticated user

        // Fetch the auction that belongs to the authenticated user with categories
        $auction = Auction::with('categories')
            ->where('user_id', $userId)
            ->where('id', $id)
            ->first();

        // If the auction does not exist or does not belong to the user, return a 404 error
        if (!$auction) {
            return response()->json(['message' => 'Auction not found or not accessible'], 404);
        }

        return response()->json(['auction' => $auction]);
    }
    public function show($id)
    {
        $auction = Auction::find($id);

        if (!$auction) {
            return response()->json(['message' => 'Auction not found.'], 404);
        }

        // التحقق من حالة المزاد (يجب أن يكون "approved" ليتم عرضه)
        if (!in_array($auction->status, ['completed', 'approved'])) {
            return response()->json(['message' => 'This auction is not available for viewing.'], 403);
        }

        // التحقق مما إذا كان المزاد قد بدأ بالفعل
        $isLive = now()->greaterThanOrEqualTo($auction->start_date);

        return response()->json([
            'auction' => $auction,
            'is_live' => $isLive
        ]);
    }

    // إنشاء مزاد جديد
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'starting_price' => 'required|numeric',
            'description' => 'nullable|string',
            'thumbnail_image' => 'required', // Main image
            'more_images.*' => 'required', // Additional images
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'min_increment' => 'required|numeric',
            'shipping_details' => 'required|string',
            'product_type' => 'required|string',
            'tags' => 'nullable|array',
            'categories' => 'required|array|min:1', // At least one category
            'categories.*' => 'exists:categories,id', // Each category must exist in the categories table
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload main image to public/images
        $thumbnailPath = $request->file('thumbnail_image')->move(public_path('images'), uniqid() . '_' . $request->file('thumbnail_image')->getClientOriginalName());
        $thumbnailUrl = '/images/' . basename($thumbnailPath);

        // Upload additional images to public/images
        $moreImages = [];
        if ($request->hasFile('more_images')) {
            foreach ($request->file('more_images') as $image) {
                $path = $image->move(public_path('images'), uniqid() . '_' . $image->getClientOriginalName());
                $moreImages[] = '/images/' . basename($path);
            }
        }


        // Create auction
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

        // Attach categories
        $auction->categories()->attach($request->categories);

        return response()->json($auction, 201);
    }


    public function update(Request $request, $id)
    {
        // التحقق من المزاد ومالكه
        $auction = Auction::find($id);
        if (!$auction || $auction->user_id !== Auth::id()) {
            return response()->json(['message' => 'Auction not found or unauthorized.'], 404);
        }
        // التحقق من صحة البيانات
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

        // Update main image if new one is uploaded
        if ($request->hasFile('thumbnail_image')) {
            if ($auction->thumbnail_image && file_exists(public_path($auction->thumbnail_image))) {
                unlink(public_path($auction->thumbnail_image));
            }
            $thumbnailPath = $request->file('thumbnail_image')->move(public_path('images'), uniqid() . '_' . $request->file('thumbnail_image')->getClientOriginalName());
            $auction->thumbnail_image = '/images/' . basename($thumbnailPath);
        }

        // Update additional images if new ones are uploaded
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

        // Update auction details
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
        // تحديث الفئات
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

        // Delete main image if it exists
        if ($auction->thumbnail_image && file_exists(public_path($auction->thumbnail_image))) {
            unlink(public_path($auction->thumbnail_image));
        }

        // Delete additional images if they exist
        if ($auction->more_images) {
            foreach ($auction->more_images as $image) {
                if (file_exists(public_path($image))) {
                    unlink(public_path($image));
                }
            }
        }

        // Delete the auction from the database
        $auction->delete();

        return response()->json(['message' => 'Auction and its images deleted successfully.']);
    }
    public function pendingAuctions()
    {
        // جلب المزادات التي حالتها "pending" فقط
        $auctions = Auction::where('status', 'pending')->get();
        return response()->json($auctions);
    }
    // الموافقة على نشر مزاد
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
    public function getBidsAndViewers($id)
    {
        // Find the auction by ID
        $auction = Auction::with('bids.auctionUser.user')->findOrFail($id);

        // Get all bids for the auction and include the bidder's username
        $bids = $auction->bids()
            ->with('auctionUser.user:id,name')
            ->orderBy('bid_date', 'desc')
            ->get()
            ->map(function ($bid) {
                return [
                    'username' => $bid->auctionUser->user->name,
                    'bid_value' => $bid->bid_value,
                    'bid_date' => $bid->bid_date,
                ];
            });

        // Get viewers count (users who joined the auction)
        $viewersCount = AuctionUser::where('auction_id', $auction->id)->count();

        return response()->json([
            'bids' => $bids,
            'viewers_count' => $viewersCount,
        ]);
    }


    // رفض نشر مزاد
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
    public function joinAuction(Request $request, $id)
    {
        $auction = Auction::find($id);

        if (!$auction) {
            return response()->json(['message' => 'Auction not found.'], 404);
        }

        // التحقق من أن المزاد "approved" ومتاح للانضمام
        if ($auction->status !== 'approved') {
            return response()->json(['message' => 'This auction is not available for joining.'], 403);
        }

        // التحقق من أن المستخدم مسجل دخول
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }


        // البحث عن انضمام المستخدم الحالي إلى المزاد
        $auctionUser = AuctionUser::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->first();

        if ($auctionUser) {
            // تعديل حالة المستخدم إذا كان قد انضم مسبقًا
            $auctionUser->status = $request->status;
            $auctionUser->save();

            return response()->json([
                'message' => 'Updated status successfully to ' . $request->status,
                'auction_user' => $auctionUser
            ]);
        } else {
            // إنشاء سجل جديد إذا لم يكن المستخدم قد انضم سابقًا
            $auctionUser = AuctionUser::create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'status' => 'bidder', // تحديد الحالة حسب اختيار المستخدم
                'join_date' => now(),
            ]);

            return response()->json([
                'message' => 'Joined auction successfully as ' . $request->status,
                'auction_user' => $auctionUser
            ]);
        }
    }
    public function getLiveAuctionDetails($id)
    {
        $user = Auth::user();

        // Fetch auction details along with categories
        $auction = Auction::with('categories')->findOrFail($id);

        // Check if auction is currently live
        $isLive = now()->between($auction->start_date, $auction->end_date);

        // Check if the user has joined the auction
        $hasJoined = AuctionUser::where('auction_id', $id)
            ->where('user_id', $user->id)
            ->exists();

        // Retrieve the highest bid for this auction, or use the starting price if no bids exist
        $highestBid = $auction->bids()->max('bid_value') ?? $auction->starting_price;

        return response()->json([
            'auction' => $auction,
            'is_live' => $isLive,
            'has_joined' => $hasJoined,
            'current_price' => $highestBid,
        ]);
    }
    public function getRelatedAuctions($id)
    {
        // Fetch the main auction
        $auction = Auction::findOrFail($id);

        // Retrieve categories associated with the main auction
        $categoryIds = $auction->categories->pluck('id');

        // Fetch related auctions including ended ones
        $relatedAuctions = Auction::where('id', '!=', $id)
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

        // Debugging step
        if ($relatedAuctions->isEmpty()) {
            return response()->json([
                'message' => 'No related auctions found. Please check your data.',
                'debug' => [
                    'categoryIds' => $categoryIds,
                    'auctionStatus' => $auction->status
                ]
            ]);
        }

        return response()->json([
            'related_auctions' => $relatedAuctions,
        ]);
    }


    // تابع لاسترجاع آخر 6 مزادات مضافة بحالة approved
    public function getLatestApprovedAuctions()
    {
        $latestAuctions = Auction::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return response()->json(['latest_auctions' => $latestAuctions], 200);
    }
    public function incrementViewCount($id)
    {
        $auction = Auction::findOrFail($id);

        // Increment the view count
        $auction->increment('view_count');

        return response()->json(['message' => 'View count incremented successfully', 'view_count' => $auction->view_count]);
    }
    public function search(Request $request)
    {
        $query = Auction::query();

        // تأكد من أن المزادات المعروضة هي فقط المزادات المعتمدة
        $query->where('status', 'approved')->orWhere('status', 'completed');

        // الفلترة حسب المزادات المخطط لها (التاريخ المستقبلي)
        if ($request->has('scheduled') && $request->scheduled) {
            $query->where('start_date', '>', now());
        }

        // الفلترة حسب المزادات المنتهية (التاريخ المنقضي)
        if ($request->has('ended') && $request->ended) {
            $query->where('end_date', '<', now());
        }

        // الفلترة حسب الفئة من خلال علاقة auction_category
        if ($request->has('category')) {
            $categoryId = $request->category;
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            });
        }

        // الفلترة حسب المزادات الحية (التاريخ الحالي بين تاريخ البدء والانتهاء)
        if ($request->has('live') && $request->live) {
            $query->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        }

        // البحث باستخدام الاسم (إذا تم تحديد اسم)
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        // تنفيذ الاستعلام واسترجاع النتائج
        $auctions = $query->get();

        return response()->json(['auctions' => $auctions]);
    }
    public function getWinnerAndHighestBid($auctionId)
    {
        // Fetch the auction to confirm it exists
        $auction = Auction::findOrFail($auctionId);

        // Get the highest bid for the specified auction by joining through auction_user
        $highestBid = Bid::whereHas('auctionUser', function ($query) use ($auctionId) {
            $query->where('auction_id', $auctionId);
        })
            ->orderBy('bid_value', 'desc')
            ->first();

        // Check if there is a highest bid
        if ($highestBid) {
            // Fetch winner details from the highest bid
            $winner = $highestBid->auctionUser->user;

            return response()->json([
                'winner' => [
                    'id' => $winner->id,
                    'name' => $winner->name,
                    'email' => $winner->email,
                ],
                'highest_bid' => $highestBid->bid_value,
            ]);
        }

        // Return response if no bid is found
        return response()->json([
            'message' => 'No bids found for this auction.',
        ], 404);
    }
}
