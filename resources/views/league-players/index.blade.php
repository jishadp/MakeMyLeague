@extends('layouts.app')

@section('title', 'League Players - ' . $league->name)

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @php
            $canManageLeague = auth()->check() && auth()->user()->canManageLeague($league->id);
            $currentStatusFilter = $statusFilter ?? 'available';
            $sharePlayers = $league->leaguePlayers ?? collect();
            if (method_exists($sharePlayers, 'loadMissing')) {
                $sharePlayers = $sharePlayers->loadMissing(['user.localBody', 'leagueTeam.team']);
            }
            $retentionPlayersShare = $sharePlayers->where('retention', true)->sortBy('leagueTeam.team.name');
            $soldPlayersShare = $sharePlayers->where('status', 'sold')->where('retention', false)->sortByDesc('bid_price');
            $availablePlayersShare = $sharePlayers->where('status', 'available')->where('retention', false)->sortBy('user.name');
            $unsoldPlayersShare = $sharePlayers->where('status', 'unsold')->where('retention', false)->sortBy('user.name');
            $availableByLocationShare = $availablePlayersShare->groupBy(function ($player) {
                return optional(optional($player->user)->localBody)->name ?? 'Unknown';
            })->sortKeys();
            $shareInitials = \Illuminate\Support\Str::upper(collect(explode(' ', $league->name))
                ->filter()
                ->map(fn ($word) => \Illuminate\Support\Str::substr($word, 0, 1))
                ->implode(''));
            if (\Illuminate\Support\Str::length($shareInitials) < 2) {
                $shareInitials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($league->name, 0, 3));
            }
            $shareLines = [
                "⭐ {$shareInitials} – {$league->name} (Season {$league->season}) ⭐",
                "────────────────────────────────────────",
                '',
            ];
            $shareCounter = 1;
            if ($retentionPlayersShare->count() > 0) {
                $shareLines[] = "📍 RETAINED PLAYERS ({$retentionPlayersShare->count()})";
                $shareLines[] = '';
                foreach ($retentionPlayersShare as $player) {
                    $line = $shareCounter . '. ' . (optional($player->user)->name ?? 'Unknown');
                    if (optional($player->user)->phone) {
                        $line .= ' – 📞 ' . optional($player->user)->phone;
                    }
                    $shareLines[] = $line;
                    $shareCounter++;
                }
                $shareLines[] = '';
            }
            if ($soldPlayersShare->count() > 0) {
                $shareLines[] = "📍 SOLD PLAYERS ({$soldPlayersShare->count()})";
                $shareLines[] = '';
                foreach ($soldPlayersShare as $player) {
                    $line = optional($player->user)->name ?? 'Unknown';
                    if (optional($player->user)->phone) {
                        $line .= ' – 📞 ' . optional($player->user)->phone;
                    }
                    if ($player->leagueTeam?->team?->name) {
                        $line .= ' – ' . $player->leagueTeam->team->name;
                    }
                    if ($player->bid_price) {
                        $line .= ' (₹' . number_format($player->bid_price) . ')';
                    }
                    $shareLines[] = $shareCounter . '. ' . $line;
                    $shareCounter++;
                }
                $shareLines[] = '';
            }
            foreach ($availableByLocationShare as $location => $playersByLocation) {
                $shareLines[] = "📍 AVAILABLE – " . \Illuminate\Support\Str::upper($location) . " ({$playersByLocation->count()})";
                $shareLines[] = '';
                foreach ($playersByLocation as $player) {
                    $line = $shareCounter . '. ' . (optional($player->user)->name ?? 'Unknown');
                    if (optional($player->user)->phone) {
                        $line .= ' – 📞 ' . optional($player->user)->phone;
                    }
                    $shareLines[] = $line;
                    $shareCounter++;
                }
                $shareLines[] = '';
            }
            if ($unsoldPlayersShare->count() > 0) {
                $shareLines[] = "📍 UNSOLD ({$unsoldPlayersShare->count()})";
                $shareLines[] = '';
                foreach ($unsoldPlayersShare as $player) {
                    $line = $shareCounter . '. ' . (optional($player->user)->name ?? 'Unknown');
                    if (optional($player->user)->phone) {
                        $line .= ' – 📞 ' . optional($player->user)->phone;
                    }
                    $shareLines[] = $line;
                    $shareCounter++;
                }
                $shareLines[] = '';
            }
            $shareLines[] = "────────────────────────────────────────";
            $shareLines[] = route('teams.league-players', ['league' => $league->slug]);
            $shareText = trim(implode("\n", array_filter($shareLines, fn ($line) => $line !== null)));
        @endphp
        <textarea id="share-text-{{ $league->id }}" class="hidden">{{ $shareText }}</textarea>
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @if(session('warnings'))
            <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 space-y-2">
                <p class="font-semibold">Skipped items during import:</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach((array) session('warnings') as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">League Players</h1>
                    <p class="text-gray-600 mt-2">{{ $league->name }} - Managing {{ $totalPlayersCount }} players</p>
                </div>
                <div class="w-full sm:w-auto flex justify-stretch sm:justify-end">
                    <button type="button"
                            class="whatsapp-share-btn inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition-colors w-full sm:w-auto"
                            data-share-target="share-text-{{ $league->id }}">
                        <svg class="w-4 h-4 mr-2" viewBox="0 0 32 32" fill="currentColor">
                            <path d="M16.03 4c-6.63 0-12 5.23-12 11.67 0 2.29.69 4.43 1.89 6.2L4 28l6.33-1.99c1.72.94 3.69 1.47 5.7 1.47 6.63 0 12-5.23 12-11.67C28.03 9.23 22.66 4 16.03 4zm7.05 16.03c-.29.81-1.7 1.59-2.39 1.69-.61.09-1.41.13-2.28-.14-.53-.17-1.21-.39-2.08-.77-3.67-1.58-6.06-5.22-6.23-5.46-.18-.24-1.49-1.98-1.49-3.77 0-1.79.95-2.67 1.29-3.03.34-.36.74-.45.99-.45.25 0 .5.01.72.02.23.01.53-.08.83.63.29.71.99 2.44 1.07 2.62.09.18.14.39.03.63-.11.24-.17.39-.34.6-.18.21-.36.47-.51.63-.17.18-.34.37-.15.73.21.36.95 1.57 2.04 2.55 1.4 1.25 2.57 1.65 2.93 1.83.36.18.57.15.78-.09.21-.24.9-1.05 1.14-1.41.24-.36.48-.3.81-.18.33.12 2.1 1 2.46 1.18.36.18.6.27.69.42.09.15.09.84-.2 1.65z"/>
                        </svg>
                        Share to WhatsApp
                    </button>
                </div>
                <!-- Mobile: 2 buttons per row -->
                <div class="grid grid-cols-2 gap-3 sm:hidden">
                    <a href="{{ route('players.create') }}?league_slug={{ $league->slug }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Player
                    </a>
                    <a href="{{ route('league-players.create', $league) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Add Player
                    </a>
                </div>
                
                <div class="grid grid-cols-2 gap-3 sm:hidden">
                    <a href="{{ route('league-players.bulk-create', $league) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Bulk Add
                    </a>
                    <a href="{{ route('leagues.show', $league) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back
                    </a>
                </div>
                
                <!-- Desktop: Original layout -->
                <div class="hidden sm:flex gap-3">
                    <a href="{{ route('players.create') }}?league_slug={{ $league->slug }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Player
                    </a>
                    <a href="{{ route('league-players.create', $league) }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Add Player to League
                    </a>
                    <a href="{{ route('league-players.bulk-create', $league) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Bulk Add Players
                    </a>
                    <a href="{{ route('leagues.show', $league) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to League
                    </a>
                </div>
            </div>
        </div>

        @auth
            @if(auth()->user()->isAdmin() || auth()->user()->isOrganizerForLeague($league->id))
                <!-- Bulk Base Price Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                    <div class="flex items-start justify-between gap-4 flex-col md:flex-row">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Bulk Base Price</h2>
                            <p class="text-sm text-gray-600 mt-1">Set base price for all players with status <span class="font-medium text-blue-700">Available</span>.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('league-players.bulk-base-price', $league) }}" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        <div>
                            <label for="bulk_base_price" class="block text-sm font-medium text-gray-700 mb-2">New Base Price</label>
                            <input id="bulk_base_price" type="number" name="base_price" min="0" step="1" required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Enter amount">
                        </div>
                        <div class="md:col-span-2 flex items-end">
                            <button type="submit"
                                    onclick="return confirm('Update base price for all available players?');"
                                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors text-sm">
                                Apply New Base Price
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Bulk Role Replacement Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                    <div class="flex items-start justify-between gap-4 flex-col md:flex-row">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Bulk Role Replacement</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                Replace <span class="font-semibold text-indigo-700">{{ $playersWithoutRoleCount }}</span> player(s) currently marked as <span class="font-semibold">No Role</span> with the <span class="font-semibold">All-rounder</span> role.
                            </p>
                        </div>
                        <form method="POST" action="{{ route('league-players.bulk-default-role', $league) }}" class="flex items-end">
                            @csrf
                            <button type="submit"
                                    @if($playersWithoutRoleCount === 0) disabled @endif
                                    onclick="return confirm('Assign the All-rounder role to all players without a role?');"
                                    class="inline-flex items-center px-4 py-2 text-white font-medium rounded-lg transition-colors text-sm {{ $playersWithoutRoleCount === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Replace Roles
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @endauth

        <!-- Filters -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-indigo-500/15 via-purple-500/15 to-blue-500/15 p-1 rounded-2xl shadow-lg">
                @php
                    $statusOptions = [
                        '' => 'All Statuses',
                        'pending' => 'Pending',
                        'available' => 'Available',
                        'sold' => 'Sold',
                        'unsold' => 'Unsold',
                        'skip' => 'Skip',
                    ];

                    $retentionOptions = [
                        'true' => 'Retained',
                        'false' => 'Not Retained',
                    ];

                    $retentionSelection = request('retention');
                    $selectedRetentions = is_array($retentionSelection)
                        ? array_values(array_filter($retentionSelection, fn($value) => $value !== '' && $value !== null))
                        : (($retentionSelection !== null && $retentionSelection !== '') ? [$retentionSelection] : []);

                    $statusActiveClasses = 'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-200';
                    $statusInactiveClasses = 'border-gray-200 text-gray-600 hover:border-indigo-300 hover:text-indigo-700';

                    $retentionActiveClasses = 'bg-green-600 text-white border-green-600 shadow-lg shadow-green-200';
                    $retentionInactiveClasses = 'border-gray-200 text-gray-600 hover:border-green-300 hover:text-green-700';
                @endphp
                <form method="GET" class="bg-white rounded-2xl p-6 space-y-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold uppercase tracking-wide">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Smart Filters
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mt-2">Filter & Search Players</h3>
                            <p class="text-sm text-gray-500">
                                Refine the roster by status, retention, and teams or quickly search by name/email.
                            </p>
                        </div>
                        <div class="w-full lg:w-80">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search players..."
                                    class="pl-11 w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 shadow-sm"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-6 gap-4">
                        <div class="lg:col-span-3 p-4 rounded-xl border border-gray-100 bg-gray-50/80 hover:border-indigo-200 transition-all duration-200">
                            <p class="text-sm font-semibold text-gray-800 mb-3">Status</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach($statusOptions as $value => $label)
                                    @php
                                        $isSelected = ($currentStatusFilter ?? '') === $value;
                                    @endphp
                                    <label
                                        class="filter-chip flex items-center justify-center px-3 py-2 rounded-xl border text-sm font-medium cursor-pointer transition-all duration-200 {{ $isSelected ? $statusActiveClasses : $statusInactiveClasses }}"
                                        data-filter-group="status"
                                        data-active-class="{{ $statusActiveClasses }}"
                                        data-inactive-class="{{ $statusInactiveClasses }}"
                                    >
                                        <input type="radio"
                                               name="status"
                                               value="{{ $value }}"
                                               class="sr-only"
                                               {{ $isSelected ? 'checked' : '' }}>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="lg:col-span-2 p-4 rounded-xl border border-gray-100 bg-gray-50/80 hover:border-indigo-200 transition-all duration-200">
                            <p class="text-sm font-semibold text-gray-800 mb-3">Retention</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($retentionOptions as $value => $label)
                                    @php
                                        $isChecked = in_array($value, $selectedRetentions, true);
                                    @endphp
                                    <label
                                        class="filter-chip flex items-center px-3 py-2 rounded-xl border text-sm font-medium cursor-pointer transition-all duration-200 {{ $isChecked ? $retentionActiveClasses : $retentionInactiveClasses }}"
                                        data-filter-group="retention"
                                        data-active-class="{{ $retentionActiveClasses }}"
                                        data-inactive-class="{{ $retentionInactiveClasses }}"
                                    >
                                        <input type="checkbox"
                                               name="retention[]"
                                               value="{{ $value }}"
                                               class="sr-only"
                                               {{ $isChecked ? 'checked' : '' }}>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Select one or both retention states.</p>
                        </div>

                        <div class="lg:col-span-1 p-4 rounded-xl border border-gray-100 bg-gray-50/80 hover:border-indigo-200 transition-all duration-200">
                            <label for="team" class="block text-sm font-semibold text-gray-800 mb-2">Team</label>
                            <div class="relative">
                                <select name="team" id="team" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Teams</option>
                                    <option value="unassigned" {{ request('team') == 'unassigned' ? 'selected' : '' }}>
                                        Unassigned (Auction Pool)
                                    </option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->slug }}" {{ request('team') == $team->slug ? 'selected' : '' }}>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.832.445l4.5 6a1 1 0 01-.832 1.555H5.5a1 1 0 01-.832-1.555l4.5-6A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-4.5-6A1 1 0 015.5 9h9.999a1 1 0 01.832 1.555l-4.5 6A1 1 0 0110 17z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                            <button type="submit" class="inline-flex items-center justify-center px-5 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Apply Filters
                            </button>
                            <a href="{{ route('league-players.index', $league) }}" class="inline-flex items-center justify-center px-5 py-3 bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                        </div>
                        <p class="text-sm text-gray-500">
                            Showing <span class="font-semibold text-gray-900">{{ $leaguePlayers->total() }}</span> players in {{ $league->name }}
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-2 md:grid-cols-7 gap-4 mb-8">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalPlayersCount }}</p>
                    <p class="text-xs text-gray-500">Total Players</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-gray-900">{{ $unassignedCount ?? 0 }}</p>
                    <p class="text-xs text-indigo-600">Unassigned</p>
                </div>
            </div>
            
            @foreach(['pending' => 'yellow', 'available' => 'blue', 'sold' => 'green', 'unsold' => 'red', 'skip' => 'gray'] as $status => $color)
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-center">
                        <p class="text-2xl font-semibold text-gray-900">{{ $statusCounts[$status] ?? 0 }}</p>
                        <p class="text-xs text-{{ $color }}-600">{{ ucfirst($status) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Players Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($leaguePlayers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sl
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Player
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Team
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Base Price
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Retention
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($leaguePlayers as $index => $leaguePlayer)
                                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('league-players.show', [$league, $leaguePlayer]) }}'">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ($leaguePlayers->currentPage() - 1) * $leaguePlayers->perPage() + $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($leaguePlayer->user && $leaguePlayer->user->photo)
                                                    <img
                                                        src="{{ Storage::url($leaguePlayer->user->photo) }}"
                                                        alt="{{ $leaguePlayer->user->name }}"
                                                        class="h-10 w-10 rounded-full object-cover"
                                                    />
                                                @else
                                                    <img
                                                        src="{{ asset('images/defaultplayer.jpeg') }}"
                                                        alt="{{ $leaguePlayer->user->name }}"
                                                        class="h-10 w-10 rounded-full object-cover"
                                                    />
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $leaguePlayer->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $leaguePlayer->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($leaguePlayer->leagueTeam)
                                            <div class="text-sm text-gray-900">{{ $leaguePlayer->leagueTeam->team->name ?? 'No Team Assigned' }}</div>
                                        @else
                                            <div class="text-sm text-indigo-600 font-medium">Available for Auction</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $playerRoleName = optional(optional($leaguePlayer->user)->position)->name ?? 'Assign Role';
                                            $playerRoleId = optional($leaguePlayer->user)->position_id;
                                        @endphp
                                        @if($canManageLeague && $gamePositions->isNotEmpty())
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-indigo-100 bg-indigo-50 text-sm font-medium text-indigo-700 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                                data-role-button
                                                data-player-name="{{ $leaguePlayer->user->name }}"
                                                data-current-role-id="{{ $playerRoleId }}"
                                                data-role-action="{{ route('league-players.update-role', [$league, $leaguePlayer]) }}"
                                                title="Update role for {{ $leaguePlayer->user->name }}"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 13.5V19h5.5l9.268-9.268a1.5 1.5 0 10-2.121-2.121L4 13.5z" />
                                                </svg>
                                                <span>{{ $playerRoleName }}</span>
                                            </button>
                                        @else
                                            <div class="text-sm text-gray-900">
                                                {{ $playerRoleName }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        ₹{{ number_format($leaguePlayer->base_price) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'available' => 'bg-blue-100 text-blue-800',
                                                'sold' => 'bg-green-100 text-green-800',
                                                'unsold' => 'bg-red-100 text-red-800',
                                                'skip' => 'bg-gray-100 text-gray-800'
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$leaguePlayer->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($leaguePlayer->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($leaguePlayer->retention)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Retained
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                No
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        @if($leaguePlayer->status === 'pending')
                                            <form action="{{ route('league-players.updateStatus', [$league, $leaguePlayer]) }}" 
                                                  method="POST" class="inline" 
                                                  onsubmit="return confirm('Approve this player?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="available">
                                                <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                            </form>
                                            <form action="{{ route('league-players.updateStatus', [$league, $leaguePlayer]) }}" 
                                                  method="POST" class="inline" 
                                                  onsubmit="return confirm('Reject this player?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="unsold">
                                                <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('league-players.show', [$league, $leaguePlayer]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">View</a>
                                        <a href="{{ route('league-players.edit', [$league, $leaguePlayer]) }}" 
                                           class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <form action="{{ route('league-players.destroy', [$league, $leaguePlayer]) }}" 
                                              method="POST" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to remove this player from the league?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $leaguePlayers->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No players</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding a player to this league.</p>
                    <div class="mt-6">
                        <a href="{{ route('league-players.create', $league) }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Add Player
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        @if($canManageLeague && $gamePositions->isNotEmpty())
            <div id="playerRoleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
                    <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" data-role-modal-close>
                        <span class="sr-only">Close</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-500">Player Role</p>
                            <h3 class="text-2xl font-semibold text-gray-900 mt-2">Update Role</h3>
                            <p class="text-sm text-gray-500">Assign a primary role for <span id="rolePlayerName" class="font-semibold text-gray-900">&nbsp;</span>.</p>
                        </div>
                        <form id="playerRoleForm" method="POST" class="space-y-5">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label for="roleSelect" class="text-sm font-medium text-gray-700">Select role</label>
                                <select id="roleSelect" name="position_id" class="mt-2 block w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500" required>
                                    <option value="">Choose a role</option>
                                    @foreach($gamePositions as $position)
                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center justify-end gap-3">
                                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200" data-role-modal-close>Cancel</button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        
</div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const groups = ['status', 'retention'];

    const classListFromString = (value = '') => value.split(' ').filter(Boolean);

    const applyChipStyles = (group) => {
        document.querySelectorAll(`[data-filter-group="${group}"]`).forEach((label) => {
            const input = label.querySelector('input');
            if (!input) return;

            const activeClasses = classListFromString(label.dataset.activeClass);
            const inactiveClasses = classListFromString(label.dataset.inactiveClass);

            label.classList.remove(...activeClasses, ...inactiveClasses);
            label.classList.add(...(input.checked ? activeClasses : inactiveClasses));
        });
    };

    groups.forEach((group) => {
        applyChipStyles(group);
        document.querySelectorAll(`[data-filter-group="${group}"] input`).forEach((input) => {
            input.addEventListener('change', () => applyChipStyles(group));
        });
    });

    document.querySelectorAll('.whatsapp-share-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.shareTarget;
            const source = document.getElementById(targetId);
            if (!source) {
                return;
            }

            const shareText = source.value.trim();
            if (!shareText) {
                return;
            }

            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(shareText)}`;

            if (navigator.share) {
                navigator.share({ text: shareText }).catch(() => window.open(whatsappUrl, '_blank'));
            } else {
                window.open(whatsappUrl, '_blank');
            }
        });
    });

    const roleModal = document.getElementById('playerRoleModal');
    if (roleModal) {
        const body = document.body;
        const roleForm = document.getElementById('playerRoleForm');
        const roleSelect = document.getElementById('roleSelect');
        const rolePlayerName = document.getElementById('rolePlayerName');

        const closeRoleModal = () => {
            roleModal.classList.add('hidden');
            roleModal.classList.remove('flex');
            body.classList.remove('overflow-hidden');
            if (roleForm) {
                roleForm.reset();
            }
        };

        document.querySelectorAll('[data-role-button]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.stopPropagation();
                const { playerName = '', currentRoleId = '', roleAction = '' } = button.dataset;
                if (roleForm && roleAction) {
                    roleForm.action = roleAction;
                }
                if (roleSelect) {
                    roleSelect.value = currentRoleId || '';
                    if (!roleSelect.value) {
                        roleSelect.selectedIndex = 0;
                    }
                }
                if (rolePlayerName) {
                    rolePlayerName.textContent = playerName || 'this player';
                }
                roleModal.classList.remove('hidden');
                roleModal.classList.add('flex');
                body.classList.add('overflow-hidden');
                setTimeout(() => {
                    if (roleSelect) {
                        roleSelect.focus();
                    }
                }, 50);
            });
        });

        roleModal.querySelectorAll('[data-role-modal-close]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                closeRoleModal();
            });
        });

        roleModal.addEventListener('click', (event) => {
            if (event.target === roleModal) {
                closeRoleModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !roleModal.classList.contains('hidden')) {
                closeRoleModal();
            }
        });
    }
});
</script>
@endsection
