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

    public function refreshTeams()
    {
        $this->render();
    }
    
    protected $listeners = ['refreshTeams' => 'refreshTeams'];

    public function render()
    {
        $this->teams = LeagueTeam::where('league_id', $this->leagueId)
            ->with(['team', 'leaguePlayers'])
            ->withCount('leaguePlayers')
            ->get();
        return view('livewire.teams');
    }
}
