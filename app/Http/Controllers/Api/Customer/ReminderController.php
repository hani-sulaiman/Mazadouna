<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $reminders = Reminder::where('user_id', $user->id)
            ->with('auction') 
            ->get();

        return response()->json(['reminders' => $reminders]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'auction_id' => 'required|exists:auctions,id',
        ]);

        $user = Auth::user();

        $exists = Reminder::where('user_id', $user->id)
            ->where('auction_id', $request->auction_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Auction is already in your reminders.'], 400);
        }

        $reminder = Reminder::create([
            'user_id' => $user->id,
            'auction_id' => $request->auction_id,
        ]);

        return response()->json(['message' => 'Reminder added successfully.', 'reminder' => $reminder], 201);
    }
    public function delete($id)
    {
        $user = Auth::user();

        $reminder = Reminder::where('user_id', $user->id)
            ->where('auction_id', $id)
            ->first();

        if (!$reminder) {
            return response()->json(['message' => 'Reminder not found.'], 404);
        }

        $reminder->delete();

        return response()->json(['message' => 'Reminder deleted successfully.']);
    }
}
