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

    public function render()
    {
        $this->bids = Auction::whereHas('leagueTeam',function($query){
            $query->where('league_id',$this->leagueId);
        })->where('status','won')->get();

        return view('livewire.highest-bids');
    }
}
