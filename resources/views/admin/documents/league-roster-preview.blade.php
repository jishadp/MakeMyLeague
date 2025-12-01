@extends($hideChrome ? 'layouts.printable' : 'layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'League Roster Preview | ' . config('app.name'))

@section('styles')
<style>
    .preview-shell {
        background: #f1f5f9;
    }
    .print-grid {
        display: grid;
        gap: 20px;
    }
    .print-grid.compact-layout {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
    .print-grid.compact-layout.cards-16 {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    .print-grid.compact-layout.cards-25 {
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
    }
    .print-grid.wide-layout {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    .player-card {
        break-inside: avoid;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1.5rem;
    }
    .player-card.is-dense {
        padding: 14px;
        gap: 1rem;
    }
    .player-card.is-5x5 {
        align-items: center;
        text-align: center;
    }
    .player-card.is-wide {
        flex-direction: row;
        align-items: stretch;
    }
    .player-card__header {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    .player-card.is-dense .player-card__header {
        gap: 0.75rem;
    }
    .player-card.is-5x5 .player-card__header {
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }
    .player-card.is-wide .player-card__header {
        flex: 1;
    }
    .player-card__meta {
        display: grid;
        gap: 0.75rem;
    }
    .player-card.is-dense .player-card__meta {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    .player-card.is-5x5 .player-card__meta {
        grid-template-columns: 1fr;
    }
    .player-card.is-wide .player-card__meta {
        width: 240px;
        flex-shrink: 0;
    }
    @media print {
        @page {
            size: A4 portrait;
            margin: 12mm;
        }
        body {
            background: #fff;
        }
        nav,
        header,
        footer,
        .print-controls {
            display: none !important;
        }
        .preview-shell {
            padding: 0;
            background: #fff;
        }
        .print-area {
            box-shadow: none !important;
            border: none !important;
            padding: 12px !important;
        }
        .print-grid.compact-layout.cards-12 {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 12px !important;
        }
        .print-grid.compact-layout.cards-16 {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 8px !important;
        }
        .print-grid.compact-layout.cards-25 {
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: 8px !important;
        }
        .print-grid.wide-layout {
            grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
        }
        .player-card {
            border: 1px solid #c7d2fe !important;
            padding: 14px !important;
            min-height: 230px;
        }
        .print-grid.compact-layout.cards-16 .player-card {
            min-height: 165px;
            padding: 10px !important;
            gap: 10px !important;
        }
        .print-grid.compact-layout.cards-25 .player-card {
            min-height: 170px;
            padding: 10px !important;
            gap: 12px !important;
        }
        .print-grid.compact-layout.cards-16 .player-card__header {
            gap: 10px !important;
        }
        .print-grid.compact-layout.cards-16 .player-card__meta {
            gap: 6px !important;
        }
        .player-card.is-5x5 {
            text-align: center;
        }
        .player-card.is-5x5 .player-card__header {
            flex-direction: column;
            align-items: center;
        }
        .print-grid.compact-layout.cards-12 .player-card:nth-child(12n+1):not(:first-child) {
            page-break-before: always;
        }
        .print-grid.compact-layout.cards-16 .player-card:nth-child(16n+1):not(:first-child) {
            page-break-before: always;
        }
        .print-grid.compact-layout.cards-25 .player-card:nth-child(25n+1):not(:first-child) {
            page-break-before: always;
        }
        .player-card.is-wide {
            min-height: auto;
        }
    }
</style>
@endsection

@section('content')
@php
    $layoutVariant = $layout ?? 'grid';
    $isWideLayout = $layoutVariant === 'wide';
    $cardsPerPage = in_array($cardsPerPage ?? null, [12, 16, 25], true) ? (int) $cardsPerPage : 12;
    $gridLayoutClass = $isWideLayout ? 'wide-layout' : 'compact-layout cards-' . $cardsPerPage;
    $cardModifierClass = (!$isWideLayout && in_array($cardsPerPage, [16, 25], true)) ? 'is-dense' : '';
    $isFiveByFive = !$isWideLayout && $cardsPerPage === 25;
    $cardLayoutClass = $isWideLayout
        ? 'is-wide'
        : trim($cardModifierClass . ($isFiveByFive ? ' is-5x5' : ''));
    $filterQuery = collect($filters ?? [])->filter(fn ($value) => filled($value))->all();
@endphp
<div class="min-h-screen bg-gray-50 py-10">
    <div class="preview-shell max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="print-controls bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.45em] text-indigo-500">Roster Preview</p>
                <h1 class="text-2xl font-bold text-slate-900 mt-2">
                    {{ $leagueBadge ?? ($league?->name ?? 'All League Players') }}
                </h1>
                <div class="mt-2 flex flex-wrap gap-2 text-sm text-slate-600">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 font-semibold text-xs">
                        {{ $retentionFilterLabel }}
                    </span>
                    <span>Total players: <strong>{{ number_format($playerCount) }}</strong></span>
                    <span>Generated {{ $generatedAt->format('d M Y, g:i A') }}</span>
                    @if($searchTerm)
                        <span>Search “{{ $searchTerm }}”</span>
                    @endif
                </div>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <a href="{{ $backUrl }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:border-indigo-200 hover:text-indigo-600 transition-colors">
                    Back to Filters
                </a>
                <a href="{{ $downloadUrl }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-colors">
                    Download PDF
                </a>
                <div class="inline-flex items-center rounded-xl border border-slate-200 overflow-hidden">
                    @foreach(['grid' => 'Compact Grid', 'wide' => 'Wide Detail'] as $variant => $label)
                        @php
                            $isActive = $layoutVariant === $variant;
                            $variantQuery = $variant === 'grid' ? [] : ['layout' => $variant];
                            $cardsQuery = $cardsPerPage === 12 ? [] : ['cards_per_page' => $cardsPerPage];
                            $layoutUrl = route(
                                'admin.documents.leagues.preview',
                                array_filter(array_merge($filterQuery, $variantQuery, $cardsQuery), fn ($value) => filled($value))
                            );
                        @endphp
                        <a href="{{ $layoutUrl }}"
                           class="px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isActive ? 'bg-indigo-600 text-white' : 'text-slate-500 hover:text-indigo-600' }}"
                           title="Switch to {{ strtolower($label) }} layout">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                <div class="inline-flex items-center rounded-xl border border-slate-200 overflow-hidden">
                    @foreach([12 => '12 cards · 3x4', 16 => '16 cards · 4x4', 25 => '25 cards · 5x5'] as $cardsOption => $label)
                        @php
                            $isCardsActive = $cardsPerPage === $cardsOption;
                            $cardsQuery = $cardsOption === 12 ? [] : ['cards_per_page' => $cardsOption];
                            $layoutQuery = $layoutVariant === 'wide' ? ['layout' => 'wide'] : [];
                            $cardsUrl = route(
                                'admin.documents.leagues.preview',
                                array_filter(array_merge($filterQuery, $cardsQuery, $layoutQuery), fn ($value) => filled($value))
                            );
                        @endphp
                        <a href="{{ $cardsUrl }}"
                           class="px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isCardsActive ? 'bg-slate-800 text-white' : 'text-slate-500 hover:text-indigo-600' }}"
                           title="Show {{ $label }} per page when printing">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="print-area bg-white rounded-2xl shadow border border-slate-100 p-6">
            @if($players->isEmpty())
                <div class="text-center py-12 text-slate-500">
                    No players matched the selected filters.
                </div>
            @else
                <div class="print-grid grid {{ $gridLayoutClass }} print-cards">
                    @foreach($players as $player)
                        <div class="player-card border border-slate-200 rounded-2xl p-5 flex flex-col gap-4 bg-gradient-to-b from-white to-slate-50/40 {{ $cardLayoutClass }}">
                            <div class="player-card__header flex items-center gap-4 {{ $isWideLayout ? 'lg:gap-6' : '' }}">
                                <div class="relative">
                                    @if(!empty($player['photo']))
                                        <img src="{{ $player['photo'] }}" alt="{{ $player['name'] }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200">
                                    @else
                                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-xl font-semibold text-slate-500">
                                            {{ strtoupper(Str::substr($player['name'] ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="absolute -top-2 -left-2 inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-800 text-white text-xs font-bold shadow">
                                        {{ $player['serial'] }}
                                    </span>
                                </div>
                                <div class="flex flex-col gap-1 {{ $isFiveByFive ? 'items-center text-center' : '' }}">
                                    <p class="text-sm uppercase tracking-[0.3em] text-slate-400 font-semibold mb-1">{{ $player['team'] ?? $player['league_short'] ?? $player['league_name'] ?? 'Free Agent' }}</p>
                                    <h3 class="text-lg font-bold text-slate-900">{{ $player['name'] }}</h3>
                                    <div class="flex flex-wrap gap-2 items-center text-xs text-slate-500 {{ $isFiveByFive ? 'justify-center' : '' }}">
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold">{{ $player['role'] }}</span>
                                        @if($player['retained'])
                                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">Retention</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @php
                                $metaGridClass = (!$isWideLayout && !$isFiveByFive && $showLocation && $showMobile) ? 'sm:grid-cols-2' : '';
                            @endphp
                            <div class="player-card__meta grid grid-cols-1 gap-3 text-sm text-slate-600 {{ $metaGridClass }} {{ $isFiveByFive ? 'text-center' : '' }}">
                                @if($showLocation)
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-semibold">Place</p>
                                        <p class="font-semibold">{{ $player['place'] }}</p>
                                    </div>
                                @endif
                                @if($showMobile)
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-semibold">Phone</p>
                                        <p class="font-semibold">{{ $player['phone'] }}</p>
                                    </div>
                                @endif
                                @if(!$showLocation && !$showMobile)
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-semibold">Contacts</p>
                                        <p class="font-semibold text-slate-500">Hidden for this export</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
