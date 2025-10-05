<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueFinance;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class LeagueFinanceController extends Controller
{
    /**
     * Display the finance dashboard for a league.
     */
    public function index(League $league)
    {
        // Check if user is organizer of this league
        if (!$this->isLeagueOrganizer($league)) {
            abort(403, 'You are not authorized to access this league\'s finances.');
        }

        $finances = $league->finances()
            ->with(['expenseCategory', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);

        $totalIncome = $league->total_income;
        $totalExpenses = $league->total_expenses;
        $netProfit = $league->net_profit;

        $incomeCategories = ExpenseCategory::income()->active()->get();
        $expenseCategories = ExpenseCategory::expense()->active()->get();

        return view('league-finances.index', compact(
            'league',
            'finances',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'incomeCategories',
            'expenseCategories'
        ));
    }

    /**
     * Show the form for creating a new finance record.
     */
    public function create(League $league)
    {
        if (!$this->isLeagueOrganizer($league)) {
            abort(403, 'You are not authorized to access this league\'s finances.');
        }

        // Load league with relationships to get accurate counts
        $league->load(['leaguePlayers', 'leagueTeams']);

        // Calculate total potential players: Total Teams × Max Players per Team
        $teamCount = $league->leagueTeams->count();
        $totalPotentialPlayers = $teamCount * $league->max_team_players;

        // Check for existing player registration income records
        $existingPlayerRegistration = LeagueFinance::where('league_id', $league->id)
            ->where('type', 'income')
            ->where('title', 'like', '%Player Registration%')
            ->first();

        // Get individual team registration records first
        $individualTeamRegistrations = LeagueFinance::where('league_id', $league->id)
            ->where('type', 'income')
            ->where(function($query) {
                $query->where('title', 'like', '%Team Registration Fee -%')
                      ->orWhere('title', 'like', '%Team Registration -%')
                      ->orWhere('title', 'like', '%Registration Fee -%');
            })
            ->get();
            
        // Debug: Log what we found
        \Log::info('Team Registration Debug', [
            'league_id' => $league->id,
            'individual_records_count' => $individualTeamRegistrations->count(),
            'individual_records' => $individualTeamRegistrations->pluck('title', 'amount'),
            'all_team_records' => LeagueFinance::where('league_id', $league->id)
                ->where('type', 'income')
                ->where('title', 'like', '%Team Registration%')
                ->pluck('title', 'amount')
        ]);
            
        // Check for existing combined team registration income records (only if no individual records)
        $existingTeamRegistration = null;
        if ($individualTeamRegistrations->count() == 0) {
            $existingTeamRegistration = LeagueFinance::where('league_id', $league->id)
                ->where('type', 'income')
                ->where('title', 'like', '%Team Registration%')
                ->where('title', 'not like', '%Team Registration Fee -%') // Exclude individual team records
                ->first();
        }

        // Calculate expected amounts and balances
        $expectedPlayerAmount = $totalPotentialPlayers * $league->player_reg_fee;
        $expectedTeamAmount = $teamCount * $league->team_reg_fee;
        
        $playerBalance = $existingPlayerRegistration ? ($expectedPlayerAmount - $existingPlayerRegistration->amount) : 0;
        
        // For team balance, check if we have individual team fees
        $teamBalance = 0;
        $individualTeamFees = [];
        
        if ($individualTeamRegistrations->count() > 0) {
            // We have individual team records - prioritize these
            $actualTeamTotal = 0;
            foreach ($individualTeamRegistrations as $record) {
                // Extract team name from title - handle multiple patterns
                $teamName = $record->title;
                if (strpos($teamName, 'Team Registration Fee - ') !== false) {
                    $teamName = str_replace('Team Registration Fee - ', '', $teamName);
                } elseif (strpos($teamName, 'Team Registration - ') !== false) {
                    $teamName = str_replace('Team Registration - ', '', $teamName);
                } elseif (strpos($teamName, 'Registration Fee - ') !== false) {
                    $teamName = str_replace('Registration Fee - ', '', $teamName);
                }
                $individualTeamFees[trim($teamName)] = $record->amount;
                $actualTeamTotal += $record->amount;
            }
            $teamBalance = $expectedTeamAmount - $actualTeamTotal;
        } elseif ($existingTeamRegistration) {
            // We have a combined record, try to extract individual team fees from description
            $description = $existingTeamRegistration->description;
            $actualTeamTotal = 0;
            
            // Look for pattern like "Team Name: ₹amount"
            if (preg_match_all('/([^:]+):\s*₹([0-9,]+\.?[0-9]*)/', $description, $matches)) {
                foreach ($matches[1] as $index => $teamName) {
                    $amount = (float) str_replace(',', '', $matches[2][$index]);
                    $individualTeamFees[trim($teamName)] = $amount;
                    $actualTeamTotal += $amount;
                }
                $teamBalance = $expectedTeamAmount - $actualTeamTotal;
            } else {
                // Fallback to original calculation
                $teamBalance = $expectedTeamAmount - $existingTeamRegistration->amount;
            }
        }

        $incomeCategories = ExpenseCategory::income()->active()->get();
        $expenseCategories = ExpenseCategory::expense()->active()->get();

        return view('league-finances.create', compact(
            'league', 
            'incomeCategories', 
            'expenseCategories', 
            'teamCount', 
            'totalPotentialPlayers',
            'existingPlayerRegistration',
            'existingTeamRegistration',
            'individualTeamRegistrations',
            'expectedPlayerAmount',
            'expectedTeamAmount',
            'playerBalance',
            'teamBalance',
            'individualTeamFees'
        ));
    }

    /**
     * Store a newly created finance record.
     */
    public function store(Request $request, League $league)
    {
        if (!$this->isLeagueOrganizer($league)) {
            abort(403, 'You are not authorized to access this league\'s finances.');
        }

        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->all();
        $data['league_id'] = $league->id;
        $data['user_id'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('league-finances', $filename, 'public');
            $data['attachment'] = $path;
        }

        LeagueFinance::create($data);

        return redirect()->route('league-finances.index', $league)
            ->with('success', 'Finance record created successfully!');
    }

    /**
     * Create quick income records for player/team registration fees.
     */
    public function quickIncome(Request $request, League $league)
    {
        if (!$this->isLeagueOrganizer($league)) {
            abort(403, 'You are not authorized to access this league\'s finances.');
        }

        $request->validate([
            'type' => 'required|in:player_registration,team_registration',
            'percentage' => 'nullable|integer|min:1|max:100',
            'team_fees' => 'nullable|array',
            'team_fees.*' => 'nullable|numeric|min:0',
        ]);

        $type = $request->type;
        $teamCount = $league->leagueTeams()->count();

        if ($type === 'player_registration') {
            $totalPlayers = $teamCount * $league->max_team_players;
            $percentage = $request->percentage ?? 100;
            $actualPlayers = round(($totalPlayers * $percentage) / 100);
            $amount = $actualPlayers * $league->player_reg_fee;
            $title = 'Player Registration Fees - ' . $league->name;
            $description = "Registration fees collected from {$actualPlayers} players ({$percentage}% of {$totalPlayers} total players) at ₹" . number_format($league->player_reg_fee, 2) . " per player.";
        } else {
            $teamFees = $request->team_fees ?? [];
            $totalAmount = 0;
            $teamDetails = [];
            
            foreach ($league->leagueTeams as $index => $leagueTeam) {
                $teamFee = $teamFees[$index] ?? $league->team_reg_fee;
                $totalAmount += $teamFee;
                $teamDetails[] = $leagueTeam->team->name . ': ₹' . number_format($teamFee, 2);
            }
            
            $amount = $totalAmount;
            $title = 'Team Registration Fees - ' . $league->name;
            $description = "Registration fees collected from {$teamCount} teams. " . implode(', ', $teamDetails);
        }

        // Get the appropriate income category
        $incomeCategory = ExpenseCategory::where('type', 'income')
            ->where('name', 'like', '%registration%')
            ->first();

        if (!$incomeCategory) {
            $incomeCategory = ExpenseCategory::where('type', 'income')->first();
        }

        // Check if a similar record already exists
        $existingRecord = LeagueFinance::where('league_id', $league->id)
            ->where('type', 'income')
            ->where('title', 'like', '%' . ($type === 'player_registration' ? 'Player Registration' : 'Team Registration') . '%')
            ->first();

        $data = [
            'league_id' => $league->id,
            'user_id' => Auth::id(),
            'expense_category_id' => $incomeCategory->id,
            'title' => $title,
            'description' => $description,
            'amount' => $amount,
            'type' => 'income',
            'transaction_date' => now(),
        ];

        if ($existingRecord) {
            // Check if the amount has changed
            if ($existingRecord->amount != $amount) {
                // Update the existing record
                $existingRecord->update($data);
                $message = ucfirst(str_replace('_', ' ', $type)) . ' income record updated successfully!';
            } else {
                // Amount is the same, no need to update
                $message = ucfirst(str_replace('_', ' ', $type)) . ' income record already exists with the same amount.';
            }
        } else {
            // Create new record
            LeagueFinance::create($data);
            $message = ucfirst(str_replace('_', ' ', $type)) . ' income record created successfully!';
        }

        return redirect()->route('league-finances.index', $league)
            ->with('success', $message);
    }

    /**
     * Display the specified finance record.
     */
    public function show(League $league, LeagueFinance $finance)
    {
        if (!$this->isLeagueOrganizer($league)) {
            abort(403, 'You are not authorized to access this league\'s finances.');
        }

        if ($finance->league_id !== $league->id) {
            abort(404);
        }

        return view('league-finances.show', compact('league', 'finance'));
    }

    /**
     * Show the form for editing the specified finance record.
     */
    public function edit(League $league, LeagueFinance $finance)
    {
        if (!$this->isLeagueOrganizer($league)) {
            abort(403, 'You are not authorized to access this league\'s finances.');
        }

        if ($finance->league_id !== $league->id) {
            abort(404);
        }

        $incomeCategories = ExpenseCategory::income()->active()->get();
        $expenseCategories = ExpenseCategory::expense()->active()->get();

        return view('league-finances.edit', compact('league', 'finance', 'incomeCategories', 'expenseCategories'));
    }

    /**
     * Update the specified finance record.
     */
    public function update(Request $request, League $league, LeagueFinance $finance)
    {
        if (!$this->isLeagueOrganizer($league)) {
            abort(403, 'You are not authorized to access this league\'s finances.');
        }

        if ($finance->league_id !== $league->id) {
            abort(404);
        }

        // Check if this is a simple amount update (from registration fee cards)
        if ($request->has('amount') && !$request->has('expense_category_id')) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'team_fees' => 'nullable|array',
                'team_fees.*' => 'nullable|numeric|min:0',
            ]);

            $newAmount = $request->amount;
            $oldAmount = $finance->amount;
            
            // Update the amount
            $finance->update(['amount' => $newAmount]);
            
            // Update description to reflect new amount
            if (str_contains($finance->title, 'Player Registration')) {
                $teamCount = $league->leagueTeams()->count();
                $totalPotentialPlayers = $teamCount * $league->max_team_players;
                $actualPlayers = round(($newAmount / $league->player_reg_fee));
                $percentage = round(($actualPlayers / $totalPotentialPlayers) * 100);
                
                $finance->update([
                    'description' => "Registration fees collected from {$actualPlayers} players ({$percentage}% of {$totalPotentialPlayers} total players) at ₹" . number_format($league->player_reg_fee, 2) . " per player."
                ]);
            } elseif (str_contains($finance->title, 'Team Registration')) {
                $teamCount = $league->leagueTeams()->count();
                
                // If individual team fees are provided, create separate income records for each team
                if ($request->has('team_fees') && is_array($request->team_fees)) {
                    $leagueTeams = $league->leagueTeams()->with('team')->get();
                    
                    // Get the appropriate income category
                    $incomeCategory = ExpenseCategory::where('type', 'income')
                        ->where('name', 'like', '%registration%')
                        ->first();
                    
                    if (!$incomeCategory) {
                        $incomeCategory = ExpenseCategory::where('type', 'income')->first();
                    }
                    
                    // Delete the old combined record
                    $finance->delete();
                    
                    // Create individual records for each team
                    foreach ($leagueTeams as $index => $leagueTeam) {
                        $teamFee = $request->team_fees[$index] ?? $league->team_reg_fee;
                        
                        LeagueFinance::create([
                            'league_id' => $league->id,
                            'user_id' => Auth::id(),
                            'expense_category_id' => $incomeCategory->id,
                            'title' => 'Team Registration Fee - ' . $leagueTeam->team->name,
                            'description' => "Registration fee collected from {$leagueTeam->team->name} for {$league->name}",
                            'amount' => $teamFee,
                            'type' => 'income',
                            'transaction_date' => now(),
                        ]);
                    }
                    
                    return redirect()->route('league-finances.create', $league)
                        ->with('success', 'Team registration fees updated as individual records!');
                } else {
                    // Fallback to simple description update
                    $finance->update([
                        'description' => "Registration fees collected from {$teamCount} teams. Total amount: ₹" . number_format($newAmount, 2)
                    ]);
                }
            }

            return redirect()->route('league-finances.create', $league)
                ->with('success', 'Registration fee updated successfully!');
        }

        // Full form update (from edit page)
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment
            if ($finance->attachment && Storage::disk('public')->exists($finance->attachment)) {
                Storage::disk('public')->delete($finance->attachment);
            }

            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('league-finances', $filename, 'public');
            $data['attachment'] = $path;
        }

        $finance->update($data);

        return redirect()->route('league-finances.index', $league)
            ->with('success', 'Finance record updated successfully!');
    }

    /**
     * Remove the specified finance record.
     */
    public function destroy(League $league, LeagueFinance $finance)
    {
        if (!$this->isLeagueOrganizer($league)) {
            abort(403, 'You are not authorized to access this league\'s finances.');
        }

        if ($finance->league_id !== $league->id) {
            abort(404);
        }

        // Delete attachment if exists
        if ($finance->attachment && Storage::disk('public')->exists($finance->attachment)) {
            Storage::disk('public')->delete($finance->attachment);
        }

        $finance->delete();

        return redirect()->route('league-finances.index', $league)
            ->with('success', 'Finance record deleted successfully!');
    }

    /**
     * Generate PDF report for league finances.
     */
    public function report(League $league, Request $request)
    {
        if (!$this->isLeagueOrganizer($league)) {
            abort(403, 'You are not authorized to access this league\'s finances.');
        }

        $startDate = $request->get('start_date', $league->start_date);
        $endDate = $request->get('end_date', $league->end_date);

        $finances = $league->finances()
            ->with(['expenseCategory', 'user'])
            ->dateRange($startDate, $endDate)
            ->orderBy('transaction_date', 'desc')
            ->get();

        $totalIncome = $finances->where('type', 'income')->sum('amount');
        $totalExpenses = $finances->where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        $pdf = Pdf::loadView('league-finances.report', compact(
            'league',
            'finances',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'startDate',
            'endDate'
        ));

        return $pdf->download("league-finances-{$league->slug}-{$startDate}-to-{$endDate}.pdf");
    }

    /**
     * Check if the current user is the organizer of the league.
     */
    private function isLeagueOrganizer(League $league): bool
    {
        // Check if user is admin
        if (Auth::user()->isAdmin()) {
            return true;
        }

        // Check if user is organizer of this specific league
        return $league->organizers()->where('user_id', Auth::id())->exists();
    }
}