<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with(['owners', 'localBody.district'])
            ->withCount('leagueTeams')
            ->latest()
            ->paginate(20);
        
        return view('admin.teams.index', compact('teams'));
    }

    public function edit(Team $team)
    {
        $team->load(['owners', 'localBody.district.state']);
        return view('admin.teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $team->update($request->only(['name']));

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team updated successfully!');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('admin.teams.index')
            ->with('success', 'Team deleted successfully!');
    }

    public function uploadLogo(Request $request, Team $team)
    {
        $request->validate(['logo' => 'required|image|max:2048']);
        
        if ($team->logo) Storage::disk('public')->delete($team->logo);
        
        $team->update(['logo' => $request->file('logo')->store('teams/logos', 'public')]);
        
        return back()->with('success', 'Logo uploaded successfully!');
    }

    public function uploadBanner(Request $request, Team $team)
    {
        $request->validate(['banner' => 'required|image|max:5120']);
        
        if ($team->banner) Storage::disk('public')->delete($team->banner);
        
        $team->update(['banner' => $request->file('banner')->store('teams/banners', 'public')]);
        
        return back()->with('success', 'Banner uploaded successfully!');
    }

    public function removeLogo(Team $team)
    {
        if ($team->logo) {
            Storage::disk('public')->delete($team->logo);
            $team->update(['logo' => null]);
        }
        
        return back()->with('success', 'Logo removed successfully!');
    }

    public function removeBanner(Team $team)
    {
        if ($team->banner) {
            Storage::disk('public')->delete($team->banner);
            $team->update(['banner' => null]);
        }
        
        return back()->with('success', 'Banner removed successfully!');
    }
}
