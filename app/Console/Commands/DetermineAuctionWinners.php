<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Winner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class DetermineAuctionWinners extends Command
{
    protected $signature = 'auction:determine-winners';
    protected $description = 'Determine winners for auctions that have ended';

    public function handle()
    {
        Log::info('Running auction:determine-winners at ' . now());
        $this->info("Starting to determine winners...");

        // Retrieve all auctions that are approved and have ended
        $auctions = Auction::where('status', 'approved')
                           ->where('end_date', '<=', Carbon::now())
                           ->get();

        foreach ($auctions as $auction) {
            // Find the highest bid for the auction
            $highestBid = Bid::whereHas('auctionUser', function ($query) use ($auction) {
                                $query->where('auction_id', $auction->id);
                            })
                            ->orderBy('bid_value', 'desc')
                            ->first();

            if ($highestBid) {
                // Set the ended price to the highest bid value
                $auction->ended_price = $highestBid->bid_value;
                $auction->status = 'completed';
                $auction->save();

                // Record the winner in the winners table
                Winner::create([
                    'auction_id' => $auction->id,
                    'user_id' => $highestBid->auctionUser->user_id,
                ]);

                $this->info("Winner determined for auction ID {$auction->id}: User ID {$highestBid->auctionUser->user_id} with bid value {$highestBid->bid_value}");
            } else {
                // If no bids, complete the auction without a winner
                $auction->status = 'completed';
                $auction->save();

                $this->info("No bids for auction ID {$auction->id}, auction completed without a winner.");
            }
        }

        $this->info("Finished determining winners.");
        return Command::SUCCESS;
    }
}
