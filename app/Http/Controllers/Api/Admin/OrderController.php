<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getRecentOrders()
    {
        $orders = Order::with(['transaction', 'winner'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'user_name' => $order->winner ? $order->winner->name : 'Unknown',
                    'price' => $order->transaction ? $order->transaction->pay_amount : 'N/A',
                    'payment_status' => $order->transaction ? $order->transaction->transaction_status : 'N/A',
                    'status' => $order->status,
                ];
            });

        return response()->json(['orders' => $orders]);
    }

}
