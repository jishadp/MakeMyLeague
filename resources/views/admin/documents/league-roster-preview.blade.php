@extends($hideChrome ? 'layouts.printable' : 'layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'League Team Preview | S1' . config('app.name'))

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
    .player-card.is-wide {
        flex-direction: row;
        align-items: stretch;
    }
    .player-card__header {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    .player-card.is-wide .player-card__header {
        flex: 1;
    }
    .player-card__meta {
        display: grid;
        gap: 0.75rem;
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
        }
        .print-grid.compact-layout {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 10px !important;
        }
        .print-grid.wide-layout {
            grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
        }
        .player-card {
            border: 1px solid #c7d2fe !important;
            padding: 14px !important;
            min-height: 230px;
        }
        .print-grid.compact-layout .player-card:nth-child(12n+1):not(:first-child) {
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
                            $layoutUrl = route('admin.documents.leagues.preview', array_merge($filterQuery, $variantQuery));
                        @endphp
                        <a href="{{ $layoutUrl }}"
                           class="px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isActive ? 'bg-indigo-600 text-white' : 'text-slate-500 hover:text-indigo-600' }}"
                           title="Switch to {{ strtolower($label) }} layout">
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
                <div class="print-grid grid {{ $isWideLayout ? 'wide-layout' : 'compact-layout' }} gap-5 print-cards">
                    @foreach($players as $player)
                        <div class="player-card border border-slate-200 rounded-2xl p-5 flex flex-col gap-4 bg-gradient-to-b from-white to-slate-50/40 {{ $isWideLayout ? 'is-wide' : '' }}">
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
                                <div>
                                    <p class="text-sm uppercase tracking-[0.3em] text-slate-400 font-semibold mb-1">{{ $player['team'] ?? $player['league_short'] ?? $player['league_name'] ?? 'Free Agent' }}</p>
                                    <h3 class="text-lg font-bold text-slate-900">{{ $player['name'] }}</h3>
                                    <div class="flex flex-wrap gap-2 items-center text-xs text-slate-500">
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold">{{ $player['role'] }}</span>
                                        @if($player['retained'])
                                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">Retention</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="player-card__meta grid grid-cols-1 gap-3 text-sm text-slate-600 {{ $isWideLayout ? '' : 'sm:grid-cols-2' }}">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-semibold">Place</p>
                                    <p class="font-semibold">{{ $player['place'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-semibold">Phone</p>
                                    <p class="font-semibold">{{ $player['phone'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
