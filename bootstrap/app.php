<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'league.organizer' => \App\Http\Middleware\CheckLeagueOrganizer::class,
            'league.viewer' => \App\Http\Middleware\CheckLeagueViewer::class,
            'auction.access' => \App\Http\Middleware\CheckAuctionAccess::class,
            'live.auction' => \App\Http\Middleware\CheckLiveAuctionAccess::class,
            'team.owner' => \App\Http\Middleware\CheckTeamOwner::class,
            'admin' => \App\Http\Middleware\CheckAdminAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
