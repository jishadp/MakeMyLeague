<?php

namespace App\Providers;

use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\Team;
use App\Models\TeamAuctioneer;
use App\Models\TeamOwner;
use App\Models\User;
use App\Observers\LeaguePlayerObserver;
use App\Observers\TeamAuctioneerObserver;
use App\Observers\TeamOwnerObserver;
use App\Services\AuctionAccessService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for auction access control
        $this->registerObservers();

        // Register policies
        $this->registerPolicies();

        // Share stats with all views
        View::composer('*', function ($view) {
            try {
                // Get statistics for footer (only approved leagues for public display)
                $stats = [
                    'leagues' => \App\Models\League::whereHas('organizers', function($query) {
                        $query->where('status', 'approved');
                    })->where('status', '!=', 'cancelled')->count(),
                    'teams' => \App\Models\Team::count(),
                    'players' => \App\Models\User::count(),
                ];
                
                $view->with([
                    'stats' => $stats
                ]);
            } catch (\Exception $e) {
                // Handle case when database isn't set up yet
                $view->with([
                    'stats' => [
                        'leagues' => 0,
                        'teams' => 0,
                        'players' => 0
                    ]
                ]);
            }
        });
    }

    /**
     * Register policies.
     */
    protected function registerPolicies(): void
    {
        \Illuminate\Support\Facades\Gate::policy(League::class, \App\Policies\AuctionPolicy::class);
    }

    /**
     * Register model observers for auction access control.
     */
    protected function registerObservers(): void
    {
        // Register observers with dependency injection
        $auctionAccessService = app(AuctionAccessService::class);
        
        LeaguePlayer::observe(new LeaguePlayerObserver($auctionAccessService));
        TeamOwner::observe(new TeamOwnerObserver($auctionAccessService));
        TeamAuctioneer::observe(new TeamAuctioneerObserver($auctionAccessService));
    }
}
