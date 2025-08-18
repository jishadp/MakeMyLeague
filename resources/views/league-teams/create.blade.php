@extends('layouts.app')

@section('title', 'Add Team - ' . $league->name)

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Add Team to League</h1>
                    <p class="text-gray-600 mt-1">{{ $league->name }}</p>
                </div>
                <a href="{{ route('league-teams.index', $league) }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                    Back to Teams
                </a>
            </div>

            <form action="{{ route('league-teams.store', $league) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="team_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Team <span class="text-red-500">*</span>
                    </label>
                    <select name="team_id" id="team_id" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('team_id') border-red-500 @enderror">
                        <option value="">Choose a team...</option>
                        @foreach($availableTeams as $team)
                            <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }} ({{ $team->owner->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('team_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($availableTeams->isEmpty())
                        <p class="mt-1 text-sm text-yellow-600">All teams are already added to this league.</p>
                    @endif
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Available</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="wallet_balance" class="block text-sm font-medium text-gray-700 mb-2">
                        Max Team Wallet Limit
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
                               value="{{ old('wallet_balance', $league->team_wallet_limit) }}"
                               placeholder="0.00"
                               class="w-full pl-8 border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('wallet_balance') border-red-500 @enderror">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Maximum allowed: ₹{{ number_format($league->team_wallet_limit, 2) }}
                    </p>
                    @error('wallet_balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('league-teams.index', $league) }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                        Add Team
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection
