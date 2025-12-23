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
        $users = \App\Models\User::select('id', 'name', 'mobile')->orderBy('name')->take(500)->get(); 
        // Note: For large user bases, this approach might need optimization (e.g., AJAX search). 
        // Limiting to 500 for now to prevent memory issues, but assuming admin knows who to look for or searching is handled client-side if we use a library.
        // Better approach for production with many users: Use a search API. 
        // For this task, getting a list is a simple start, but 500 might miss the intended user.
        // Let's use a simple get() if the user base isn't huge, or Select2 with AJAX. 
        // Given constraints, I'll fetch all users but select only necessary columns to minimize memory.
        $users = \App\Models\User::select('id', 'name', 'mobile')->orderBy('name')->get();

        return view('admin.teams.edit', compact('team', 'users'));
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'nullable|exists:users,id'
        ]);

        $team->update($request->only(['name']));

        if ($request->has('owner_id') && $request->owner_id != $team->owner_id) {
            $team->update(['owner_id' => $request->owner_id]);
            
            // Sync the new owner in the pivot table as the primary owner
            // This replaces existing owners. If multiple owners are supported, this logic might need adjustment.
            // Based on 'primaryOwners' relation in Team model, 'role' => 'owner' is correct.
            $team->owners()->sync([$request->owner_id => ['role' => 'owner']]);
        }

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
