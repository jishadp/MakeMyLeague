@extends('layouts.app')

@section('title', 'Printable Player Card - ' . $player->name)

@section('styles')
    @include('admin.documents.partials.card-styles')
@endsection

@section('content')
@php
    $backQuery = array_filter([
        'search' => $filters['search'] ?? null,
        'league_id' => $filters['league_id'] ?? null,
    ], fn ($value) => filled($value));
    $backRoute = route('admin.documents.index', $backQuery);
    $downloadRoute = route('admin.documents.players.download', array_merge(['player' => $player->slug], $backQuery));
@endphp
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-indigo-500">Document Preview</p>
                <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $player->name }}</h1>
                <p class="text-slate-500 mt-2">Review the live player card before exporting it as a PDF.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ $backRoute }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:border-indigo-200 hover:text-indigo-600 transition-colors">
                    Back to documents
                </a>
                <a href="{{ $downloadRoute }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-indigo-50 font-semibold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-colors">
                    Download PDF
                </a>
            </div>
        </div>

        <div class="player-doc-wrapper rounded-3xl">
            @include('admin.documents.partials.card', [
                'photoSource' => $photoWebUrl,
                'generatedAt' => $generatedAt,
            ])
        </div>
    </div>
</div>
@endsection
