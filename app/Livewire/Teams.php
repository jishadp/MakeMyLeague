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
        $categories = \App\Models\LeaguePlayerCategory::where('league_id', $this->leagueId)->get();
        
        $categoryCounts = \App\Models\LeaguePlayer::where('league_id', $this->leagueId)
            ->whereIn('status', ['sold', 'retained'])
            ->whereNotNull('league_player_category_id')
            ->select('league_team_id', 'league_player_category_id', \DB::raw('count(*) as count'))
            ->groupBy('league_team_id', 'league_player_category_id')
            ->get()
            ->groupBy('league_team_id');

        $this->teams = LeagueTeam::where('league_id', $this->leagueId)
            ->with([
                'team',
                'auctioneer', // Include auctioneer information
                'teamAuctioneer.auctioneer', // Include active team auctioneer
                'leaguePlayers' => function($query) {
                    $query->with(['player.position', 'player.primaryGameRole.gamePosition'])
                          ->where(function($q) {
                              $q->whereIn('status', ['retained', 'sold'])
                                ->orWhere('retention', true); // Include retained players regardless of status
                          })
                          ->orderByRaw("FIELD(status, 'retained', 'sold')")
                          ->orderBy('bid_price', 'desc');
                }
            ])
            ->withCount('leaguePlayers')
            ->get()
            ->map(function($team) use ($categories, $categoryCounts) {
                  $teamStats = $categoryCounts[$team->id] ?? collect();
                  $team->category_compliance = $categories->map(function($cat) use ($teamStats) {
                       $count = $teamStats->where('league_player_category_id', $cat->id)->first()->count ?? 0;
                       return [
                           'name' => $cat->name,
                           'min' => $cat->min_requirement,
                           'max' => $cat->max_requirement,
                           'current' => $count,
                           'met' => $count >= $cat->min_requirement,
                       ];
                  });
                  return $team;
            });
            
        return view('livewire.teams');
    }
}
