<?php

namespace App\Livewire;

use App\Models\Auction;
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
        $this->render();
    }
    
    protected $listeners = ['refreshBids' => 'refreshBids'];

    public function render()
    {
        // Get the latest bids for each player in this league
        $this->bids = Auction::with(['leaguePlayer.player', 'leagueTeam.team'])
            ->whereHas('leagueTeam', function($query) {
                $query->where('league_id', $this->leagueId);
            })
            ->whereIn('status', ['won', 'ask']) // Include both won and current bids
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.highest-bids');
    }
}
