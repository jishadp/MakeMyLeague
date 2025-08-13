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
        // Share the default league with all views
        View::composer('*', function ($view) {
            try {
                $defaultLeague = League::where('is_default', true)->first();
                $view->with('defaultLeague', $defaultLeague);
            } catch (\Exception $e) {
                // Handle case when database isn't set up yet
                $view->with('defaultLeague', null);
            }
        });
    }
}
