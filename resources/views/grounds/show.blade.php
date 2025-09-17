@extends('layouts.app')
@section('title', config('app.name') . ' - ' . $ground->name)


@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('grounds.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Grounds
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $ground->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        
        <!-- Ground Details Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 animate-fadeInUp">
            <!-- Header with status badge -->
            <div class="p-6 pb-0 flex justify-between items-start">
                <h1 class="text-3xl font-bold text-gray-900">{{ $ground->name }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-sm
                        {{ $ground->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $ground->is_available ? 'Available' : 'Not Available' }}
                </span>
            </div>
            
            <!-- Location hierarchy -->
            <div class="px-6 py-3 text-sm">
                <span class="text-gray-600">
                    {{ $ground->localBody->name }}, {{ $ground->district->name }}, {{ $ground->state->name }}
                </span>
            </div>
            
            <!-- Ground image -->
            <div class="bg-gray-200 h-64 flex items-center justify-center">
                @if($ground->image)
                    <img src="{{ asset($ground->image) }}" alt="{{ $ground->name }}" class="w-full h-full object-cover">
                @else
                    <div class="text-gray-500 text-center p-4">
                        <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="text-xl">Ground Image Not Available</span>
                    </div>
                @endif
            </div>
            
            <!-- Details section -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left column -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Ground Details</h2>
                        
                        <div class="space-y-4">
                            @if($ground->address)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Address</h3>
                                    <p class="mt-1 text-gray-800">{{ $ground->address }}</p>
                                </div>
                            @endif
                            
                            @if($ground->capacity)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Capacity</h3>
                                    <p class="mt-1 text-gray-800">{{ number_format($ground->capacity) }} spectators</p>
                                </div>
                            @endif
                            
                            @if($ground->description)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Description</h3>
                                    <p class="mt-1 text-gray-800">{{ $ground->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Right column -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Information</h2>
                        
                        <div class="space-y-4">
                            @if($ground->contact_person)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Contact Person</h3>
                                    <p class="mt-1 text-gray-800">{{ $ground->contact_person }}</p>
                                </div>
                            @endif
                            
                            @if($ground->contact_phone)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Contact Phone</h3>
                                    <p class="mt-1 text-gray-800">{{ $ground->contact_phone }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back button -->
        <div class="flex justify-start mb-8">
            <a href="{{ route('grounds.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Grounds
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
