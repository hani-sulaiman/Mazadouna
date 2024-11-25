<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = ['auction_user_id', 'bid_value', 'bid_date'];

    public function auctionUser()
    {
        return $this->belongsTo(AuctionUser::class);
    }
}
