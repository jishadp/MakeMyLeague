@extends('layouts.app')

@section('title', 'Auction Setup - ' . $league->name)

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('leagues.show', $league) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">← Back to League</a>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 tracking-tight">Auction Setup</h2>
            <p class="text-gray-600 mb-8">Configure auction parameters for {{ $league->name }}</p>
            
            <form action="{{ route('auction.start', $league) }}" method="POST" class="space-y-6" novalidate>
                @csrf
                
                <div>
                    <label for="min_players_needed" class="block text-sm font-medium text-gray-700 mb-2">Minimum Players Needed</label>
                    <input type="number" name="min_players_needed" id="min_players_needed" 
                           value="{{ old('min_players_needed', 11) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                    <p class="text-sm text-gray-500 mt-1">Minimum squad size per team (1-25 players)</p>
                    @error('min_players_needed')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="min_bid_amount" class="block text-sm font-medium text-gray-700 mb-2">Minimum Bid Amount</label>
                    <input type="number" name="min_bid_amount" id="min_bid_amount" 
                           value="{{ old('min_bid_amount', 100000) }}" step="100"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                    <p class="text-sm text-gray-500 mt-1">Base price for player bidding (minimum: ₹100)</p>
                    @error('min_bid_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purse_balance" class="block text-sm font-medium text-gray-700 mb-2">Purse Balance for Each Team</label>
                    <input type="number" name="purse_balance" id="purse_balance" 
                           value="{{ old('purse_balance', 10000000) }}" step="100000"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                    <p class="text-sm text-gray-500 mt-1">Must be at least (minimum players × minimum bid amount)</p>
                    <p class="text-sm text-indigo-600" id="minPurseInfo">Calculating minimum required purse...</p>
                    @error('purse_balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit" id="submitButton"
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all text-lg font-medium w-full sm:w-auto">
                        Start Auction
                    </button>
                    <a href="{{ route('leagues.show', $league) }}" 
                       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 shadow-md hover:shadow-lg transition-all text-lg font-medium text-center w-full sm:w-auto">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const minPlayersInput = document.getElementById('min_players_needed');
            const minBidInput = document.getElementById('min_bid_amount');
            const purseInput = document.getElementById('purse_balance');
            const minPurseInfo = document.getElementById('minPurseInfo');
            const form = document.querySelector('form');
            
            function updateMinPurseInfo() {
                const minPlayers = parseInt(minPlayersInput.value) || 0;
                const minBid = parseFloat(minBidInput.value) || 0;
                const minRequired = minPlayers * minBid;
                
                // Format number with commas
                const formattedAmount = new Intl.NumberFormat('en-IN').format(minRequired);
                
                minPurseInfo.textContent = `Minimum required purse: ₹${formattedAmount} (${minPlayers} players × ₹${new Intl.NumberFormat('en-IN').format(minBid)})`;
                
                // We don't set min attribute to avoid browser validation messages
                // Instead we'll handle it in the form submission
                
                // Highlight if current value is less than required
                if (parseFloat(purseInput.value) < minRequired) {
                    minPurseInfo.classList.remove('text-indigo-600');
                    minPurseInfo.classList.add('text-red-600', 'font-semibold');
                } else {
                    minPurseInfo.classList.remove('text-red-600', 'font-semibold');
                    minPurseInfo.classList.add('text-indigo-600');
                }
            }
            
            // Update on input change
            minPlayersInput.addEventListener('input', updateMinPurseInfo);
            minBidInput.addEventListener('input', updateMinPurseInfo);
            purseInput.addEventListener('input', updateMinPurseInfo);
            
            // Form validation
            form.addEventListener('submit', function(e) {
                // Ensure min bid amount is at least 100
                if (parseFloat(minBidInput.value) < 100) {
                    e.preventDefault();
                    const errorEl = document.createElement('p');
                    errorEl.className = 'mt-1 text-sm text-red-600';
                    errorEl.textContent = 'Minimum bid amount must be at least ₹100';
                    
                    // Remove any existing error message
                    const existingError = minBidInput.parentNode.querySelector('.text-red-600');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    minBidInput.parentNode.appendChild(errorEl);
                    minBidInput.focus();
                    return false;
                }
                
                // Check purse balance is sufficient
                const minPlayers = parseInt(minPlayersInput.value) || 0;
                const minBid = parseFloat(minBidInput.value) || 0;
                const minRequired = minPlayers * minBid;
                
                if (parseFloat(purseInput.value) < minRequired) {
                    e.preventDefault();
                    const formattedAmount = new Intl.NumberFormat('en-IN').format(minRequired);
                    
                    const errorEl = document.createElement('p');
                    errorEl.className = 'mt-1 text-sm text-red-600';
                    errorEl.textContent = `Purse balance must be at least ₹${formattedAmount} (${minPlayers} players × ₹${new Intl.NumberFormat('en-IN').format(minBid)})`;
                    
                    // Remove any existing error message
                    const existingError = purseInput.parentNode.querySelector('.text-red-600');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    purseInput.parentNode.appendChild(errorEl);
                    purseInput.focus();
                    return false;
                }
            });
            
            // Initial calculation
            updateMinPurseInfo();
        });
    </script>
@endsection