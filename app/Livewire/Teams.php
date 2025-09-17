<?php

namespace App\Livewire;

use App\Models\LeagueTeam;
use App\Models\Team;
use Livewire\Component;

class Teams extends Component
{
    public $teams;
    public $leagueId;

    public function mount($leagueId)
    {
        $this->leagueId = $leagueId;
    }

    public function render()
    {
        $this->teams = LeagueTeam::withCount('players')->get();
        return view('livewire.teams');
    }
}
