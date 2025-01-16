<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionUser extends Model
{
    use HasFactory;

    protected $fillable = ['auction_id', 'user_id', 'status', 'join_date'];
    protected $table = 'auctions_users';

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }
    
    public function bids()
    {
        return $this->hasMany(Bid::class, 'auction_user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
