<?php

namespace App\Observers;

use App\Models\LeaguePlayer;
use App\Services\AuctionAccessService;
use Illuminate\Support\Facades\Log;

class LeaguePlayerObserver
{
    protected $auctionAccessService;

    public function __construct(AuctionAccessService $auctionAccessService)
    {
        $this->auctionAccessService = $auctionAccessService;
    }

    /**
     * Handle the LeaguePlayer "updated" event.
     */
    public function updated(LeaguePlayer $leaguePlayer): void
    {
        // Check if status changed to 'auctioning'
        if ($leaguePlayer->wasChanged('status') && $leaguePlayer->status === 'auctioning') {
            $this->auctionAccessService->handlePlayerAuctioning($leaguePlayer);
            
            Log::info("LeaguePlayer {$leaguePlayer->id} status changed to auctioning. Triggering auction access control update.");
        }

        // Check if status changed from 'auctioning' to something else
        if ($leaguePlayer->wasChanged('status') && $leaguePlayer->getOriginal('status') === 'auctioning') {
            $league = $leaguePlayer->league;
            
            // Check if there are any other players currently being auctioned
            $otherAuctioningPlayers = LeaguePlayer::where('league_id', $league->id)
                ->where('status', 'auctioning')
                ->where('id', '!=', $leaguePlayer->id)
                ->count();

            // Check if there are any available players left to auction
            $availablePlayers = LeaguePlayer::where('league_id', $league->id)
                ->where('status', 'available')
                ->where('retention', false) // Exclude retained players
                ->count();

            // If no other players are being auctioned AND no available players left, end the auction
            if ($otherAuctioningPlayers === 0 && $availablePlayers === 0) {
                $league->update([
                    'auction_active' => false,
                    'auction_ended_at' => now()
                ]);

                // Refresh access cache for all users
                $this->auctionAccessService->refreshLeagueAccessCache($league);
                
                Log::info("Auction completed for league {$league->id}. All players processed. Auction ended.");
            }
            // If no other players are being auctioned but there are still available players, keep auction active
            else if ($otherAuctioningPlayers === 0) {
                Log::info("Player {$leaguePlayer->id} finished auctioning in league {$league->id}. Auction remains active for remaining players.");
            }
        }
    }

    /**
     * Handle the LeaguePlayer "created" event.
     */
    public function created(LeaguePlayer $leaguePlayer): void
    {
        // If a player is created with 'auctioning' status, handle it
        if ($leaguePlayer->status === 'auctioning') {
            $this->auctionAccessService->handlePlayerAuctioning($leaguePlayer);
            
            Log::info("LeaguePlayer {$leaguePlayer->id} created with auctioning status. Triggering auction access control update.");
        }
    }
}
