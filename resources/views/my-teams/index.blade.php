@extends('layouts.app')

@section('title', 'My Teams - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                My Teams
            </h1>
            <p class="text-lg sm:text-xl text-white-100">
                Your team ownership and participation
            </p>
        </div>
    </div>
</section>

<!-- Teams Content -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        @if($ownedTeams->isNotEmpty())
        <div class="mb-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Owned by Me
                </h2>
                <a href="{{ route('teams.create') }}" class="text-blue-700 hover:text-blue-800 font-medium flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create New Team
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($ownedTeams as $team)
                <a href="{{ route('teams.show', $team) }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp cursor-pointer">
                        <div class="h-40 overflow-hidden relative">
                            @if($team->logo)
                                <img src="{{ asset($team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/owned.jpg') }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                                <h3 class="text-xl font-semibold text-white p-4">{{ $team->name }}</h3>
                            </div>
                            <span class="absolute top-3 right-3 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">
                                Owner
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <p><span class="font-medium">🏟️ Home Ground:</span> {{ $team->homeGround->name ?? 'Not specified' }}</p>
                                <p><span class="font-medium">📍 Location:</span> {{ $team->localBody->name ?? 'Not specified' }}</p>
                                <p><span class="font-medium">📅 Created:</span> {{ $team->created_at->format('M Y') }}</p>
                            </div>
                            <div class="mt-6">
                                <span class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    Manage Team →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($playerTeams->isNotEmpty())
        <div class="mb-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    Playing In
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($playerTeams as $leaguePlayer)
                @if($leaguePlayer->leagueTeam && $leaguePlayer->leagueTeam->team)
                @php $team = $leaguePlayer->leagueTeam->team; @endphp
                <a href="{{ route('teams.show', $team) }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp cursor-pointer">
                        <div class="h-40 overflow-hidden relative">
                            @if($team->logo)
                                <img src="{{ asset($team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/leagueteams.jpg') }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                                <h3 class="text-xl font-semibold text-white p-4">{{ $team->name }}</h3>
                            </div>
                            <span class="absolute top-3 right-3 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">
                                {{ ucfirst($leaguePlayer->status) }}
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                @if($leaguePlayer->leagueTeam && $leaguePlayer->leagueTeam->league)
                                <p><span class="font-medium">🏆 League:</span> {{ $leaguePlayer->leagueTeam->league->name }}</p>
                                @endif
                                <p><span class="font-medium">🏟️ Home Ground:</span> {{ $team->homeGround->name ?? 'Not specified' }}</p>
                                <p><span class="font-medium">📍 Location:</span> {{ $team->localBody->name ?? 'Not specified' }}</p>
                            </div>
                            <div class="mt-6">
                                <span class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    View Team →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($requestedTeams->isNotEmpty())
        <div class="mb-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Registration Requested
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($requestedTeams as $leaguePlayer)
                @if($leaguePlayer->leagueTeam && $leaguePlayer->leagueTeam->team)
                @php $team = $leaguePlayer->leagueTeam->team; @endphp
                <a href="{{ route('teams.show', $team) }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp cursor-pointer">
                        <div class="h-40 overflow-hidden relative">
                            @if($team->logo)
                                <img src="{{ asset($team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/leagueteams.jpg') }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                                <h3 class="text-xl font-semibold text-white p-4">{{ $team->name }}</h3>
                            </div>
                            <span class="absolute top-3 right-3 bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">
                                Pending Approval
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                @if($leaguePlayer->leagueTeam && $leaguePlayer->leagueTeam->league)
                                <p><span class="font-medium">🏆 League:</span> {{ $leaguePlayer->leagueTeam->league->name }}</p>
                                @endif
                                <p><span class="font-medium">⏳ Status:</span> <span class="text-yellow-600">Awaiting Approval</span></p>
                                <p><span class="font-medium">📍 Location:</span> {{ $team->localBody->name ?? 'Not specified' }}</p>
                            </div>
                            <div class="mt-6">
                                <span class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    View Team →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($ownedTeams->isEmpty() && $playerTeams->isEmpty() && $requestedTeams->isEmpty())
        <div class="bg-white rounded-xl shadow-lg p-12 text-center animate-fadeInUp">
            <div class="mb-4">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Teams Yet</h3>
            <p class="text-gray-600 mb-6">You haven't joined or created any teams yet.</p>
            <a href="{{ route('teams.index') }}" class="inline-block bg-blue-700 hover:bg-blue-800 text-white py-2 px-6 rounded-lg font-medium active:scale-95 transition-all shadow-md hover:shadow-lg">
                Browse Available Teams
            </a>
        </div>
        @endif
    </div>
</section>

<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
</style>
@endsection