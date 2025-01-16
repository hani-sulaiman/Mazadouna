<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = "categories";
    protected $fillable = ['title'];

    /**
     * Define the many-to-many relationship with Auction.
     */
    public function auctions()
    {
        return $this->belongsToMany(Auction::class, 'auction_category');
    }
}
