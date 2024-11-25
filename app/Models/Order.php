<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'user_id',
        'transaction_id',
        'status',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }
    public function winner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
