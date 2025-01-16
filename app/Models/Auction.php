<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'starting_price',
        'description',
        'thumbnail_image',
        'more_images',
        'start_date',
        'end_date',
        'min_increment',
        'shipping_details',
        'product_type',
        'ended_price',
        'tags',
        'user_id',
        'status',
    ];

    protected $casts = [
        'more_images' => 'array',
        'tags' => 'array',
    ];
     // Relationship to the user who created the auction
     public function user()
     {
         return $this->belongsTo(User::class);
     }
 
     // Relationship to get the winner of the auction
     public function winner()
     {
         return $this->hasOne(Winner::class);
     }
     public function order()
     {
         return $this->hasOne(Order::class, 'auction_id');
     }
     
     // Relationship to get all bids on the auction
     public function bids()
     {
         return $this->hasManyThrough(Bid::class, AuctionUser::class, 'auction_id', 'auction_user_id');
     }
 
     // Relationship to get all participants in the auction
     public function participants()
     {
         return $this->hasMany(AuctionUser::class);
     }
 
     // Many-to-many relationship with categories
     public function categories()
     {
         return $this->belongsToMany(Category::class, 'auction_category', 'auction_id', 'category_id');
     }
     
}
