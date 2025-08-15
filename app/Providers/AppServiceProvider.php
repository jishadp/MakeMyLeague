<?php

namespace App\Providers;

use App\Models\League;
use App\Models\Team;
use App\Models\User;
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
        // Share the default league, all active leagues, and stats with all views
        View::composer('*', function ($view) {
            try {
                $defaultLeague = League::where('is_default', true)->first();
                $activeLeagues = League::where('status', 'active')->orderBy('name')->get();
                
                // Get statistics for footer
                $stats = [
                    'leagues' => \App\Models\League::where('status', '!=', 'cancelled')->count(),
                    'teams' => \App\Models\Team::count(),
                    'players' => \App\Models\User::players()->count(),
                ];
                
                $view->with([
                    'defaultLeague' => $defaultLeague,
                    'navLeagues' => $activeLeagues,
                    'stats' => $stats
                ]);
            } catch (\Exception $e) {
                // Handle case when database isn't set up yet
                $view->with([
                    'defaultLeague' => null,
                    'navLeagues' => collect(),
                    'stats' => [
                        'leagues' => 0,
                        'teams' => 0,
                        'players' => 0
                    ]
                ]);
            }
        });
    }
}
