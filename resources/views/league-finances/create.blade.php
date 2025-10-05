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
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs font-medium text-blue-800">Already Recorded</p>
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
                                
                                <!-- Update Percentage Toggle -->
                                <div class="mt-3 bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">Update Collection Percentage</p>
                                    <div class="flex items-center space-x-3">
                                        <input type="range" id="playerUpdatePercentage" min="1" max="100" 
                                               value="{{ round(($existingPlayerRegistration->amount / $expectedPlayerAmount) * 100) }}" 
                                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                        <div class="flex items-center space-x-2">
                                            <input type="number" id="playerUpdatePercentageInput" min="1" max="100" 
                                                   value="{{ round(($existingPlayerRegistration->amount / $expectedPlayerAmount) * 100) }}" 
                                                   class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <span class="text-sm font-medium text-gray-700">%</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 mt-3">
                                        <div class="bg-white rounded p-2 text-center">
                                            <p class="text-xs text-gray-500">Players Paid</p>
                                            <p class="text-lg font-bold text-blue-600" id="playerUpdateActualCount">{{ round(($existingPlayerRegistration->amount / $league->player_reg_fee)) }}</p>
                                        </div>
                                        <div class="bg-white rounded p-2 text-center">
                                            <p class="text-xs text-gray-500">Total Players</p>
                                            <p class="text-lg font-bold text-gray-700">{{ $totalPotentialPlayers }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Update Button -->
                                    <div class="mt-3 hidden" id="playerUpdateButtonContainer">
                                        <form method="POST" action="{{ route('league-finances.update', [$league, $existingPlayerRegistration]) }}" class="w-full">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="amount" id="playerUpdateAmountHidden" value="{{ $existingPlayerRegistration->amount }}">
                                            <button type="submit" 
                                                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Update Player Registration
                                            </button>
                                        </form>
                                    </div>
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
                        <p class="text-xs sm:text-sm text-gray-600">Total collected from team registrations</p>
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
                    
                    {{-- Debug: Show what we found --}}
                    <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200 mb-3">
                        <p class="text-xs font-medium text-yellow-800">Debug Info:</p>
                        <p class="text-xs text-yellow-700">Individual Records Count: {{ $individualTeamRegistrations->count() }}</p>
                        <p class="text-xs text-yellow-700">Existing Combined Record: {{ $existingTeamRegistration ? 'Yes' : 'No' }}</p>
                        @if($individualTeamRegistrations->count() > 0)
                            <p class="text-xs text-yellow-700">Individual Records:</p>
                            @foreach($individualTeamRegistrations as $record)
                                <p class="text-xs text-yellow-600">- {{ $record->title }}: ₹{{ $record->amount }}</p>
                            @endforeach
                        @endif
                    </div>
                    
                    @if($individualTeamRegistrations->count() > 0)
                        <!-- Individual Team Records Info -->
                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs font-medium text-green-800">Individual Team Records</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-green-700">Total Collected:</p>
                                    <p class="text-sm font-bold text-green-800">₹{{ number_format($individualTeamRegistrations->sum('amount'), 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-green-700">Records:</p>
                                    <p class="text-sm font-bold text-green-800">{{ $individualTeamRegistrations->count() }} teams</p>
                                </div>
                            </div>
                            @if(abs($teamBalance) > 0.01)
                                <div class="mt-2 p-2 bg-orange-50 rounded border border-orange-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-orange-700">Balance:</span>
                                        <span class="text-sm font-bold {{ $teamBalance > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $teamBalance > 0 ? '+' : '' }}₹{{ number_format($teamBalance, 2) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-orange-600 mt-1">
                                        Expected: ₹{{ number_format($expectedTeamAmount, 2) }} | 
                                        Collected: ₹{{ number_format($individualTeamRegistrations->sum('amount'), 2) }}
                                    </p>
                                </div>
                                
                                <!-- Simple Team Amount Selection -->
                                <div class="mt-3 bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-3">Select Team Amount Paid</p>
                                    
                                    <!-- Team Selection Dropdown -->
                                    <div class="mb-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Select Team:</label>
                                        <select id="teamSelector" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                            <option value="">Choose a team...</option>
                                            @foreach($league->leagueTeams as $index => $leagueTeam)
                                                @php
                                                    $currentTeamFee = isset($individualTeamFees[$leagueTeam->team->name]) ? $individualTeamFees[$leagueTeam->team->name] : $league->team_reg_fee;
                                                @endphp
                                                <option value="{{ $index }}" data-team-name="{{ $leagueTeam->team->name }}" data-current-fee="{{ $currentTeamFee }}">
                                                    {{ $leagueTeam->team->name }} - Currently: ₹{{ number_format($currentTeamFee, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Amount Input -->
                                    <div class="mb-3" id="amountInputContainer" style="display: none;">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Amount Paid:</label>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-500">₹</span>
                                            <input type="number" id="teamAmountInput" step="0.01" min="0" 
                                                   class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                        </div>
                                    </div>
                                    
                                    <!-- Balance Display -->
                                    <div class="mb-3" id="balanceContainer" style="display: none;">
                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <div class="grid grid-cols-2 gap-3 text-xs">
                                                <div>
                                                    <p class="text-gray-500">Expected Amount:</p>
                                                    <p class="font-bold text-gray-900" id="expectedAmount">₹0.00</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-500">Balance to Pay:</p>
                                                    <p class="font-bold text-green-600" id="balanceAmount">₹0.00</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Update Button -->
                                    <div id="updateButtonContainer" style="display: none;">
                                        <form method="POST" action="{{ route('league-finances.update', [$league, $individualTeamRegistrations->first()]) }}" class="w-full">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="amount" id="teamUpdateAmountHidden" value="{{ $individualTeamRegistrations->sum('amount') }}">
                                            <div id="teamUpdateFeesData"></div>
                                            <button type="submit" 
                                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Update Team Registration
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif($existingTeamRegistration)
                        <!-- Existing Record Info -->
                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs font-medium text-green-800">Already Recorded</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-green-700">Amount Collected:</p>
                                    <p class="text-sm font-bold text-green-800">₹{{ number_format($existingTeamRegistration->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-green-700">Date Recorded:</p>
                                    <p class="text-sm font-bold text-green-800">{{ $existingTeamRegistration->transaction_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @if(abs($teamBalance) > 0.01)
                                <div class="mt-2 p-2 bg-orange-50 rounded border border-orange-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-orange-700">Balance:</span>
                                        <span class="text-sm font-bold {{ $teamBalance > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $teamBalance > 0 ? '+' : '' }}₹{{ number_format($teamBalance, 2) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-orange-600 mt-1">
                                        Expected: ₹{{ number_format($expectedTeamAmount, 2) }} | 
                                        Recorded: ₹{{ number_format($existingTeamRegistration->amount, 2) }}
                                    </p>
                                </div>
                                
                                <!-- Update Teams List -->
                                <div class="mt-3 bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">Update Team Fees</p>
                                    <div class="max-h-40 overflow-y-auto space-y-2" id="teamUpdateList">
                                        @foreach($league->leagueTeams as $index => $leagueTeam)
                                            @php
                                                $currentTeamFee = isset($individualTeamFees[$leagueTeam->team->name]) ? $individualTeamFees[$leagueTeam->team->name] : $league->team_reg_fee;
                                            @endphp
                                            <div class="bg-white rounded p-2 border border-gray-200">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="font-medium text-gray-900 text-xs">{{ $leagueTeam->team->name }}</span>
                                                    <button type="button" class="team-update-override-toggle text-xs text-blue-600 hover:text-blue-800 font-medium" 
                                                            data-team-index="{{ $index }}">
                                                        Override
                                                    </button>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-gray-500">Registration Fee:</span>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-green-600 font-bold text-xs team-update-fee-display" 
                                                              data-team-index="{{ $index }}">₹{{ number_format($currentTeamFee, 2) }}</span>
                                                        <input type="number" class="team-update-fee-input hidden w-20 px-1 py-0.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-green-500 focus:border-green-500" 
                                                               step="0.01" min="0" value="{{ $currentTeamFee }}" 
                                                               data-team-index="{{ $index }}" data-default-fee="{{ $league->team_reg_fee }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Update Button -->
                                    <div class="mt-3 hidden" id="teamUpdateButtonContainer">
                                        <form method="POST" action="{{ route('league-finances.update', [$league, $existingTeamRegistration]) }}" class="w-full">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="amount" id="teamUpdateAmountHidden" value="{{ $existingTeamRegistration->amount }}">
                                            <div id="teamUpdateFeesData"></div>
                                            <button type="submit" 
                                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Update Team Registration
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                            <p class="text-xs text-green-600 mt-2">{{ $existingTeamRegistration->description }}</p>
                        </div>
                    @else
                        <!-- Teams List -->
                        @if($teamCount > 0)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">Teams Registered</p>
                            <div class="max-h-40 overflow-y-auto space-y-2" id="teamsList">
                                @foreach($league->leagueTeams as $index => $leagueTeam)
                                    <div class="bg-white rounded p-2 border border-gray-200">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-medium text-gray-900 text-xs">{{ $leagueTeam->team->name }}</span>
                                            <button type="button" class="team-override-toggle text-xs text-blue-600 hover:text-blue-800 font-medium" 
                                                    data-team-index="{{ $index }}">
                                                Override
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">Registration Fee:</span>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-green-600 font-bold text-xs team-fee-display" 
                                                      data-team-index="{{ $index }}">₹{{ number_format($league->team_reg_fee, 2) }}</span>
                                                <input type="number" class="team-fee-input hidden w-20 px-1 py-0.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-green-500 focus:border-green-500" 
                                                       step="0.01" min="0" value="{{ $league->team_reg_fee }}" 
                                                       data-team-index="{{ $index }}" data-default-fee="{{ $league->team_reg_fee }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">Teams Registered</p>
                            <p class="text-xs text-gray-500 italic">No teams registered yet</p>
                        </div>
                        @endif
                    @endif
                    
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-3 sm:p-4 border border-green-200">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
                            <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-600">Total Amount to Collect</p>
                                <p class="text-lg sm:text-xl lg:text-2xl font-bold text-green-600" id="teamTotalAmount">
                                    ₹{{ number_format($existingTeamRegistration ? $existingTeamRegistration->amount : $expectedTeamAmount, 2) }}
                                </p>
                            </div>
                            <div class="text-left sm:text-right">
                                <p class="text-xs sm:text-sm font-medium text-gray-600">Collection Rate</p>
                                <p class="text-sm sm:text-lg font-bold text-green-600">100%</p>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('league-finances.quick-income', $league) }}" class="w-full">
                        @csrf
                        <input type="hidden" name="type" value="team_registration">
                        <div id="teamFeesData"></div>
                        <button type="submit" 
                                class="w-full {{ $existingTeamRegistration ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 sm:py-2.5 rounded-lg transition-colors text-sm sm:text-base flex items-center justify-center"
                                {{ $existingTeamRegistration ? 'disabled' : '' }}>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            {{ $existingTeamRegistration ? 'Already Recorded' : 'Add Team Registration Income' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 lg:p-8">
            <form method="POST" action="{{ route('league-finances.store', $league) }}" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Transaction Type -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Transaction Type</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="type" value="income" 
                                       class="mr-2 text-green-600 focus:ring-green-500" 
                                       {{ old('type') === 'income' ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700">Income</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="type" value="expense" 
                                       class="mr-2 text-red-600 focus:ring-red-500" 
                                       {{ old('type') === 'expense' ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700">Expense</span>
                            </label>
                        </div>
                        @error('type')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Category</label>
                        <select name="expense_category_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select a category</option>
                            @foreach($incomeCategories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                            @foreach($expenseCategories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('expense_category_id')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Amount (₹)</label>
                        <input type="number" name="amount" step="0.01" min="0.01" 
                               value="{{ old('amount') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="0.00">
                        @error('amount')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Title</label>
                        <input type="text" name="title" 
                               value="{{ old('title') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Enter transaction title">
                        @error('title')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Description</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                  placeholder="Enter transaction description (optional)">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Transaction Date</label>
                        <input type="date" name="transaction_date" 
                               value="{{ old('transaction_date', date('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('transaction_date')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Reference Number</label>
                        <input type="text" name="reference_number" 
                               value="{{ old('reference_number') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Invoice number, receipt number, etc. (optional)">
                        @error('reference_number')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachment -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Attachment</label>
                        <input type="file" name="attachment" 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <p class="text-sm text-gray-500 mt-2">Upload receipt, invoice, or other supporting documents (PDF, JPG, PNG - Max 10MB)</p>
                        @error('attachment')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <button type="submit" 
                            class="w-full sm:w-auto bg-green-600 text-white px-6 sm:px-8 py-3 rounded-lg hover:bg-green-700 transition-colors text-sm sm:text-base font-medium flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Add Transaction
                    </button>
                    <a href="{{ route('league-finances.index', $league) }}" 
                       class="w-full sm:w-auto bg-gray-600 text-white px-6 sm:px-8 py-3 rounded-lg hover:bg-gray-700 transition-colors text-sm sm:text-base font-medium text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const categorySelect = document.querySelector('select[name="expense_category_id"]');
    
    function updateCategories() {
        const selectedType = document.querySelector('input[name="type"]:checked')?.value;
        const options = categorySelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') return; // Keep the default option
            
            const categoryText = option.textContent;
            const isIncomeCategory = @json($incomeCategories->pluck('name')->toArray()).includes(categoryText);
            const isExpenseCategory = @json($expenseCategories->pluck('name')->toArray()).includes(categoryText);
            
            if (selectedType === 'income' && isIncomeCategory) {
                option.style.display = 'block';
            } else if (selectedType === 'expense' && isExpenseCategory) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Reset selection if current selection is not valid for the type
        if (categorySelect.value && !categorySelect.querySelector(`option[value="${categorySelect.value}"]`).style.display !== 'none') {
            categorySelect.value = '';
        }
    }
    
    typeRadios.forEach(radio => {
        radio.addEventListener('change', updateCategories);
    });
    
    // Initial update
    updateCategories();
    
    // Player percentage toggle functionality (only if no existing record)
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
        const percentage = parseInt(playerPercentageSlider.value);
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
        const value = Math.min(100, Math.max(1, parseInt(this.value) || 1));
        this.value = value;
        playerPercentageSlider.value = value;
        updatePlayerAmount();
    });
    @endif
    
    // Individual team override functionality (only if no existing record)
    @if(!$existingTeamRegistration)
    const teamTotalAmount = document.querySelector('.bg-gradient-to-r.from-green-50 .text-lg');
    const teamCount = {{ $teamCount }};
    const defaultTeamRegFee = {{ $league->team_reg_fee }};
    const teamFeesData = document.getElementById('teamFeesData');
    
    // Initialize team fees data
    function initializeTeamFees() {
        teamFeesData.innerHTML = '';
        for (let i = 0; i < teamCount; i++) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `team_fees[${i}]`;
            input.value = defaultTeamRegFee;
            teamFeesData.appendChild(input);
        }
        updateTeamTotal();
    }
    
    // Handle individual team override toggles
    document.querySelectorAll('.team-override-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const teamIndex = this.dataset.teamIndex;
            const feeDisplay = document.querySelector(`.team-fee-display[data-team-index="${teamIndex}"]`);
            const feeInput = document.querySelector(`.team-fee-input[data-team-index="${teamIndex}"]`);
            
            if (feeInput.classList.contains('hidden')) {
                // Show input
                feeDisplay.classList.add('hidden');
                feeInput.classList.remove('hidden');
                feeInput.focus();
                this.textContent = 'Save';
                this.classList.add('text-red-600');
                this.classList.remove('text-blue-600');
            } else {
                // Save and hide input
                const newFee = parseFloat(feeInput.value) || 0;
                feeDisplay.textContent = '₹' + newFee.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                feeDisplay.classList.remove('hidden');
                feeInput.classList.add('hidden');
                this.textContent = 'Override';
                this.classList.remove('text-red-600');
                this.classList.add('text-blue-600');
                
                // Update hidden field
                const hiddenInput = teamFeesData.querySelector(`input[name="team_fees[${teamIndex}]"]`);
                if (hiddenInput) {
                    hiddenInput.value = newFee;
                }
                updateTeamTotal();
            }
        });
    });
    
    // Handle team fee input changes
    document.querySelectorAll('.team-fee-input').forEach(input => {
        input.addEventListener('input', function() {
            const teamIndex = this.dataset.teamIndex;
            const hiddenInput = teamFeesData.querySelector(`input[name="team_fees[${teamIndex}]"]`);
            if (hiddenInput) {
                hiddenInput.value = parseFloat(this.value) || 0;
            }
            updateTeamTotal();
        });
    });
    
    function updateTeamTotal() {
        let totalAmount = 0;
        const hiddenInputs = teamFeesData.querySelectorAll('input[name^="team_fees"]');
        hiddenInputs.forEach(input => {
            totalAmount += parseFloat(input.value) || 0;
        });
        
        if (teamTotalAmount) {
            teamTotalAmount.textContent = '₹' + totalAmount.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }
    
    // Initialize on page load
    initializeTeamFees();
    @endif
});

    // Player update percentage functionality (for existing records)
    @if($existingPlayerRegistration && abs($playerBalance) > 0.01)
    const playerUpdatePercentageSlider = document.getElementById('playerUpdatePercentage');
    const playerUpdatePercentageInput = document.getElementById('playerUpdatePercentageInput');
    const playerUpdateActualCount = document.getElementById('playerUpdateActualCount');
    
    const totalPlayers = {{ $totalPotentialPlayers }};
    const playerRegFee = {{ $league->player_reg_fee }};
    
    function updatePlayerAmount() {
        const percentage = parseInt(playerUpdatePercentageSlider.value);
        const actualPlayers = Math.round((totalPlayers * percentage) / 100);
        const newAmount = actualPlayers * playerRegFee;
        
        playerUpdateActualCount.textContent = actualPlayers;
        
        // Show update button if amount changed
        const originalAmount = {{ $existingPlayerRegistration->amount }};
        const updateButtonContainer = document.getElementById('playerUpdateButtonContainer');
        const updateAmountHidden = document.getElementById('playerUpdateAmountHidden');
        
        if (Math.abs(newAmount - originalAmount) > 0.01) {
            updateButtonContainer.classList.remove('hidden');
            updateAmountHidden.value = newAmount;
        } else {
            updateButtonContainer.classList.add('hidden');
        }
    }
    
    if (playerUpdatePercentageSlider) {
        playerUpdatePercentageSlider.addEventListener('input', function() {
            playerUpdatePercentageInput.value = this.value;
            updatePlayerAmount();
        });
    }
    
    if (playerUpdatePercentageInput) {
        playerUpdatePercentageInput.addEventListener('input', function() {
            const value = Math.min(100, Math.max(1, parseInt(this.value) || 1));
            this.value = value;
            playerUpdatePercentageSlider.value = value;
            updatePlayerAmount();
        });
    }
    @endif
    
    // Simple team amount selection functionality
    @if($individualTeamRegistrations->count() > 0 && abs($teamBalance) > 0.01)
    const teamSelector = document.getElementById('teamSelector');
    const amountInputContainer = document.getElementById('amountInputContainer');
    const balanceContainer = document.getElementById('balanceContainer');
    const updateButtonContainer = document.getElementById('updateButtonContainer');
    const teamAmountInput = document.getElementById('teamAmountInput');
    const expectedAmountElement = document.getElementById('expectedAmount');
    const balanceAmountElement = document.getElementById('balanceAmount');
    const teamUpdateAmountHidden = document.getElementById('teamUpdateAmountHidden');
    const teamUpdateFeesData = document.getElementById('teamUpdateFeesData');
    
    const defaultTeamRegFee = {{ $league->team_reg_fee }};
    const individualTeamFees = @json($individualTeamFees);
    
    let selectedTeamIndex = null;
    let selectedTeamName = null;
    
    teamSelector.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value === '') {
            // Hide all containers
            amountInputContainer.style.display = 'none';
            balanceContainer.style.display = 'none';
            updateButtonContainer.style.display = 'none';
            selectedTeamIndex = null;
            selectedTeamName = null;
        } else {
            // Show amount input container
            selectedTeamIndex = parseInt(this.value);
            selectedTeamName = selectedOption.dataset.teamName;
            const currentFee = parseFloat(selectedOption.dataset.currentFee);
            
            amountInputContainer.style.display = 'block';
            teamAmountInput.value = currentFee;
            
            // Calculate and show balance
            calculateBalance(currentFee);
        }
    });
    
    teamAmountInput.addEventListener('input', function() {
        if (selectedTeamIndex !== null) {
            const newAmount = parseFloat(this.value) || 0;
            calculateBalance(newAmount);
        }
    });
    
    function calculateBalance(amountPaid) {
        const expectedAmount = defaultTeamRegFee;
        const balance = expectedAmount - amountPaid;
        
        expectedAmountElement.textContent = '₹' + expectedAmount.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        balanceAmountElement.textContent = '₹' + balance.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        // Show balance container
        balanceContainer.style.display = 'block';
        
        // Show update button if amount changed
        const originalAmount = individualTeamFees[selectedTeamName] || defaultTeamRegFee;
        if (Math.abs(amountPaid - originalAmount) > 0.01) {
            updateButtonContainer.style.display = 'block';
            
            // Update hidden form data
            updateFormData(amountPaid);
        } else {
            updateButtonContainer.style.display = 'none';
        }
    }
    
    function updateFormData(newAmount) {
        // Clear existing hidden inputs
        teamUpdateFeesData.innerHTML = '';
        
        // Create hidden inputs for all teams
        const teamCount = {{ $teamCount }};
        let totalAmount = 0;
        
        for (let i = 0; i < teamCount; i++) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `team_fees[${i}]`;
            
            if (i === selectedTeamIndex) {
                hiddenInput.value = newAmount;
                totalAmount += newAmount;
            } else {
                // Get current amount for other teams
                const teamOption = teamSelector.options[i + 1]; // +1 because of "Choose a team..." option
                const currentAmount = parseFloat(teamOption.dataset.currentFee);
                hiddenInput.value = currentAmount;
                totalAmount += currentAmount;
            }
            
            teamUpdateFeesData.appendChild(hiddenInput);
        }
        
        // Update total amount hidden input
        teamUpdateAmountHidden.value = totalAmount;
    }
    @endif
</script>
@endsection

