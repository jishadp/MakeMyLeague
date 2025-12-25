<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    public function assignScorer(Request $request, Fixture $fixture)
    {
        $this->authorize('assignScorer', $fixture);

        $validated = $request->validate([
            'scorer_id' => 'required|exists:users,id',
        ]);

        $fixture->update(['scorer_id' => $validated['scorer_id']]);

        return back()->with('success', 'Scorer assigned successfully.');
    }
}
