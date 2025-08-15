@extends('layouts.app')

@section('title', 'League Manager - Dashboard')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-12 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                        Welcome to Cricket League Manager
                    </h1>
                    <p class="text-lg sm:text-xl text-indigo-100 mb-8">
                        Organize leagues, manage teams, track player stats, and run exciting tournaments.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('leagues.index') }}"
                           class="bg-white text-indigo-600 px-6 py-3 rounded-xl font-medium 
                                  hover:bg-indigo-50 active:scale-95 transition-all shadow-md hover:shadow-lg">
                            My Leagues
                        </a>
                        <a href="{{ route('leagues.create') }}"
                           class="bg-indigo-500 text-white px-6 py-3 rounded-xl font-medium 
                                  hover:bg-indigo-400 border border-indigo-300 active:scale-95 
                                  transition-all shadow-md hover:shadow-lg">
                            Create New League
                        </a>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <img src="{{ asset('images/hero.jpg') }}"
                         alt="Cricket League"
                         class="rounded-xl shadow-xl hover:scale-[1.02] transition-transform duration-300">
                </div>
            </div>
        </div>
    </section>

    <!-- My Leagues Section -->
    <section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">My Leagues</h2>
                <a href="{{ route('leagues.create') }}"
                   class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create New League
                </a>
            </div>

            @if($userLeagues->isEmpty())
                <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fadeInUp">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5
                                     a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3
                                     m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">You haven't created any leagues yet</h3>
                    <p class="text-gray-600 mb-6">Get started by creating your first cricket league.</p>
                    <a href="{{ route('leagues.create') }}"
                       class="inline-block bg-indigo-600 text-white py-2 px-6 rounded-lg font-medium 
                              hover:bg-indigo-700 active:scale-95 transition-all shadow-md hover:shadow-lg">
                        Create Your First League
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($userLeagues as $league)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                            <div class="h-40 overflow-hidden relative">
                                <img src="{{ asset('images/league.jpg') }}" 
                                     alt="{{ $league->name }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                                    <h3 class="text-xl font-semibold text-white p-4">{{ $league->name }}</h3>
                                </div>
                                @if($league->is_default)
                                    <span class="absolute top-3 right-3 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">Default</span>
                                @endif
                            </div>
                            <div class="p-6">
                                <div class="mb-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shadow-sm
                                        {{ 
                                            $league->status === 'active' ? 'bg-green-100 text-green-800' : 
                                            ($league->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($league->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) 
                                        }}">
                                        {{ ucfirst($league->status) }}
                                    </span>
                                </div>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <p><span class="font-medium">🎮 Game:</span> {{ $league->game->name }}</p>
                                    <p><span class="font-medium">📅 Season:</span> {{ $league->season }}</p>
                                    <p><span class="font-medium">⏳ Duration:</span> {{ $league->start_date->format('M d, Y') }} to {{ $league->end_date->format('M d, Y') }}</p>
                                    @if($league->localbody_id)
                                        <p><span class="font-medium">📍 Venue:</span> {{ $league->localBody->name }}</p>
                                    @endif
                                    @if($league->venue_details)
                                        <p><span class="font-medium">🏟️ Details:</span> {{ $league->venue_details }}</p>
                                    @endif
                                </div>
                                <div class="mt-6">
                                    <a href="{{ route('leagues.show', $league) }}"
                                       class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        View Details →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Owned Teams Section -->
    <section class="py-12 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Owned Teams</h2>
                <p class="text-gray-600">Teams you own and manage</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Owned Team 1 -->
                <div class="bg-gray-50 rounded-xl shadow-sm overflow-hidden hover:shadow-lg hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    <div class="h-40 overflow-hidden relative">
                        <img src="{{ asset('images/owned.jpg') }}" 
                             alt="Super Kings" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                            <h3 class="text-lg font-semibold text-white p-4">Super Kings</h3>
                        </div>
                        <div class="absolute top-3 right-3">
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">Owner</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shadow-sm bg-green-100 text-green-800">
                                Active
                            </span>
                            <span class="text-gray-500 text-sm">Since 2024</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <p><span class="font-medium">League:</span> Kerala Cricket League</p>
                            <p><span class="font-medium">Players:</span> 12 / 15</p>
                            <p><span class="font-medium">Matches:</span> Won 7, Lost 2</p>
                            <p><span class="font-medium">Position:</span> Top of the table</p>
                        </div>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Manage Team →
                        </a>
                    </div>
                </div>

                <!-- Owned Team 2 -->
                <div class="bg-gray-50 rounded-xl shadow-sm overflow-hidden hover:shadow-lg hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    <div class="h-40 overflow-hidden relative">
                        <img src="{{ asset('images/owned.jpg') }}" 
                             alt="Bangalore Strikers" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                            <h3 class="text-lg font-semibold text-white p-4">Bangalore Strikers</h3>
                        </div>
                        <div class="absolute top-3 right-3">
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">Owner</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shadow-sm bg-yellow-100 text-yellow-800">
                                Pre-season
                            </span>
                            <span class="text-gray-500 text-sm">New Team</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <p><span class="font-medium">League:</span> South Indian Cricket Cup</p>
                            <p><span class="font-medium">Players:</span> 8 / 15</p>
                            <p><span class="font-medium">Registration:</span> In Progress</p>
                            <p><span class="font-medium">Position:</span> Not Started</p>
                        </div>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Complete Registration →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- My League Teams Section -->
    <section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">My League Teams</h2>
                <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Join a Team
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Team Card 1 -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    <div class="h-48 overflow-hidden relative">
                        <img src="{{ asset('images/leagueteams.jpg') }}" alt="Royal Challengers" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                            <h3 class="text-xl font-bold text-white p-4">Royal Challengers</h3>
                        </div>
                        <div class="absolute top-3 right-3">
                            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/2/2a/Royal_Challengers_Bangalore_2020.svg/220px-Royal_Challengers_Bangalore_2020.svg.png" alt="Team Logo" class="w-12 h-12 object-contain bg-white rounded-full p-1">
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">Active</span>
                            <span class="text-gray-600 text-sm">Since 2023</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">🏆 League:</span> Kochi Premier League</p>
                            <p><span class="font-medium">👥 Players:</span> 11/15</p>
                            <p><span class="font-medium">🏏 Role:</span> Batsman</p>
                            <p><span class="font-medium">🏅 Position:</span> 2nd in league</p>
                        </div>
                        <div class="mt-6 flex justify-between">
                            <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">View Team →</a>
                            <span class="text-gray-500 text-sm">Won 5/7 matches</span>
                        </div>
                    </div>
                </div>

                <!-- Team Card 2 -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    <div class="h-48 overflow-hidden relative">
                        <img src="{{ asset('images/leagueteams.jpg') }}" alt="Chennai Kings" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                            <h3 class="text-xl font-bold text-white p-4">Chennai Kings</h3>
                        </div>
                        <div class="absolute top-3 right-3">
                            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/2/2b/Chennai_Super_Kings_Logo.svg/220px-Chennai_Super_Kings_Logo.svg.png" alt="Team Logo" class="w-12 h-12 object-contain bg-white rounded-full p-1">
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">Pending</span>
                            <span class="text-gray-600 text-sm">Since 2024</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">🏆 League:</span> South India Tournament</p>
                            <p><span class="font-medium">👥 Players:</span> 9/15</p>
                            <p><span class="font-medium">🏏 Role:</span> All-rounder</p>
                            <p><span class="font-medium">🏅 Position:</span> Registration Phase</p>
                        </div>
                        <div class="mt-6 flex justify-between">
                            <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">View Team →</a>
                            <span class="text-gray-500 text-sm">Upcoming season</span>
                        </div>
                    </div>
                </div>

                <!-- Team Card 3 -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    <div class="h-48 overflow-hidden relative">
                        <img src="{{ asset('images/leagueteams.jpg') }}" alt="Mumbai Indians" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                            <h3 class="text-xl font-bold text-white p-4">Mumbai Indians</h3>
                        </div>
                        <div class="absolute top-3 right-3">
                            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/c/cd/Mumbai_Indians_Logo.svg/220px-Mumbai_Indians_Logo.svg.png" alt="Team Logo" class="w-12 h-12 object-contain bg-white rounded-full p-1">
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">Completed</span>
                            <span class="text-gray-600 text-sm">2022-2023</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">🏆 League:</span> National Cricket Cup</p>
                            <p><span class="font-medium">👥 Players:</span> 15/15</p>
                            <p><span class="font-medium">🏏 Role:</span> Bowler</p>
                            <p><span class="font-medium">🏅 Position:</span> Champions</p>
                        </div>
                        <div class="mt-6 flex justify-between">
                            <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">View Team →</a>
                            <span class="text-gray-500 text-sm">Won 8/10 matches</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- My Player Profile Section -->
    <section class="py-12 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">My Player Profile</h2>
                <p class="text-gray-600">Your cricket statistics and achievements</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 animate-fadeInUp">
                <div class="grid grid-cols-1 lg:grid-cols-3">
                    <!-- Profile Details -->
                    <div class="p-6 lg:p-8 bg-gradient-to-br from-indigo-500 to-purple-600 text-white relative">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-32 h-32 rounded-full overflow-hidden mb-4 border-4 border-white shadow-lg">
                                <img src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                                     alt="Player Photo" class="w-full h-full object-cover">
                            </div>
                            <h3 class="text-2xl font-bold mb-1">John Smith</h3>
                            <p class="text-indigo-200 mb-4">Right-handed Batsman</p>
                            <div class="flex space-x-3 mb-6">
                                <span class="bg-white/20 text-white text-xs font-medium px-3 py-1 rounded-full">All-rounder</span>
                                <span class="bg-white/20 text-white text-xs font-medium px-3 py-1 rounded-full">Captain</span>
                            </div>
                            <div class="w-full space-y-3 text-left">
                                <p class="flex justify-between">
                                    <span>Age:</span>
                                    <span class="font-medium">28</span>
                                </p>
                                <p class="flex justify-between">
                                    <span>Batting Style:</span>
                                    <span class="font-medium">Right Handed</span>
                                </p>
                                <p class="flex justify-between">
                                    <span>Bowling Style:</span>
                                    <span class="font-medium">Right Arm Medium</span>
                                </p>
                                <p class="flex justify-between">
                                    <span>Experience:</span>
                                    <span class="font-medium">5 years</span>
                                </p>
                            </div>
                        </div>
                        <div class="absolute bottom-0 right-0 opacity-10">
                            <img src="https://upload.wikimedia.org/wikipedia/en/8/8d/Cricket_India_Crest.svg" 
                                 alt="Cricket Logo" class="w-40 h-40 object-contain">
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="lg:col-span-2 p-6 lg:p-8">
                        <h4 class="text-xl font-semibold text-gray-900 mb-6">Career Statistics</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <!-- Batting Stats -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1099/1099680.png" alt="Batting" class="w-8 h-8 mr-2">
                                    <h5 class="text-lg font-medium text-gray-900">Batting</h5>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Matches</span>
                                        <span class="font-medium">42</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Innings</span>
                                        <span class="font-medium">38</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Runs</span>
                                        <span class="font-medium">1,258</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Average</span>
                                        <span class="font-medium">36.24</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Strike Rate</span>
                                        <span class="font-medium">142.8</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Highest Score</span>
                                        <span class="font-medium">89*</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bowling Stats -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1099/1099673.png" alt="Bowling" class="w-8 h-8 mr-2">
                                    <h5 class="text-lg font-medium text-gray-900">Bowling</h5>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Matches</span>
                                        <span class="font-medium">42</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Innings</span>
                                        <span class="font-medium">40</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Wickets</span>
                                        <span class="font-medium">53</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Economy</span>
                                        <span class="font-medium">7.32</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Average</span>
                                        <span class="font-medium">22.15</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Best Figures</span>
                                        <span class="font-medium">4/28</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Achievements -->
                        <div>
                            <h5 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <img src="https://cdn-icons-png.flaticon.com/512/2583/2583344.png" alt="Trophy" class="w-6 h-6 mr-2">
                                Achievements
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-center bg-indigo-50 p-3 rounded-lg">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1021/1021220.png" alt="Medal" class="w-6 h-6 text-indigo-600 mr-2">
                                    <span>Man of the Match (5)</span>
                                </div>
                                <div class="flex items-center bg-indigo-50 p-3 rounded-lg">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4232/4232494.png" alt="Trophy" class="w-6 h-6 text-indigo-600 mr-2">
                                    <span>Tournament Winner (2)</span>
                                </div>
                                <div class="flex items-center bg-indigo-50 p-3 rounded-lg">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1021/1021219.png" alt="Award" class="w-6 h-6 text-indigo-600 mr-2">
                                    <span>Best Bowler (1)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Become an Organizer Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-indigo-50">
        <div class="max-w-5xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Become a League Organizer</h2>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Create and manage your own cricket leagues, set rules, schedule matches, and oversee competitions.
            </p>
            <div class="bg-white rounded-xl shadow-xl overflow-hidden animate-fadeInUp">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="p-8 flex flex-col justify-center">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Organizer Benefits</h3>
                        <ul class="space-y-3 text-left">
                            <li class="flex items-start"><span class="text-green-500 mr-2">✅</span> Create multiple leagues with customizable rules</li>
                            <li class="flex items-start"><span class="text-green-500 mr-2">✅</span> Manage team registrations and player drafts</li>
                            <li class="flex items-start"><span class="text-green-500 mr-2">✅</span> Oversee match scheduling and results</li>
                            <li class="flex items-start"><span class="text-green-500 mr-2">✅</span> Access to detailed analytics and reporting tools</li>
                        </ul>
                    </div>
                    <div class="p-8 bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex flex-col justify-center">
                        <h3 class="text-2xl font-semibold mb-4">Ready to Get Started?</h3>
                        <p class="mb-6">Join today and create your first cricket league in minutes.</p>
                        <a href="{{ route('leagues.create') }}"
                           class="bg-white text-indigo-600 px-6 py-3 rounded-xl font-medium 
                                  hover:bg-indigo-50 active:scale-95 transition-all shadow-md hover:shadow-lg">
                            Create Your First League
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Logout Button -->
    <div class="fixed bottom-4 right-4 p-4 bg-white rounded-full shadow-lg hover:shadow-xl transition-all animate-fadeInUp">
        <a href="{{ route('logout') }}"
           class="text-gray-700 hover:text-red-600 transition-colors flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 
                         01-3 3H6a3 3 0 01-3-3V7a3 3 0 
                         013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Logout</span>
        </a>
    </div>

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
        .animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
    </style>
@endsection
