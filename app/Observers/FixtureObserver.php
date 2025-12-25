<?php

namespace App\Observers;

use App\Models\Fixture;

class FixtureObserver
{
    /**
     * Handle the Fixture "created" event.
     */
    public function created(Fixture $fixture): void
    {
        //
    }

    /**
     * Handle the Fixture "updated" event.
     */
    public function updated(Fixture $fixture): void
    {
        if ($fixture->wasChanged('status') && $fixture->status == 'completed') {
            $this->updateLeagueStandings($fixture->league_id);
        }
    }

    protected function updateLeagueStandings($leagueId)
    {
        $leagueTeams = \App\Models\LeagueTeam::where('league_id', $leagueId)->get();
        $fixtures = \App\Models\Fixture::where('league_id', $leagueId)
            ->where('status', 'completed')
            ->get();

        foreach ($leagueTeams as $team) {
            $played = 0;
            $won = 0;
            $lost = 0;
            $drawn = 0;
            $goalsFor = 0;
            $goalsAgainst = 0;
            $points = 0;

            foreach ($fixtures as $match) {
                if ($match->home_team_id == $team->id || $match->away_team_id == $team->id) {
                    $played++;
                    
                    $isHome = $match->home_team_id == $team->id;
                    $myScore = $isHome ? $match->home_score : $match->away_score;
                    $opponentScore = $isHome ? $match->away_score : $match->home_score;

                    $goalsFor += $myScore;
                    $goalsAgainst += $opponentScore;

                    if ($myScore > $opponentScore) {
                        $won++;
                        $points += 3; // Standard 3 points for win
                    } elseif ($myScore < $opponentScore) {
                        $lost++;
                    } else {
                        $drawn++;
                        $points += 1; // Standard 1 point for draw
                    }
                }
            }

            $team->update([
                'played' => $played,
                'won' => $won,
                'lost' => $lost,
                'drawn' => $drawn,
                'points' => $points,
                'goals_for' => $goalsFor,
                'goals_against' => $goalsAgainst,
                'goal_difference' => $goalsFor - $goalsAgainst,
            ]);
        }
    }

    /**
     * Handle the Fixture "deleted" event.
     */
    public function deleted(Fixture $fixture): void
    {
        //
    }

    /**
     * Handle the Fixture "restored" event.
     */
    public function restored(Fixture $fixture): void
    {
        //
    }

    /**
     * Handle the Fixture "force deleted" event.
     */
    public function forceDeleted(Fixture $fixture): void
    {
        //
    }
}
