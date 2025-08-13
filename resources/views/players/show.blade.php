@extends('layouts.app')

@section('title', $player->name . ' | CricBid')

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('players.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Players
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $player->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        
        <!-- Player Profile Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 animate-fadeInUp">
            <!-- Header -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 text-white">
                <div class="flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 rounded-full p-4">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ $player->name }}</h1>
                        <p class="text-indigo-100 text-lg">{{ $player->role->name ?? 'Cricket Player' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Details section -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left column -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Player Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Full Name</h3>
                                <p class="mt-1 text-gray-800">{{ $player->name }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Playing Role</h3>
                                <p class="mt-1 text-gray-800">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $player->role->name ?? 'N/A' }}
                                    </span>
                                </p>
                            </div>
                            
                            @if($player->email)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Email</h3>
                                    <p class="mt-1 text-gray-800">{{ $player->email }}</p>
                                </div>
                            @endif
                            
                            @if($player->localBody)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Location</h3>
                                    <p class="mt-1 text-gray-800">{{ $player->localBody->name }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Right column -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Information</h2>
                        
                        <div class="space-y-4">
                            @if($player->mobile)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Mobile Number</h3>
                                    <p class="mt-1 text-gray-800">{{ $player->mobile }}</p>
                                </div>
                            @endif
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Member Since</h3>
                                <p class="mt-1 text-gray-800">{{ $player->created_at->format('F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Role Description -->
                @if($player->role)
                    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">About {{ $player->role->name }}</h3>
                        <p class="text-gray-600">
                            @switch($player->role->name)
                                @case('Batter')
                                    Specializes in scoring runs and building partnerships. Key responsibility is to face deliveries and accumulate runs for the team.
                                    @break
                                @case('Bowler')
                                    Responsible for delivering the ball to dismiss batters and restrict scoring. Focuses on taking wickets and maintaining pressure.
                                    @break
                                @case('All-Rounder')
                                    A versatile player who contributes significantly with both bat and ball. Provides balance to the team composition.
                                    @break
                                @case('Wicket-Keeper Batter')
                                    Combines wicket-keeping duties with batting responsibilities. Essential for team's defensive setup and batting order.
                                    @break
                                @default
                                    A cricket player contributing to team success through their specialized skills and role.
                            @endswitch
                        </p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Back button -->
        <div class="flex justify-start mb-8">
            <a href="{{ route('players.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Players
            </a>
        </div>
    </div>
</div>

<!-- Animations -->
<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
</style>
@endsection
