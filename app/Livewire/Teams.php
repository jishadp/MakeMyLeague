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
        try {
            // Just trigger re-render silently
            $this->dispatch('refresh-teams')->self();
        } catch (\Exception $e) {
            // Silently ignore errors, log them if in development
            if (config('app.debug')) {
                \Log::warning("Error refreshing teams: " . $e->getMessage());
            }
        }
    }
    
    protected $listeners = ['refreshTeams' => 'refreshTeams'];

    public function render()
    {
        $this->teams = LeagueTeam::where('league_id', $this->leagueId)
            ->with([
                'team',
                'leaguePlayers' => function($query) {
                    $query->with(['player.position', 'player.primaryGameRole.gamePosition'])
                          ->whereIn('status', ['retained', 'sold'])
                          ->orderByRaw("FIELD(status, 'retained', 'sold')")
                          ->orderBy('bid_price', 'desc');
                }
            ])
            ->withCount('leaguePlayers')
            ->get();
        return view('livewire.teams');
    }
}
