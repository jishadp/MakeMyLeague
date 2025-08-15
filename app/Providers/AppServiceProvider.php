<?php

namespace App\Providers;

use App\Models\League;
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
        // Share the default league and all active leagues with all views
        View::composer('*', function ($view) {
            try {
                $defaultLeague = League::where('is_default', true)->first();
                $activeLeagues = League::where('status', 'active')->orderBy('name')->get();
                
                $view->with([
                    'defaultLeague' => $defaultLeague,
                    'navLeagues' => $activeLeagues
                ]);
            } catch (\Exception $e) {
                // Handle case when database isn't set up yet
                $view->with([
                    'defaultLeague' => null,
                    'navLeagues' => collect()
                ]);
            }
        });
    }
}
