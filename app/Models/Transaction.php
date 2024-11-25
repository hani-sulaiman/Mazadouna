<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'currency',
        'amount',
        'fiat_amount',
        'transaction_status',
        'payin_address',
        'pay_amount',
        'status',
        'date',
        'order_id'
    ];

    /**
     * Relationship to the User model for the sender of the transaction.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relationship to the User model for the receiver of the transaction.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Relationship to the Order model.
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }
    public function auction(){
        return $this->belongsTo(Auction::class,'');
    }
}
