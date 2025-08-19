@props(['user', 'variant' => 'default'])

@php
    $primaryRole = $user->roles->first();
@endphp

@if($primaryRole)
    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $variant === 'dark' ? 'bg-white/20 text-white border border-white/30 backdrop-blur-sm' : 'bg-indigo-100 text-indigo-800 border border-indigo-200' }}">
        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            @switch($primaryRole->name)
                @case('organizer')
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                    @break
                @case('owner')
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    @break
                @case('player')
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                    @break
                @default
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
            @endswitch
        </svg>
        {{ ucfirst($primaryRole->name) }}
    </span>
@endif
