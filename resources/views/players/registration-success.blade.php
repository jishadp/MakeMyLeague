@extends('layouts.app')

@section('title', 'Registration Completed')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-lg w-full text-center">
        <!-- Success Icon -->
        <div class="flex justify-center mb-6">
            <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <!-- Heading -->
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Registration Completed!</h1>

        <!-- Message -->
        <p class="text-gray-600 mb-6">
            Thank you for registering. Your registration has been successfully received.
        </p>

        <!-- Button -->
        <a href="{{ route('leagues.index') }}"
           class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg shadow-sm hover:bg-green-700 transition-colors text-base">
            Back to Leagues
        </a>
    </div>
</div>
@endsection
