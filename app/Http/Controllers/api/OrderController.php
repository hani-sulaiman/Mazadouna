<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function getUserOrders()
    {
        $user = Auth::user();
        
        // Retrieve the latest order with its transactions for the logged-in user
        $order = Order::where('user_id', $user->id)
            ->with(['auction', 'transactions'])
            ->latest()
            ->first();

        // Format the order to match the frontend structure
        if ($order) {
            $formattedOrder = [
                'name' => $order->auction->name,
                'order_id' => $order->id,
                'status' => $order->status,
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'updated_at' => $order->updated_at->format('Y-m-d H:i'),
                'transactions' => $order->transactions->map(function ($transaction) {
                    return [
                        'payment_id' => $transaction->id,
                        'price' => $transaction->pay_amount,
                        'payment_link' => $transaction->payin_address,
                        'payment_status' => $transaction->transaction_status,
                    ];
                })
            ];
        } else {
            $formattedOrder = null; // No order found for user
        }

        return response()->json($formattedOrder);
    }
}
