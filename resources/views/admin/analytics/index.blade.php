@extends('layouts.app')

@section('title', 'Analytics Dashboard | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
                    <p class="text-gray-600 mt-2">Comprehensive overview of platform activity and statistics</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.auction-panel.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Auction Panel
                    </a>
                    <a href="{{ route('admin.organizer-requests.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Admin
                    </a>
                </div>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form action="{{ route('admin.analytics.index') }}" method="GET">
                <div class="flex items-center gap-4">
                    <label for="date_range" class="text-sm font-medium text-gray-700">Date Range:</label>
                    <select name="date_range" id="date_range" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2">
                        <option value="7" {{ $dateRange == '7' ? 'selected' : '' }}>Last 7 days</option>
                        <option value="30" {{ $dateRange == '30' ? 'selected' : '' }}>Last 30 days</option>
                        <option value="90" {{ $dateRange == '90' ? 'selected' : '' }}>Last 90 days</option>
                        <option value="365" {{ $dateRange == '365' ? 'selected' : '' }}>Last year</option>
                    </select>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Apply Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Player Registrations -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Player Registrations</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $playerStats['new'] }}</p>
                        <p class="text-sm text-gray-600">Total: {{ $playerStats['total'] }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.analytics.player-registrations', ['date_range' => $dateRange]) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Details →</a>
                </div>
            </div>

            <!-- Teams Created -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Teams Created</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $teamStats['new'] }}</p>
                        <p class="text-sm text-gray-600">Total: {{ $teamStats['total'] }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.analytics.team-creations', ['date_range' => $dateRange]) }}" 
                       class="text-green-600 hover:text-green-800 text-sm font-medium">View Details →</a>
                </div>
            </div>

            <!-- Leagues Created -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5zM8 15a1 1 0 100-2 1 1 0 000 2zm4 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Leagues Created</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $leagueStats['new'] }}</p>
                        <p class="text-sm text-gray-600">Total: {{ $leagueStats['total'] }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.analytics.league-creations', ['date_range' => $dateRange]) }}" 
                       class="text-purple-600 hover:text-purple-800 text-sm font-medium">View Details →</a>
                </div>
            </div>

            <!-- Auctions Completed -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Auctions Completed</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $auctionStats['completed'] }}</p>
                        <p class="text-sm text-gray-600">Players Sold: {{ $auctionStats['players_sold'] }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.analytics.auction-completions', ['date_range' => $dateRange]) }}" 
                       class="text-orange-600 hover:text-orange-800 text-sm font-medium">View Details →</a>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Daily Player Registrations Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Player Registrations</h3>
                <div class="h-64">
                    <canvas id="playerRegistrationsChart"></canvas>
                </div>
            </div>

            <!-- Daily Team Creations Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Team Creations</h3>
                <div class="h-64">
                    <canvas id="teamCreationsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
            <div class="space-y-4">
                @forelse($activityLogs as $log)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-{{ $log['color'] }}-100 rounded-full flex items-center justify-center">
                                @if($log['icon'] === 'user-plus')
                                    <svg class="w-4 h-4 text-{{ $log['color'] }}-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                                    </svg>
                                @elseif($log['icon'] === 'users')
                                    <svg class="w-4 h-4 text-{{ $log['color'] }}-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                    </svg>
                                @elseif($log['icon'] === 'trophy')
                                    <svg class="w-4 h-4 text-{{ $log['color'] }}-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5zM8 15a1 1 0 100-2 1 1 0 000 2zm4 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($log['icon'] === 'gavel')
                                    <svg class="w-4 h-4 text-{{ $log['color'] }}-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $log['description'] }}</p>
                            <p class="text-xs text-gray-500">{{ $log['timestamp']->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500 mt-2">No recent activity found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Player Registrations Chart
const playerCtx = document.getElementById('playerRegistrationsChart').getContext('2d');
new Chart(playerCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($playerStats['daily']->pluck('date')) !!},
        datasets: [{
            label: 'Player Registrations',
            data: {!! json_encode($playerStats['daily']->pluck('count')) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Team Creations Chart
const teamCtx = document.getElementById('teamCreationsChart').getContext('2d');
new Chart(teamCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($teamStats['daily']->pluck('date')) !!},
        datasets: [{
            label: 'Team Creations',
            data: {!! json_encode($teamStats['daily']->pluck('count')) !!},
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection
