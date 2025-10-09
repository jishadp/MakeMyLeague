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
}
