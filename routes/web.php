<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroundController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Get 3 featured grounds (highest capacity) for the landing page
    $featuredGrounds = \App\Models\Ground::with(['state', 'district', 'localBody'])
        ->where('is_available', true)
        ->orderBy('capacity', 'desc')
        ->take(3)
        ->get();
    
    // Get 3 featured teams for the landing page
    $featuredTeams = \App\Models\Team::with(['homeGround', 'localBody'])
        ->take(3)
        ->get();
        
    return view('welcome', compact('featuredGrounds', 'featuredTeams'));
});

Route::get('login',[LoginController::class,'login'])->name('login');
Route::post('do-login',[LoginController::class,'doLogin'])->name('do.login');
Route::get('logout',[LoginController::class,'logout'])->name('logout');

// Public routes
Route::get('grounds', [GroundController::class, 'index'])->name('grounds.index');
Route::get('grounds/{ground}', [GroundController::class, 'show'])->name('grounds.show');

// Team routes
Route::get('teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('teams/{team}', [TeamController::class, 'show'])->name('teams.show');

Route::middleware('auth')->group(function(){
    Route::get('dashboard',[DashboardController::class,'view'])->name('dashboard');
    
    // Leagues resource routes
    Route::resource('leagues', LeagueController::class);
    Route::post('leagues/{league}/set-default', [LeagueController::class, 'setDefault'])->name('leagues.setDefault');
});


