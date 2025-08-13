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
                    <img src="https://placehold.co/600x400/667EEA/FFFFFF/png?text=Cricket+League+Manager&font=Montserrat"
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
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ $league->name }}</h3>
                                    @if($league->is_default)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">Default</span>
                                    @endif
                                </div>
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

    <!-- Active Leagues Section -->
    <section class="py-12 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Active Leagues</h2>
                <p class="text-gray-600">Browse all active cricket leagues</p>
            </div>

            @if($leagues->isEmpty())
                <div class="bg-gray-50 rounded-xl p-6 text-center animate-fadeInUp">
                    <p class="text-gray-600">No active leagues available at the moment.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($leagues as $league)
                        <div class="bg-gray-50 rounded-xl shadow-sm p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $league->name }}</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shadow-sm
                                    {{ $league->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($league->status) }}
                                </span>
                            </div>
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <p><span class="font-medium">Organizer:</span> {{ $league->organizer->name }}</p>
                                <p><span class="font-medium">Teams:</span> 0 / {{ $league->max_teams }}</p>
                                <p><span class="font-medium">Start Date:</span> {{ $league->start_date->format('M d, Y') }}</p>
                                @if($league->localbody_id)
                                    <p><span class="font-medium">Venue:</span> {{ $league->localBody->name }}</p>
                                @endif
                                @if($league->venue_details)
                                    <p><span class="font-medium">Venue Details:</span> {{ $league->venue_details }}</p>
                                @endif
                            </div>
                            <a href="{{ route('leagues.show', $league) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                View League Details →
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
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
