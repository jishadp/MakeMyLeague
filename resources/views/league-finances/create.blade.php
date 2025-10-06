@extends('layouts.app')

@section('title', 'Add Transaction - ' . $league->name)

@section('content')
<section class="min-h-screen bg-white py-4 sm:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Add Transaction</h1>
                    <p class="text-sm sm:text-base text-gray-600">{{ $league->name }}</p>
                </div>
                <a href="{{ route('league-finances.index', $league) }}"
                   class="w-full sm:w-auto bg-gray-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-gray-700 transition-colors text-sm sm:text-base flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Finances
                </a>
            </div>
        </div>

        <!-- Cash Prize Cards Section -->
        @if($league->winner_prize || $league->runner_prize)
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 border border-gray-100 mb-6 sm:mb-8">
            <div class="flex items-center mb-4">
                <div class="p-2 sm:p-3 bg-yellow-100 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Cash Prizes</h3>
                    <p class="text-xs sm:text-sm text-gray-600">Add cash prizes as expense records</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($league->winner_prize)
                    @php
                        $existingWinnerExpense = \App\Models\LeagueFinance::where('league_id', $league->id)
                            ->where('type', 'expense')
                            ->where('title', 'like', '%Winner Prize%')
                            ->first();
                    @endphp
                    @if(!$existingWinnerExpense)
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-4 border border-yellow-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Winner Prize</h4>
                                    <p class="text-xs text-gray-600">Cash prize for winning team</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-yellow-600">₹{{ number_format($league->winner_prize, 2) }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('league-finances.store', $league) }}" class="w-full">
                            @csrf
                            <input type="hidden" name="type" value="expense">
                            <input type="hidden" name="expense_category_id" value="{{ $expenseCategories->where('name', 'Trophies and Awards')->first()->id ?? $expenseCategories->first()->id }}">
                            <input type="hidden" name="title" value="Winner Prize - {{ $league->name }}">
                            <input type="hidden" name="description" value="Cash prize for the winning team">
                            <input type="hidden" name="amount" value="{{ $league->winner_prize }}">
                            <input type="hidden" name="transaction_date" value="{{ date('Y-m-d') }}">
                            <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors text-sm font-medium flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add as Expense
                            </button>
                        </form>
                    </div>
                    @endif
                @endif

                @if($league->runner_prize)
                    @php
                        $existingRunnerExpense = \App\Models\LeagueFinance::where('league_id', $league->id)
                            ->where('type', 'expense')
                            ->where('title', 'like', '%Runner-up Prize%')
                            ->first();
                    @endphp
                    @if(!$existingRunnerExpense)
                    <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="p-2 bg-gray-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Runner-up Prize</h4>
                                    <p class="text-xs text-gray-600">Cash prize for runner-up team</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-600">₹{{ number_format($league->runner_prize, 2) }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('league-finances.store', $league) }}" class="w-full">
                            @csrf
                            <input type="hidden" name="type" value="expense">
                            <input type="hidden" name="expense_category_id" value="{{ $expenseCategories->where('name', 'Trophies and Awards')->first()->id ?? $expenseCategories->first()->id }}">
                            <input type="hidden" name="title" value="Runner-up Prize - {{ $league->name }}">
                            <input type="hidden" name="description" value="Cash prize for the runner-up team">
                            <input type="hidden" name="amount" value="{{ $league->runner_prize }}">
                            <input type="hidden" name="transaction_date" value="{{ date('Y-m-d') }}">
                            <button type="submit" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add as Expense
                            </button>
                        </form>
                    </div>
                    @endif
                @endif
            </div>
        </div>
        @endif

        <!-- Easy Cards Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Player Registration Fee Card -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="p-2 sm:p-3 bg-blue-100 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900">Player Registration Fee</h3>
                        <p class="text-xs sm:text-sm text-gray-600">Total collected from player registrations</p>
                    </div>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total Players</p>
                            <p class="text-base sm:text-lg font-bold text-gray-900">{{ $totalPotentialPlayers }}</p>
                            <p class="text-xs text-gray-500">{{ $teamCount }} teams × {{ $league->max_team_players }} players</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Registration Fee</p>
                            <p class="text-base sm:text-lg font-bold text-gray-900">₹{{ number_format($league->player_reg_fee, 2) }}</p>
                        </div>
                    </div>

                    @if($existingPlayerRegistration)
                        <!-- Existing Record Info -->
                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs font-medium text-blue-800">Already Recorded</p>
                                </div>
                                @if(abs($playerBalance) > 0.01)
                                    <button type="button" onclick="showPlayerUpdateModal()" class="text-xs text-orange-600 hover:text-orange-800 font-medium">
                                        Update Balance
                                    </button>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-blue-700">Amount Collected:</p>
                                    <p class="text-sm font-bold text-blue-800">₹{{ number_format($existingPlayerRegistration->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-700">Date Recorded:</p>
                                    <p class="text-sm font-bold text-blue-800">{{ $existingPlayerRegistration->transaction_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @if(abs($playerBalance) > 0.01)
                                <div class="mt-2 p-2 bg-orange-50 rounded border border-orange-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-orange-700">Balance:</span>
                                        <span class="text-sm font-bold {{ $playerBalance > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $playerBalance > 0 ? '+' : '' }}₹{{ number_format($playerBalance, 2) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-orange-600 mt-1">
                                        Expected: ₹{{ number_format($expectedPlayerAmount, 2) }} |
                                        Recorded: ₹{{ number_format($existingPlayerRegistration->amount, 2) }}
                                    </p>
                                </div>
                            @endif
                            <p class="text-xs text-blue-600 mt-2">{{ $existingPlayerRegistration->description }}</p>
                        </div>
                    @else
                        <!-- Percentage Toggle -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">Collection Percentage</p>
                            <div class="flex items-center space-x-3">
                                <input type="range" id="playerPercentage" min="1" max="100" value="100"
                                       class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                <div class="flex items-center space-x-2">
                                    <input type="number" id="playerPercentageInput" min="1" max="100" value="100"
                                           class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <span class="text-sm font-medium text-gray-700">%</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mt-3">
                                <div class="bg-white rounded p-2 text-center">
                                    <p class="text-xs text-gray-500">Players Paid</p>
                                    <p class="text-lg font-bold text-blue-600" id="actualPlayersCount">{{ $totalPotentialPlayers }}</p>
                                </div>
                                <div class="bg-white rounded p-2 text-center">
                                    <p class="text-xs text-gray-500">Total Players</p>
                                    <p class="text-lg font-bold text-gray-700">{{ $totalPotentialPlayers }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3 sm:p-4 border border-blue-200">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
                            <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-600">Total Amount to Collect</p>
                                <p class="text-lg sm:text-xl lg:text-2xl font-bold text-blue-600" id="playerTotalAmount">
                                    ₹{{ number_format($existingPlayerRegistration ? $existingPlayerRegistration->amount : $expectedPlayerAmount, 2) }}
                                </p>
                            </div>
                            <div class="text-left sm:text-right">
                                <p class="text-xs sm:text-sm font-medium text-gray-600">Collection Rate</p>
                                <p class="text-sm sm:text-lg font-bold text-green-600" id="playerCollectionRate">
                                    {{ $existingPlayerRegistration ? '100%' : '100%' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('league-finances.quick-income', $league) }}" class="w-full">
                        @csrf
                        <input type="hidden" name="type" value="player_registration">
                        <input type="hidden" name="percentage" id="playerPercentageHidden" value="100">
                        <button type="submit"
                                class="w-full {{ $existingPlayerRegistration ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700' }} text-white px-4 py-2 sm:py-2.5 rounded-lg transition-colors text-sm sm:text-base flex items-center justify-center"
                                {{ $existingPlayerRegistration ? 'disabled' : '' }}>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            {{ $existingPlayerRegistration ? 'Already Recorded' : 'Add Player Registration Income' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Team Registration Fee Card -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="p-2 sm:p-3 bg-green-100 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900">Team Registration Fee</h3>
                        <p class="text-xs sm:text-sm text-gray-600">Individual team payment tracking</p>
                    </div>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total Teams</p>
                            <p class="text-base sm:text-lg font-bold text-gray-900">{{ $teamCount }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Registration Fee</p>
                            <p class="text-base sm:text-lg font-bold text-gray-900">₹{{ number_format($league->team_reg_fee, 2) }}</p>
                        </div>
                    </div>

                    @if($teamCount > 0)
                        <!-- Simple Team Registration Form -->
                        <form method="POST" action="{{ route('league-finances.individual-team-income', $league) }}" class="w-full" id="teamPaymentForm">
                            @csrf

                            <!-- Team Selection -->
                            <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                <label for="teamSelect" class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">Select Team</label>
                                <select name="team_id" id="teamSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" required onchange="updateTeamPaymentInfo()">
                                    <option value="">Choose a team...</option>
                                    @foreach($league->leagueTeams as $leagueTeam)
                                        @php
                                            // Calculate total paid amount for this team
                                            $totalPaid = \App\Models\LeagueFinance::where('league_id', $league->id)
                                                ->where('type', 'income')
                                                ->where('title', 'like', '%Team Registration Fee - ' . $leagueTeam->team->name . '%')
                                                ->sum('amount');
                                            $balance = $league->team_reg_fee - $totalPaid;
                                        @endphp
                                        <option value="{{ $leagueTeam->id }}" 
                                                data-team-name="{{ $leagueTeam->team->name }}"
                                                data-paid="{{ $totalPaid }}"
                                                data-balance="{{ $balance }}"
                                                data-status="{{ $balance <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'pending') }}">
                                            {{ $leagueTeam->team->name }}
                                            @if($balance <= 0)
                                                (Paid)
                                            @elseif($totalPaid > 0)
                                                (₹{{ number_format($totalPaid, 2) }} paid)
                                            @else
                                                (Pending)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Payment Status Display -->
                            <div id="paymentStatus" class="hidden mb-3">
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-bold text-gray-900" id="selectedTeamName"></h4>
                                        <span id="paymentStatusBadge" class="px-2 py-1 rounded-full text-xs font-medium"></span>
                                    </div>

                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="text-center">
                                            <p class="text-xs text-gray-500">Expected</p>
                                            <p class="text-sm font-bold text-gray-900">₹{{ number_format($league->team_reg_fee, 2) }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-gray-500">Paid</p>
                                            <p class="text-sm font-bold text-green-600" id="paidAmount">₹0.00</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-gray-500">Balance</p>
                                            <p class="text-sm font-bold" id="balanceAmount">₹0.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount Input Section -->
                            <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                <label for="amount" class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">Amount to Record</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">₹</span>
                                    <input type="number"
                                           name="amount"
                                           id="amountInput"
                                           step="0.01"
                                           min="0.01"
                                           max="{{ $league->team_reg_fee }}"
                                           value="{{ $league->team_reg_fee }}"
                                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="Enter amount"
                                           required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Maximum: ₹{{ number_format($league->team_reg_fee, 2) }}</p>
                            </div>

                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-3 sm:p-4 border border-green-200 mb-3">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
                                    <div>
                                        <p class="text-xs sm:text-sm font-medium text-gray-600">Amount to Collect</p>
                                        <p class="text-lg sm:text-xl lg:text-2xl font-bold text-green-600" id="amountToCollect">
                                            ₹{{ number_format($league->team_reg_fee, 2) }}
                                        </p>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <p class="text-xs sm:text-sm font-medium text-gray-600">Status</p>
                                        <p class="text-sm sm:text-lg font-bold text-gray-600" id="collectionStatus">Select Team</p>
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                    id="submitBtn"
                                    class="w-full bg-gray-400 cursor-not-allowed text-white px-4 py-2 sm:py-2.5 rounded-lg transition-colors text-sm sm:text-base flex items-center justify-center"
                                    disabled>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span id="submitText">Select Team First</span>
                            </button>
                        </form>
                    @else
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">Teams Registered</p>
                            <p class="text-xs text-gray-500 italic">No teams registered yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Expense Card -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 border border-gray-100 mb-6 sm:mb-8">
            <div class="flex items-center mb-4">
                <div class="p-2 sm:p-3 bg-red-100 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Expense</h3>
                    <p class="text-xs sm:text-sm text-gray-600">Record a single expense transaction</p>
                </div>
            </div>

            <form method="POST" action="{{ route('league-finances.store', $league) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="expense">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                        <select name="expense_category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                            <option value="">Select expense category</option>
                            @foreach($expenseCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (₹)</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                               placeholder="0.00">
                    </div>

                    <!-- Title -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                               placeholder="Enter expense title">
                    </div>

                    <!-- Description -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                                  placeholder="Enter expense description (optional)"></textarea>
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Reference Number</label>
                        <input type="text" name="reference_number"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                               placeholder="Invoice/receipt number (optional)">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Expense
                    </button>
                </div>
            </form>
        </div>

        <!-- Income Card -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 border border-gray-100 mb-6 sm:mb-8">
            <div class="flex items-center mb-4">
                <div class="p-2 sm:p-3 bg-green-100 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Income</h3>
                    <p class="text-xs sm:text-sm text-gray-600">Record a single income transaction</p>
                </div>
            </div>

            <form method="POST" action="{{ route('league-finances.store', $league) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="income">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                        <select name="expense_category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                            <option value="">Select income category</option>
                            @foreach($incomeCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (₹)</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                               placeholder="0.00">
                    </div>

                    <!-- Title -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                               placeholder="Enter income title">
                    </div>

                    <!-- Description -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                  placeholder="Enter income description (optional)"></textarea>
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Reference Number</label>
                        <input type="text" name="reference_number"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                               placeholder="Invoice/receipt number (optional)">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Income
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Player Update Modal -->
@if($existingPlayerRegistration)
<div id="playerUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Update Player Registration</h3>
                    <button onclick="hidePlayerUpdateModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Current Balance: <span class="font-bold {{ $playerBalance > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $playerBalance > 0 ? '+' : '' }}₹{{ number_format($playerBalance, 2) }}</span></p>
                    <p class="text-xs text-gray-500">Expected: ₹{{ number_format($expectedPlayerAmount, 2) }} | Recorded: ₹{{ number_format($existingPlayerRegistration->amount, 2) }}</p>
                </div>

                <form method="POST" action="{{ route('league-finances.update', [$league, $existingPlayerRegistration]) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Amount</label>
                        <input type="number" name="amount" step="0.01" min="0"
                               value="{{ $expectedPlayerAmount }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hidePlayerUpdateModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                            Update Amount
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Player Registration Card: Percentage Slider ---
    @if(!$existingPlayerRegistration)
    const playerPercentageSlider = document.getElementById('playerPercentage');
    const playerPercentageInput = document.getElementById('playerPercentageInput');
    const playerPercentageHidden = document.getElementById('playerPercentageHidden');
    const playerTotalAmount = document.getElementById('playerTotalAmount');
    const playerCollectionRate = document.getElementById('playerCollectionRate');
    const actualPlayersCount = document.getElementById('actualPlayersCount');
    
    const totalPlayers = {{ $totalPotentialPlayers }};
    const playerRegFee = {{ $league->player_reg_fee }};
    
    function updatePlayerAmount() {
        const percentage = parseInt(playerPercentageInput.value);
        const actualPlayers = Math.round((totalPlayers * percentage) / 100);
        const totalAmount = actualPlayers * playerRegFee;
        
        playerTotalAmount.textContent = '₹' + totalAmount.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        playerCollectionRate.textContent = percentage + '%';
        playerPercentageHidden.value = percentage;
        actualPlayersCount.textContent = actualPlayers;
    }
    
    playerPercentageSlider.addEventListener('input', function() {
        playerPercentageInput.value = this.value;
        updatePlayerAmount();
    });
    
    playerPercentageInput.addEventListener('input', function() {
        let value = parseInt(this.value) || 0;
        value = Math.max(1, Math.min(100, value));
        this.value = value;
        playerPercentageSlider.value = value;
        updatePlayerAmount();
    });
    @endif

    // --- Team Registration Card: Simple Logic ---
    // This function is called when team selection changes
    window.updateTeamPaymentInfo = function() {
        const teamSelect = document.getElementById('teamSelect');
        const paymentStatus = document.getElementById('paymentStatus');
        const selectedTeamName = document.getElementById('selectedTeamName');
        const paymentStatusBadge = document.getElementById('paymentStatusBadge');
        const paidAmount = document.getElementById('paidAmount');
        const balanceAmount = document.getElementById('balanceAmount');
        const amountInput = document.getElementById('amountInput');
        const amountToCollect = document.getElementById('amountToCollect');
        const collectionStatus = document.getElementById('collectionStatus');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');

        if (!teamSelect || teamSelect.value === '') {
            // No team selected
            paymentStatus.classList.add('hidden');
            collectionStatus.textContent = 'Select Team';
            submitBtn.disabled = true;
            submitBtn.className = 'w-full bg-gray-400 cursor-not-allowed text-white px-4 py-2 sm:py-2.5 rounded-lg transition-colors text-sm sm:text-base flex items-center justify-center';
            submitText.textContent = 'Select Team First';
            return;
        }

        // Get selected team data
        const selectedOption = teamSelect.options[teamSelect.selectedIndex];
        const teamName = selectedOption.dataset.teamName;
        const paid = parseFloat(selectedOption.dataset.paid || 0);
        const balance = parseFloat(selectedOption.dataset.balance || 0);
        const status = selectedOption.dataset.status;

        // Update display
        selectedTeamName.textContent = teamName;
        paidAmount.textContent = `₹${paid.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        balanceAmount.textContent = `₹${balance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        balanceAmount.className = 'text-sm font-bold ' + (balance > 0 ? 'text-red-600' : 'text-green-600');

        // Update status badge
        if (status === 'paid') {
            paymentStatusBadge.textContent = 'Paid';
            paymentStatusBadge.className = 'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
            collectionStatus.textContent = 'Fully Paid';
        } else if (status === 'partial') {
            paymentStatusBadge.textContent = 'Partial';
            paymentStatusBadge.className = 'px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
            collectionStatus.textContent = 'Partially Paid';
        } else {
            paymentStatusBadge.textContent = 'Pending';
            paymentStatusBadge.className = 'px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
            collectionStatus.textContent = 'Payment Pending';
        }

        // Update amount input and collection display
        if (balance > 0) {
            amountInput.value = balance.toFixed(2);
            amountInput.max = balance;
            amountToCollect.textContent = `₹${balance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        } else {
            amountInput.value = '0.00';
            amountInput.max = '0';
            amountToCollect.textContent = '₹0.00';
        }

        // Show payment status and update button
        paymentStatus.classList.remove('hidden');
        
        if (status === 'paid') {
            submitBtn.disabled = true;
            submitBtn.className = 'w-full bg-gray-500 cursor-not-allowed text-white px-4 py-2 sm:py-2.5 rounded-lg transition-colors text-sm sm:text-base flex items-center justify-center';
            submitText.textContent = 'Fully Paid';
        } else {
            submitBtn.disabled = false;
            submitBtn.className = 'w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 sm:py-2.5 rounded-lg transition-colors text-sm sm:text-base flex items-center justify-center';
            submitText.textContent = 'Record Payment';
        }
    };
});

// --- Modal Functions (No changes needed here) ---
function showPlayerUpdateModal() {
    const modal = document.getElementById('playerUpdateModal');
    if (modal) modal.classList.remove('hidden');
}

function hidePlayerUpdateModal() {
    const modal = document.getElementById('playerUpdateModal');
    if (modal) modal.classList.add('hidden');
}

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        hidePlayerUpdateModal();
    }
});

const playerModal = document.getElementById('playerUpdateModal');
if (playerModal) {
    playerModal.addEventListener('click', function(e) {
        if (e.target === this) {
            hidePlayerUpdateModal();
        }
    });
}
</script>
@endsection