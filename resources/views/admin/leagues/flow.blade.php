@extends('layouts.app')

@section('title', 'League Progress Flow - ' . $league->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="mb-6 md:mb-8">
            <div class="flex flex-col gap-4 mb-6">
                <a href="{{ route('admin.leagues.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold transition-colors w-fit">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Leagues
                </a>
                
                <div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-2">
                        League Progress Flow
                    </h1>
                    <p class="text-base sm:text-lg md:text-xl text-gray-600">
                        {{ $league->name }} <span class="hidden sm:inline text-gray-400">•</span> 
                        <span class="block sm:inline mt-1 sm:mt-0">Season {{ $league->season }}</span>
                    </p>
                </div>
                
                <!-- Action Buttons - Mobile: Full Width, Desktop: Inline -->
                <div class="grid grid-cols-2 sm:flex gap-2 sm:gap-3">
                    <a href="{{ route('leagues.show', $league) }}" class="inline-flex items-center justify-center px-3 sm:px-5 py-2 sm:py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg sm:rounded-xl text-sm sm:text-base font-bold shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="hidden sm:inline">View League</span>
                        <span class="sm:hidden">View</span>
                    </a>
                    <a href="{{ route('admin.leagues.show', $league) }}" class="inline-flex items-center justify-center px-3 sm:px-5 py-2 sm:py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg sm:rounded-xl text-sm sm:text-base font-bold shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Admin Details</span>
                        <span class="sm:hidden">Admin</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Overall Progress Card -->
        <div class="bg-gradient-to-br from-white to-indigo-50 rounded-2xl sm:rounded-3xl shadow-2xl p-4 sm:p-6 md:p-8 mb-6 md:mb-8 border border-indigo-100">
            <div class="grid md:grid-cols-2 gap-6 md:gap-8 mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-900 mb-3">Overall Completion</h2>
                    <div class="space-y-2">
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="text-sm sm:text-base text-gray-600 font-medium">Current Stage:</span>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs sm:text-sm font-bold">
                                {{ $currentStage['name'] }}
                            </span>
                        </div>
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="text-sm sm:text-base text-gray-600 font-medium">Status:</span>
                            <span class="px-3 py-1 {{ $league->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-xs sm:text-sm font-bold">
                                {{ ucfirst($league->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-center md:justify-end">
                    <div class="relative">
                        <svg class="transform -rotate-90 w-32 h-32 sm:w-40 sm:h-40">
                            <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="10" fill="none" class="sm:hidden" />
                            <circle cx="64" cy="64" r="56" stroke="url(#gradient)" stroke-width="10" fill="none"
                                    stroke-dasharray="{{ 2 * 3.14159 * 56 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 56 * (1 - $completionPercentage / 100) }}"
                                    stroke-linecap="round" class="transition-all duration-1000 sm:hidden" />
                            <circle cx="80" cy="80" r="70" stroke="#e5e7eb" stroke-width="12" fill="none" class="hidden sm:block" />
                            <circle cx="80" cy="80" r="70" stroke="url(#gradient)" stroke-width="12" fill="none"
                                    stroke-dasharray="{{ 2 * 3.14159 * 70 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 70 * (1 - $completionPercentage / 100) }}"
                                    stroke-linecap="round" class="transition-all duration-1000 hidden sm:block" />
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#6366f1;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900">{{ $completionPercentage }}%</span>
                            <span class="text-xs sm:text-sm text-gray-600 font-medium">Complete</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="relative h-4 sm:h-6 bg-gray-200 rounded-full overflow-hidden shadow-inner">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 transition-all duration-1000 ease-out" 
                     style="width: {{ $completionPercentage }}%">
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs sm:text-sm font-bold text-white drop-shadow-lg">{{ $completionPercentage }}% Complete</span>
                </div>
            </div>

            @if($completionPercentage >= 100)
                <div class="mt-4 sm:mt-6 p-4 sm:p-5 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-xl sm:rounded-2xl">
                    <div class="flex items-start sm:items-center gap-3 sm:gap-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-green-900">🎉 League Ready for Completion!</h3>
                            <p class="text-sm sm:text-base text-green-700 mt-1">All required stages have been completed. The league is ready to proceed to the final stage.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Progress Flow Diagram -->
        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl p-4 sm:p-6 md:p-8 mb-6 md:mb-8">
            <h2 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-900 mb-6 md:mb-8 flex items-center">
                <div class="p-2 sm:p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="hidden sm:inline">Progress Flow Diagram</span>
                <span class="sm:hidden">Flow Diagram</span>
            </h2>

            <div class="space-y-6">
                @php
                    $stageNumber = 0;
                    $stageColors = [
                        'blue' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600', 'light' => 'bg-blue-50', 'border' => 'border-blue-300', 'gradient' => 'from-blue-400 to-blue-600'],
                        'indigo' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'light' => 'bg-indigo-50', 'border' => 'border-indigo-300', 'gradient' => 'from-indigo-400 to-indigo-600'],
                        'purple' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-600', 'light' => 'bg-purple-50', 'border' => 'border-purple-300', 'gradient' => 'from-purple-400 to-purple-600'],
                        'green' => ['bg' => 'bg-green-500', 'text' => 'text-green-600', 'light' => 'bg-green-50', 'border' => 'border-green-300', 'gradient' => 'from-green-400 to-green-600'],
                        'yellow' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-600', 'light' => 'bg-yellow-50', 'border' => 'border-yellow-300', 'gradient' => 'from-yellow-400 to-yellow-600'],
                        'pink' => ['bg' => 'bg-pink-500', 'text' => 'text-pink-600', 'light' => 'bg-pink-50', 'border' => 'border-pink-300', 'gradient' => 'from-pink-400 to-pink-600'],
                    ];
                @endphp

                @foreach($progressStages as $stageKey => $stage)
                    @php
                        $stageNumber++;
                        $isOptional = isset($stage['optional']) && $stage['optional'];
                        $colors = $stageColors[$stage['color']] ?? $stageColors['blue'];
                        $isCurrent = $currentStage['key'] === $stageKey;
                    @endphp

                    <!-- Stage Card -->
                    <div class="relative group">
                        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 p-4 sm:p-6 rounded-xl sm:rounded-2xl border-2 transition-all duration-300 hover:shadow-2xl 
                            {{ $stage['completed'] ? 'bg-gradient-to-r from-green-50 to-emerald-50 border-green-400' : ($isOptional ? 'bg-gray-50 border-gray-300' : $colors['light'] . ' ' . $colors['border']) }}
                            {{ $isCurrent ? 'ring-2 sm:ring-4 ring-indigo-400 ring-opacity-50 shadow-xl' : '' }}">
                            
                            <!-- Stage Number/Icon -->
                            <div class="flex-shrink-0 flex sm:block justify-center">
                                <div class="relative">
                                    @if($stage['completed'])
                                        <!-- Completed Icon -->
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-xl">
                                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <!-- Stage Number -->
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl {{ $colors['bg'] }} flex items-center justify-center shadow-xl">
                                            <span class="text-2xl sm:text-3xl font-black text-white">{{ $stageNumber }}</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Current Stage Indicator -->
                                    @if($isCurrent)
                                        <div class="absolute -top-1 sm:-top-2 -right-1 sm:-right-2">
                                            <span class="flex h-4 w-4 sm:h-5 sm:w-5">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-4 w-4 sm:h-5 sm:w-5 bg-indigo-500 border-2 border-white"></span>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Stage Content -->
                            <div class="flex-1">
                                <!-- Stage Header -->
                                <div class="flex flex-col gap-3 mb-4">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                        <div class="flex-1">
                                            <div class="flex items-center flex-wrap gap-2 mb-2">
                                                <h3 class="text-lg sm:text-xl md:text-2xl font-black text-gray-900">
                                                    {{ $stage['name'] }}
                                                </h3>
                                                @if($isOptional)
                                                    <span class="px-2 sm:px-3 py-0.5 sm:py-1 bg-gray-200 text-gray-700 text-xs font-bold rounded-full uppercase">Optional</span>
                                                @endif
                                                @if($isCurrent)
                                                    <span class="px-2 sm:px-3 py-0.5 sm:py-1 bg-indigo-500 text-white text-xs font-bold rounded-full uppercase animate-pulse">Current</span>
                                                @endif
                                            </div>
                                            <p class="text-sm sm:text-base text-gray-600">{{ $stage['description'] }}</p>
                                        </div>
                                        
                                        <!-- Progress Percentage -->
                                        <div class="flex-shrink-0 text-center sm:text-right">
                                            <div class="text-3xl sm:text-4xl md:text-5xl font-black {{ $stage['completed'] ? 'text-green-600' : $colors['text'] }}">
                                                {{ $stage['progress'] }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="relative h-4 sm:h-5 bg-gray-200 rounded-full overflow-hidden shadow-inner mb-3 sm:mb-4">
                                    <div class="absolute inset-0 bg-gradient-to-r {{ $stage['completed'] ? 'from-green-500 to-emerald-600' : $colors['gradient'] }} transition-all duration-1000 ease-out" 
                                         style="width: {{ $stage['progress'] }}%">
                                    </div>
                                </div>

                                <!-- Stage Details -->
                                <div class="flex flex-wrap items-center gap-3 sm:gap-6 text-xs sm:text-sm">
                                    <!-- Count Info -->
                                    @if(isset($stage['current']) && isset($stage['required']))
                                        <div class="flex items-center px-3 sm:px-4 py-1.5 sm:py-2 {{ $colors['light'] }} rounded-lg">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="font-bold text-gray-900">{{ $stage['current'] }}</span>
                                            <span class="mx-1 text-gray-500">/</span>
                                            <span class="font-bold text-gray-700">{{ $stage['required'] }}</span>
                                            <span class="ml-1 text-gray-600 hidden sm:inline">Complete</span>
                                        </div>
                                    @endif

                                    <!-- Status Badge -->
                                    @if($stage['completed'])
                                        <div class="flex items-center px-3 sm:px-4 py-1.5 sm:py-2 bg-green-100 text-green-700 rounded-lg font-bold">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="hidden sm:inline">Stage Complete</span>
                                            <span class="sm:hidden">Complete</span>
                                        </div>
                                    @elseif($stage['progress'] > 0)
                                        <div class="flex items-center px-3 sm:px-4 py-1.5 sm:py-2 {{ $colors['light'] }} {{ $colors['text'] }} rounded-lg font-bold">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            In Progress
                                        </div>
                                    @else
                                        <div class="flex items-center px-3 sm:px-4 py-1.5 sm:py-2 bg-gray-100 text-gray-500 rounded-lg font-bold">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Not Started
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Arrow Connector -->
                        @if(!$loop->last)
                            <div class="flex justify-center py-3 sm:py-4">
                                <div class="flex flex-col items-center">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 {{ $stage['completed'] ? 'text-green-500' : 'text-gray-300' }} animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Detailed Statistics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 md:mb-8">
            <!-- Teams Statistics -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 border border-indigo-100 hover:shadow-2xl transition-shadow">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="p-2 sm:p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-black text-gray-900">Teams</h3>
                </div>
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <span class="text-gray-700 font-semibold text-sm sm:text-base md:text-lg">Registered Teams</span>
                        <div class="text-left sm:text-right">
                            <span class="text-3xl sm:text-4xl font-black text-indigo-600">{{ $teamsCurrent }}</span>
                            <span class="text-xl sm:text-2xl text-gray-400 mx-1 sm:mx-2">/</span>
                            <span class="text-2xl sm:text-3xl font-bold text-gray-600">{{ $teamsRequired }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Players Statistics -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 border border-purple-100 hover:shadow-2xl transition-shadow">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="p-2 sm:p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-black text-gray-900">Players</h3>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <span class="text-gray-700 font-semibold text-sm sm:text-base md:text-lg">Registered Players</span>
                        <div class="text-left sm:text-right">
                            <span class="text-3xl sm:text-4xl font-black text-purple-600">{{ $playersCurrent }}</span>
                            <span class="text-xl sm:text-2xl text-gray-400 mx-1 sm:mx-2">/</span>
                            <span class="text-2xl sm:text-3xl font-bold text-gray-600">{{ $playersRequired }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auction Statistics -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 border border-green-100 hover:shadow-2xl transition-shadow">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="p-2 sm:p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-black text-gray-900">Auction</h3>
                </div>
                <div class="grid grid-cols-3 gap-2 sm:gap-4">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                        <div class="text-2xl sm:text-3xl font-black text-green-600 mb-1">{{ $auctionSold }}</div>
                        <div class="text-xs sm:text-sm text-gray-600 font-semibold">Sold</div>
                    </div>
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                        <div class="text-2xl sm:text-3xl font-black text-red-600 mb-1">{{ $auctionUnsold }}</div>
                        <div class="text-xs sm:text-sm text-gray-600 font-semibold">Unsold</div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                        <div class="text-2xl sm:text-3xl font-black text-blue-600 mb-1">{{ $auctionAvailable }}</div>
                        <div class="text-xs sm:text-sm text-gray-600 font-semibold">Available</div>
                    </div>
                </div>
            </div>

            <!-- Fixtures Statistics -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 border border-yellow-100 hover:shadow-2xl transition-shadow">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="p-2 sm:p-3 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-black text-gray-900">Fixtures</h3>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:gap-4">
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                        <div class="text-2xl sm:text-3xl font-black text-yellow-600 mb-1">{{ $fixturesTotal }}</div>
                        <div class="text-xs sm:text-sm text-gray-600 font-semibold">Total</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                        <div class="text-2xl sm:text-3xl font-black text-green-600 mb-1">{{ $fixturesCompleted }}</div>
                        <div class="text-xs sm:text-sm text-gray-600 font-semibold">Done</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finance Statistics (Optional) -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 mb-6 md:mb-8 border border-pink-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 sm:mb-6">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg sm:rounded-xl mr-3 sm:mr-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-black text-gray-900">Finance</h3>
                </div>
                <span class="px-3 sm:px-4 py-1 sm:py-2 bg-gray-100 text-gray-600 text-xs sm:text-sm font-bold rounded-full uppercase w-fit">Optional</span>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg sm:rounded-xl p-3 sm:p-5">
                    <div class="text-xs sm:text-sm text-gray-600 font-semibold mb-1 sm:mb-2">Income Records</div>
                    <div class="text-2xl sm:text-3xl font-black text-green-600">{{ $financeIncome }}</div>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg sm:rounded-xl p-3 sm:p-5">
                    <div class="text-xs sm:text-sm text-gray-600 font-semibold mb-1 sm:mb-2">Expense Records</div>
                    <div class="text-2xl sm:text-3xl font-black text-red-600">{{ $financeExpenses }}</div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg sm:rounded-xl p-3 sm:p-5">
                    <div class="text-xs sm:text-sm text-gray-600 font-semibold mb-1 sm:mb-2">Total Income</div>
                    <div class="text-lg sm:text-2xl font-black text-blue-600">₹{{ number_format($financeTotalIncome / 1000, 0) }}K</div>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg sm:rounded-xl p-3 sm:p-5">
                    <div class="text-xs sm:text-sm text-gray-600 font-semibold mb-1 sm:mb-2">Total Expenses</div>
                    <div class="text-lg sm:text-2xl font-black text-purple-600">₹{{ number_format($financeTotalExpenses / 1000, 0) }}K</div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
@keyframes bounce {
    0%, 100% { 
        transform: translateY(0); 
    }
    50% { 
        transform: translateY(-12px); 
    }
}

.animate-bounce {
    animation: bounce 2s ease-in-out infinite;
}

@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

.animate-ping {
    animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .7;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endsection
