<?php

namespace App\Http\Controllers;

use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController 
{
    public function view()
    {
        $leagues = League::with('game', 'organizer')->latest()->take(5)->get();
        $userLeagues = Auth::check() ? League::where('user_id', Auth::id())->get() : collect();
        
        return view('dashboard', compact('leagues', 'userLeagues'));
    }
}
