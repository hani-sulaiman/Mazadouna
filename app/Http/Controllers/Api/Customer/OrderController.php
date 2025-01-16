<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function getUserOrders()
    {
        $user = Auth::user();
        
        $order = Order::where('user_id', $user->id)
            ->with(['auction', 'transactions'])
            ->latest()
            ->first();

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
            $formattedOrder = null;
        }

        return response()->json($formattedOrder);
    }
}
