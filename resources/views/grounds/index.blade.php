@extends('layouts.app')

@section('title', 'Cricket Grounds | CricBid')

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Cricket Grounds
            </h1>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-8 animate-fadeInUp">
            <form action="{{ route('grounds.index') }}" method="GET">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Grounds</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- State Filter -->
                    <div>
                        <label for="state_id" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <select name="state_id" id="state_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                onchange="this.form.submit()">
                            <option value="">All States</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- District Filter -->
                    <div>
                        <label for="district_id" class="block text-sm font-medium text-gray-700 mb-1">District</label>
                        <select name="district_id" id="district_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                onchange="this.form.submit()">
                            <option value="">All Districts</option>
                            @if(request('state_id'))
                                @foreach($activeDistricts as $district)
                                    <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            @else
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <!-- Local Body Filter -->
                    <div>
                        <label for="localbody_id" class="block text-sm font-medium text-gray-700 mb-1">Local Body</label>
                        <select name="localbody_id" id="localbody_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                onchange="this.form.submit()">
                            <option value="">All Local Bodies</option>
                            @if(request('district_id'))
                                @foreach($activeLocalBodies as $localBody)
                                    <option value="{{ $localBody->id }}" {{ request('localbody_id') == $localBody->id ? 'selected' : '' }}>
                                        {{ $localBody->name }}
                                    </option>
                                @endforeach
                            @else
                                @foreach($localBodies as $localBody)
                                    <option value="{{ $localBody->id }}" {{ request('localbody_id') == $localBody->id ? 'selected' : '' }}>
                                        {{ $localBody->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Capacity Filter -->
                    <div>
                        <label for="min_capacity" class="block text-sm font-medium text-gray-700 mb-1">Minimum Capacity</label>
                        <select name="min_capacity" id="min_capacity" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                onchange="this.form.submit()">
                            <option value="">Any Capacity</option>
                            <option value="1000" {{ request('min_capacity') == 1000 ? 'selected' : '' }}>1,000+</option>
                            <option value="2000" {{ request('min_capacity') == 2000 ? 'selected' : '' }}>2,000+</option>
                            <option value="3000" {{ request('min_capacity') == 3000 ? 'selected' : '' }}>3,000+</option>
                            <option value="4000" {{ request('min_capacity') == 4000 ? 'selected' : '' }}>4,000+</option>
                            <option value="5000" {{ request('min_capacity') == 5000 ? 'selected' : '' }}>5,000+</option>
                        </select>
                    </div>
                    
                    <!-- Availability Filter -->
                    <div>
                        <label for="available" class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                        <select name="available" id="available" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="1" {{ request('available') == '1' ? 'selected' : '' }}>Available</option>
                            <option value="0" {{ request('available') == '0' ? 'selected' : '' }}>Not Available</option>
                        </select>
                    </div>
                    
                    <!-- Sort By -->
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <div class="flex gap-2">
                            <select name="sort_by" id="sort_by" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                <option value="capacity" {{ request('sort_by') == 'capacity' ? 'selected' : '' }}>Capacity</option>
                            </select>
                            <select name="sort_dir" id="sort_dir" class="w-28 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Asc</option>
                                <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Desc</option>
                            </select>
                            <button type="submit" class="bg-indigo-600 text-white rounded-lg px-4 hover:bg-indigo-700 transition-colors">
                                Sort
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Reset Filters -->
                <div class="flex justify-end">
                    <a href="{{ route('grounds.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        Reset Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- No Grounds -->
        @if($grounds->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl shadow-md p-8 text-center animate-fadeInUp">
                <p class="text-gray-600 mb-4">No grounds found matching your criteria.</p>
                <a href="{{ route('grounds.index') }}"
                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                    View all grounds
                </a>
            </div>
        @else
            <!-- Ground Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($grounds as $ground)
                    <div class="bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden 
                                hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                        <div class="bg-gray-200 h-48 flex items-center justify-center">
                            @if($ground->image)
                                <img src="{{ asset($ground->image) }}" alt="{{ $ground->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-gray-500 text-center p-4">
                                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span>Ground Image</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-5">
                            <!-- Title & Status Badge -->
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-gray-900">{{ $ground->name }}</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-sm
                                            {{ $ground->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $ground->is_available ? 'Available' : 'Not Available' }}
                                </span>
                            </div>
                            
                            <!-- Details -->
                            <div class="space-y-3 text-sm text-gray-600">
                                <p><span class="font-medium">📍 Location:</span> {{ $ground->localBody->name }}, {{ $ground->district->name }}</p>
                                @if($ground->capacity)
                                    <p><span class="font-medium">👥 Capacity:</span> {{ number_format($ground->capacity) }} spectators</p>
                                @endif
                                @if($ground->contact_person)
                                    <p><span class="font-medium">👤 Contact:</span> {{ $ground->contact_person }}</p>
                                @endif
                                @if($ground->contact_phone)
                                    <p><span class="font-medium">📞 Phone:</span> {{ $ground->contact_phone }}</p>
                                @endif
                            </div>
                            
                            <!-- View Details -->
                            <div class="mt-6 flex justify-end items-center">
                                <a href="{{ route('grounds.show', $ground) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $grounds->links() }}
            </div>
        @endif
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
