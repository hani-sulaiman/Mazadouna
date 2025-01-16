<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    function convertBtcToUsd($btcAmount)
    {
        return $btcAmount * 76318.92;
    }
    public function generatePaymentLinks(Request $request)
    {
        $user =Auth::user();
    
        $auctionId = $request->auction_id;
        $winner = Winner::where('auction_id', $auctionId)->where('user_id', $user->id)->first();
        if (!$winner) {
            return response()->json(['message' => 'Only the auction winner can generate payment links.'], 403);
        }
        $existingOrder = Order::where('auction_id', $auctionId)->where('user_id', $user->id)->first();
        if ($existingOrder) {
            return response()->json(['message' => 'Payment order already exists for this auction winner.'], 400);
        }
    
        $order = Order::create([
            'auction_id' => $auctionId,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    
        $admin = User::where('is_admin', true)->first();
        if (!$admin) {
            return response()->json(['message' => 'Admin user not found.'], 404);
        }
    
        $auction = $order->auction;
        $endedPrice = $auction->ended_price;
        $endedPrice = $this->convertBtcToUsd(floatval($endedPrice));
        $adminAmount = $endedPrice * 0.10;
        $ownerAmount = $endedPrice * 0.90;
    
        $currency = 'BTC';
        $ipnCallbackUrl = 'https://webhook.site/your-unique-url';
        $successUrl = 'https://yourwebsite.com/payment/success';
        $cancelUrl = 'https://yourwebsite.com/payment/cancel';
        $apiKey = "C6XBCT6-N01M1PQ-H8ZRDS0-YN62A3J";
        $baseUrl = "https://api-sandbox.nowpayments.io/v1";

        try {
            DB::beginTransaction();
    
            $adminResponse = Http::withHeaders([
                 'User-Agent' => 'MazadounaApp/1.0',
                'x-api-key' => $apiKey,
            ])->withoutVerifying()->post("{$baseUrl}/payment", [
                'price_amount' => intval($adminAmount),
                'price_currency' => 'USD',
                'pay_currency' => "BTC",
                'order_id' => "admin_payment_{$order->id}",
                'order_description' => 'Payment to admin',
                'ipn_callback_url' => $ipnCallbackUrl,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);
            if (!$adminResponse->successful()) {
                throw new \Exception('Failed to generate admin payment link: ' . $adminResponse->body());
            }
    
            $adminPaymentData = $adminResponse->json();
    
            $adminTransaction = Transaction::create([
                'sender_id' => $user->id,
                'receiver_id' => $admin->id,
                'currency' => $currency,
                'pay_amount' => $adminPaymentData['pay_amount'], 
                'amount' => $adminAmount,
                'fiat_amount' => $adminPaymentData['price_amount'],
                'transaction_status' => $adminPaymentData['payment_status'],
                'payin_address' => $adminPaymentData['pay_address'],
                'status' => 'pending',
                'order_id' => $order->id, 
                'date' => now(),
            ]);
            $ownerResponse = Http::withHeaders([
                  'User-Agent' => 'MazadounaApp/1.0',
                'x-api-key' => $apiKey,
            ])->withoutVerifying()->post("{$baseUrl}/payment", [
                'price_amount' => intval($ownerAmount),
                'price_currency' => 'USD',
                'pay_currency' => "BTC",
                'order_id' => "owner_payment_{$order->id}",
                'order_description' => 'Payment to auction owner',
                'ipn_callback_url' => $ipnCallbackUrl,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);
    
            if (!$ownerResponse->successful()) {
                throw new \Exception('Failed to generate owner payment link: ' . $ownerResponse->body());
            }
    
            $ownerPaymentData = $ownerResponse->json();
    
            $ownerTransaction = Transaction::create([
                'sender_id' => $user->id,
                'receiver_id' => $auction->user_id,
                'currency' => $currency,
                'amount' => $ownerAmount,
                'pay_amount' => $ownerPaymentData['pay_amount'], 
                'fiat_amount' => $ownerPaymentData['price_amount'],
                'transaction_status' => $ownerPaymentData['payment_status'],
                'payin_address' => $ownerPaymentData['pay_address'],
                'status' => 'pending',
                'order_id' => $order->id,
                'date' => now(),
            ]);

            $order->transaction_id = $adminTransaction->id;
            $order->save();
    
            DB::commit();
    
            return response()->json([
                'message' => 'Payment links generated successfully.',
                'admin_payment_link' => $adminPaymentData['invoice_url'] ?? null,
                'owner_payment_link' => $ownerPaymentData['invoice_url'] ?? null,
                'transactions' => [
                    'admin_transaction' => $adminTransaction,
                    'owner_transaction' => $ownerTransaction
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to generate payment links.', 'error' => $e->getMessage()], 500);
        }
    }
    
}
