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

        @php
            // Helper function to format numbers compactly (1000 -> 1K)
            function formatCompactNumber($number) {
                if ($number >= 1000000) {
                    return number_format($number / 1000000, 1) . 'M';
                } elseif ($number >= 100000) {
                    return number_format($number / 1000, 0) . 'K';
                } elseif ($number >= 1000) {
                    return number_format($number / 1000, 1) . 'K';
                }
                return number_format($number, 0);
            }
        @endphp

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
                            <p class="text-base sm:text-lg font-bold text-gray-900">₹{{ formatCompactNumber($league->player_reg_fee) }}</p>
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
                                    <p class="text-sm font-bold text-blue-800">₹{{ formatCompactNumber($existingPlayerRegistration->amount) }}</p>
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
                                            {{ $playerBalance > 0 ? '+' : '' }}₹{{ formatCompactNumber(abs($playerBalance)) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-orange-600 mt-1">
                                        Expected: ₹{{ formatCompactNumber($expectedPlayerAmount) }} |
                                        Recorded: ₹{{ formatCompactNumber($existingPlayerRegistration->amount) }}
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
                                    ₹{{ formatCompactNumber($existingPlayerRegistration ? $existingPlayerRegistration->amount : $expectedPlayerAmount) }}
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
                    <div class="flex-1">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900">Team Registration Fees</h3>
                        <p class="text-xs sm:text-sm text-gray-600">Track payments for each team</p>
                    </div>
                </div>

                @if($teamCount > 0)
                    @php
                        // Calculate totals
                        $totalExpected = $teamCount * $league->team_reg_fee;
                        $totalPaid = 0;
                        $teamPayments = [];
                        
                        foreach($league->leagueTeams as $leagueTeam) {
                            $paid = \App\Models\LeagueFinance::where('league_id', $league->id)
                                ->where('type', 'income')
                                ->where('title', 'like', '%Team Registration Fee - ' . $leagueTeam->team->name . '%')
                                ->sum('amount');
                            $teamPayments[$leagueTeam->id] = $paid;
                            $totalPaid += $paid;
                        }
                        $totalBalance = $totalExpected - $totalPaid;
                    @endphp

                    <!-- Summary Stats -->
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                            <p class="text-xs font-medium text-blue-600 uppercase">Expected</p>
                            <p class="text-lg font-bold text-blue-700">₹{{ formatCompactNumber($totalExpected) }}</p>
                            <p class="text-xs text-blue-600">{{ $teamCount }} teams</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            <p class="text-xs font-medium text-green-600 uppercase">Received</p>
                            <p class="text-lg font-bold text-green-700">₹{{ formatCompactNumber($totalPaid) }}</p>
                        </div>
                        <div class="bg-{{ $totalBalance > 0 ? 'orange' : 'gray' }}-50 rounded-lg p-3 text-center">
                            <p class="text-xs font-medium text-{{ $totalBalance > 0 ? 'orange' : 'gray' }}-600 uppercase">Balance</p>
                            <p class="text-lg font-bold text-{{ $totalBalance > 0 ? 'orange' : 'gray' }}-700">₹{{ formatCompactNumber($totalBalance) }}</p>
                        </div>
                    </div>

                    <!-- Teams List -->
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @foreach($league->leagueTeams as $leagueTeam)
                            @php
                                $paid = $teamPayments[$leagueTeam->id] ?? 0;
                                $balance = $league->team_reg_fee - $paid;
                                $status = $paid >= $league->team_reg_fee ? 'paid' : ($paid > 0 ? 'partial' : 'pending');
                            @endphp
                            
                            <div class="border border-gray-200 rounded-lg p-3 hover:border-green-300 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center flex-1">
                                        <h4 class="text-sm font-bold text-gray-900">{{ $leagueTeam->team->name }}</h4>
                                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($status === 'paid') bg-green-100 text-green-800
                                            @elseif($status === 'partial') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            @if($status === 'paid') Paid
                                            @elseif($status === 'partial') Partial
                                            @else Pending
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-2 mb-2">
                                    <div>
                                        <p class="text-xs text-gray-500">Expected</p>
                                        <p class="text-sm font-semibold text-gray-700">₹{{ formatCompactNumber($league->team_reg_fee) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Paid</p>
                                        <p class="text-sm font-semibold text-green-600">₹{{ formatCompactNumber($paid) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Balance</p>
                                        <p class="text-sm font-semibold {{ $balance > 0 ? 'text-orange-600' : 'text-gray-500' }}">
                                            ₹{{ formatCompactNumber($balance) }}
                                        </p>
                                    </div>
                                </div>

                                @if($balance > 0)
                                    <form method="POST" action="{{ route('league-finances.individual-team-income', $league) }}" class="flex gap-2">
                                        @csrf
                                        <input type="hidden" name="team_id" value="{{ $leagueTeam->id }}">
                                        <div class="relative flex-1">
                                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs">₹</span>
                                            <input type="number" 
                                                   name="amount" 
                                                   step="0.01" 
                                                   min="0.01" 
                                                   max="{{ $balance }}"
                                                   value="{{ $balance }}"
                                                   class="w-full pl-6 pr-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                   required>
                                        </div>
                                        <button type="submit" 
                                                class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors whitespace-nowrap">
                                            Add Payment
                                        </button>
                                    </form>
                                @else
                                    <p class="text-xs text-green-600 font-medium">✓ Fully Paid</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-500">No teams registered yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 lg:p-8">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Add Manual Transaction</h2>
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
                                       {{ old('type', 'income') === 'income' ? 'checked' : '' }}>
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
    // Helper function to format numbers compactly (1000 -> 1K)
    function formatCompactNumber(number) {
        if (number >= 1000000) {
            return (number / 1000000).toFixed(1) + 'M';
        } else if (number >= 100000) {
            return Math.round(number / 1000) + 'K';
        } else if (number >= 1000) {
            return (number / 1000).toFixed(1) + 'K';
        }
        return Math.round(number).toString();
    }

    // --- General Form: Transaction Type Category Filtering ---
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const categorySelect = document.querySelector('select[name="expense_category_id"]');
    
    function updateCategories() {
        const selectedType = document.querySelector('input[name="type"]:checked')?.value;
        const options = categorySelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') return; // Keep the default 'Select a category' option
            
            const categoryText = option.textContent.trim();
            const isIncomeCategory = @json($incomeCategories->pluck('name')->toArray()).includes(categoryText);
            const isExpenseCategory = @json($expenseCategories->pluck('name')->toArray()).includes(categoryText);
            
            option.style.display = 'none'; // Hide all options first
            
            if (selectedType === 'income' && isIncomeCategory) {
                option.style.display = 'block';
            } else if (selectedType === 'expense' && isExpenseCategory) {
                option.style.display = 'block';
            }
        });
        
        // If the currently selected option is now hidden, reset the dropdown
        const selectedOption = categorySelect.querySelector(`option[value="${categorySelect.value}"]`);
        if (selectedOption && selectedOption.style.display === 'none') {
            categorySelect.value = '';
        }
    }
    
    typeRadios.forEach(radio => radio.addEventListener('change', updateCategories));
    updateCategories(); // Initial call to set the correct state on page load

    
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
        
        playerTotalAmount.textContent = '₹' + formatCompactNumber(totalAmount);
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