<?php

namespace App\Livewire;

use App\Models\Auction;
use App\Models\LeaguePlayer;
use Livewire\Component;

class HighestBids extends Component
{
    public $bids;
    public $leagueId;
    
    public function mount($leagueId)
    {
        $this->leagueId = $leagueId;
    }
    
    public function refreshBids()
    {
        try {
            // Just trigger re-render silently
            $this->dispatch('refresh-highest-bids')->self();
        } catch (\Exception $e) {
            // Silently ignore errors, log them if in development
            if (config('app.debug')) {
                \Log::warning("Error refreshing highest bids: " . $e->getMessage());
            }
        }
    }
    
    protected $listeners = ['refreshBids' => 'refreshBids'];

    public function render()
    {
        // Get the latest bids for each player in this league
        // First, get all relevant league players with their highest bid
        $leaguePlayerIds = LeaguePlayer::where('league_id', $this->leagueId)
            ->whereIn('status', ['sold', 'auctioning'])
            ->pluck('id')
            ->toArray();
            
        // Get the highest bid for each player
        $this->bids = collect();
        if (!empty($leaguePlayerIds)) {
            // First get the latest bid for each player
            $latestBidsByPlayer = collect();
            
            foreach ($leaguePlayerIds as $playerId) {
                // Get the latest bid for this player
                $latestBid = Auction::with([
                    'leaguePlayer.player.position', 
                    'leaguePlayer.player.primaryGameRole.gamePosition', 
                    'leagueTeam.team'
                ])
                ->where('league_player_id', $playerId)
                ->whereIn('status', ['won', 'ask'])
                ->latest('id')  // Use latest by ID to ensure consistent ordering
                ->first();
                
                if ($latestBid) {
                    $latestBidsByPlayer->push($latestBid);
                }
            }
            
            // Sort by amount in descending order and take top 10
            $latestBids = $latestBidsByPlayer
                ->sortByDesc('amount')
                ->values()
                ->take(10);
            
            $this->bids = $latestBids;
        }

        return view('livewire.highest-bids');
    }
}
