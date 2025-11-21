@extends('layouts.app')

@section('title', 'Edit Team - ' . $leagueTeam->team->name)

@section('content')
@php
    $isAdmin = auth()->user()?->isAdmin();
@endphp
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit League Team</h1>
                    <p class="text-gray-600 mt-1">{{ $leagueTeam->team->name }} - {{ $league->name }}</p>
                </div>
                <a href="{{ route('league-teams.show', [$league, $leagueTeam]) }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                    Back to Team
                </a>
            </div>

            <form action="{{ route('league-teams.update', [$league, $leagueTeam]) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                        <option value="pending" {{ old('status', $leagueTeam->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="available" {{ old('status', $leagueTeam->status) === 'available' ? 'selected' : '' }}>Available</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="wallet_balance" class="block text-sm font-medium text-gray-700 mb-2">
                        Wallet Balance
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₹</span>
                        </div>
                        <input type="number" 
                               name="wallet_balance" 
                               id="wallet_balance" 
                               min="0" 
                               max="{{ $league->team_wallet_limit }}"
                               step="0.01"
                               value="{{ old('wallet_balance', $leagueTeam->wallet_balance) }}"
                               placeholder="0.00"
                               @unless($isAdmin) readonly @endunless
                               class="w-full pl-8 border border-gray-300 rounded-md px-3 py-2 {{ $isAdmin ? 'bg-white' : 'bg-gray-100 cursor-not-allowed' }} @error('wallet_balance') border-red-500 @enderror">
                    </div>
                    @if($isAdmin)
                        <p class="mt-1 text-sm text-gray-500">
                            Admin only: adjust up to ₹{{ number_format($league->team_wallet_limit, 2) }}.
                        </p>
                    @else
                        <p class="mt-1 text-sm text-gray-500">
                            Maximum allowed: ₹{{ number_format($league->team_wallet_limit, 2) }} (Read-only)
                        </p>
                    @endif
                    @error('wallet_balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('league-teams.show', [$league, $leagueTeam]) }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                        Update Team
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection
