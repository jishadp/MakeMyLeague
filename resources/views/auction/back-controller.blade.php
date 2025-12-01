@extends('layouts.app')

@section('title', 'Auction Control Room - ' . $league->name)

@section('styles')
<style>
    .control-room {
        background: radial-gradient(circle at 10% 20%, #eef2ff, #f8fafc 35%, #f1f5f9);
    }
    .control-bar-actions .home-btn {
        order: 5;
    }
    @media (max-width: 640px) {
        .control-bar-actions .home-btn {
            order: -1;
            margin-left: 0;
        }
    }
    .control-card {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border-radius: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        padding: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .control-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.12);
    }
    .control-header {
        text-align: center;
    }
    .control-header h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .player-card {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .player-thumb {
        width: 82px;
        height: 82px;
        border-radius: 1.25rem;
        overflow: hidden;
        flex-shrink: 0;
        background: #e2e8f0;
    }
    .player-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .player-details {
        flex: 1;
        min-width: 200px;
    }
    .player-bid {
        text-align: right;
    }
    .player-actions {
        width: 100%;
        border-top: 1px dashed #e2e8f0;
        margin-top: 1rem;
        padding-top: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .player-actions__buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .player-actions__buttons button {
        flex: 1;
    }
    .quick-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 0.75rem;
    }
    .quick-button {
        border-radius: 999px;
        padding: 0.85rem 1rem;
        font-weight: 600;
        background: #f1f5f9;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        width: 100%;
    }
    .quick-button.active,
    .quick-button:hover {
        background: #e0e7ff;
        border-color: #6366f1;
        color: #312e81;
    }
    .next-amount-btn {
        width: 100%;
        border-radius: 1.25rem;
        background: linear-gradient(120deg, #4f46e5, #7c3aed);
        color: #fff;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.35rem;
        font-weight: 700;
        font-size: 1rem;
        border: none;
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.35);
    }
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 0.5rem;
    }
    .control-card--wide {
        grid-column: 1 / -1;
    }
    @media (max-width: 640px) {
        .team-grid {
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        }
    }
    .round-table {
        position: relative;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        padding: 1rem;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06) inset, 0 18px 36px rgba(15, 23, 42, 0.06);
    }
    .round-table__orbit {
        position: relative;
        margin: 0 auto;
        max-width: 960px;
        width: 100%;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
        padding: 0.75rem;
        isolation: isolate;
    }
    .round-table__halo {
        display: none;
    }
    .round-table__center {
        display: none;
    }
    .round-table__legend {
        display: none;
    }
    .round-table__seat {
        position: static;
        transform: none;
        width: 100%;
        z-index: 1;
    }
    .round-table__eyebrow {
        font-size: 10px;
        letter-spacing: 0.24em;
        text-transform: uppercase;
        color: #a5b4fc;
        font-weight: 800;
    }
    .round-table__title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #f8fafc;
        margin: 0;
    }
    .round-table__chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.55rem 0.85rem;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        color: #e2e8f0;
    }
    .round-table__meta {
        font-size: 12px;
        color: #cbd5f5;
    }
    .round-table__hub {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02));
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 1rem;
        padding: 0.85rem;
        display: grid;
        gap: 0.6rem;
        text-align: left;
    }
    .round-table__hub .quick-grid {
        gap: 0.5rem;
    }
    .round-table__hub .quick-button {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.12);
        color: #e2e8f0;
    }
    .round-table__hub .quick-button:hover,
    .round-table__hub .quick-button:focus {
        border-color: #c7d2fe;
    }
    .round-table__hub .quick-jump-title {
        color: #cbd5f5;
    }
    .round-table__hub .quick-jump-input {
        background: rgba(255, 255, 255, 0.12);
        color: #e2e8f0;
        border-color: rgba(255, 255, 255, 0.2);
    }
    .round-table__hub .quick-jump-input:focus {
        border-color: #c7d2fe;
        outline: none;
    }
    .round-table__hub .quick-rule-note {
        color: #cbd5f5;
    }
    .round-table__tip {
        font-size: 11px;
        color: #cbd5f5;
        margin: 0;
    }
    .round-table__link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        font-size: 12px;
        font-weight: 700;
        color: #c7d2fe;
        text-decoration: underline;
    }
    .team-pill {
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 0.55rem;
        text-align: left;
        background: #fff;
        min-height: 60px;
        transition: all 0.2s ease;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
    }
    .team-pill--round {
        text-align: center;
        align-items: center;
        padding: 0.85rem;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
    }
    .team-pill.active {
        border-color: #10b981;
        background: #ecfdf5;
    }
    .team-pill--disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }
    .team-pill__details {
        display: none;
        margin-top: 0.35rem;
        font-size: 11px;
        line-height: 1.25;
    }
    .team-pill__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.4rem;
    }
    .team-pill--round .team-pill__header {
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .team-pill__name {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        line-height: 1.1;
    }
    .team-pill__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 11px;
        font-weight: 700;
        color: #0f172a;
        padding: 0.3rem 0.65rem;
        background: #e0e7ff;
        border-radius: 999px;
        border: 1px solid #c7d2fe;
        box-shadow: 0 6px 12px rgba(99, 102, 241, 0.12);
    }
    .team-pill__wallet {
        text-align: right;
        font-size: 11px;
        color: #475569;
        line-height: 1.1;
    }
    .team-pill__meta {
        font-size: 11px;
        line-height: 1.2;
        color: #475569;
    }
    .team-pill--round .team-pill__wallet,
    .team-pill--round .team-pill__meta {
        text-align: center;
    }
    .round-table__subtitle {
        margin-top: 0.75rem;
        text-align: center;
        font-size: 12px;
        color: #475569;
    }
    .round-table--compact {
        padding: 0.75rem;
    }
    .round-table--compact .round-table__orbit {
        --seat-radius: clamp(110px, 26vw, 200px);
        max-width: 720px;
    }
    .round-table--compact .round-table__seat {
        width: 135px;
    }
    .round-table--compact .round-table__center {
        width: min(240px, 42%);
        max-width: 300px;
        padding: 0.75rem 0.9rem;
    }
    .round-table__center--placeholder {
        background: linear-gradient(135deg, #0b1223, #0f172a);
        box-shadow: 0 14px 24px rgba(15, 23, 42, 0.2);
    }
    .seat-sorter {
        border: 1px dashed #e2e8f0;
        border-radius: 1rem;
        padding: 0.75rem;
        background: #fff;
    }
    .seat-sorter__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }
    .seat-sorter__title {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .seat-sorter__hint {
        font-size: 11px;
        color: #475569;
    }
    .seat-sorter__list {
        margin-top: 0.65rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.5rem;
    }
    .seat-sorter__item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.6rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.85rem;
        background: #f8fafc;
        cursor: grab;
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.06);
    }
    .seat-sorter__item:active {
        cursor: grabbing;
    }
    .seat-sorter__handle {
        width: 28px;
        height: 28px;
        border-radius: 0.65rem;
        background: #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        color: #475569;
        flex-shrink: 0;
    }
    .seat-sorter__name {
        flex: 1;
        font-weight: 700;
        color: #0f172a;
        font-size: 12px;
    }
    .seat-sorter__badge {
        font-size: 11px;
        font-weight: 800;
        color: #0f172a;
        background: #e0e7ff;
        border: 1px solid #c7d2fe;
        border-radius: 999px;
        padding: 0.25rem 0.55rem;
    }
    @media (max-width: 1024px) {
        .round-table__orbit {
            aspect-ratio: auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.65rem;
            padding: 0.35rem;
        }
        .round-table__halo {
            display: none;
        }
        .round-table__center {
            width: 100%;
            max-width: none;
            border-radius: 1rem;
            margin-bottom: 0.35rem;
        }
        .round-table__seat {
            position: static;
            transform: none;
            width: 100%;
        }
        .round-table__seat:hover {
            transform: none;
        }
    }
    .hidden {
        display: none !important;
    }
    .control-alert {
        border-radius: 1rem;
        padding: 0.85rem 1rem;
        font-weight: 600;
        text-align: center;
    }
    .control-alert.success {
        background: #ecfdf5;
        color: #065f46;
    }
    .control-alert.error {
        background: #fef2f2;
        color: #991b1b;
    }
    .control-footer {
        position: sticky;
        bottom: 0;
        background: linear-gradient(180deg, rgba(245,245,245,0), #f5f5f5 30%);
        padding-top: 1rem;
    }
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
    .control-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
        padding: 1rem;
    }
    .control-modal__card {
        background: #fff;
        border-radius: 1.25rem;
        padding: 1.5rem;
        width: min(400px, 100%);
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25);
    }
    .control-modal__presets {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .control-modal__presets button {
        flex: 1 1 calc(50% - 0.5rem);
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        text-align: center;
        background: #f8fafc;
    }
    .control-modal__actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.25rem;
    }
    .quick-rules-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(90px, 1fr));
        gap: 0.5rem;
        align-items: end;
    }
    .quick-rules-grid input {
        width: 100%;
    }
    .quick-rule-row {
        display: grid;
        grid-template-columns: repeat(3, minmax(90px, 1fr)) auto;
        gap: 0.5rem;
        align-items: end;
    }
    .quick-rule-row label {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
    }
    .quick-rule-row input {
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.55rem 0.75rem;
        font-weight: 700;
        color: #0f172a;
    }
    .quick-rule-row button {
        align-self: center;
    }
    .quick-rule-actions {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .quick-rule-note {
        font-size: 11px;
        color: #475569;
    }
    .quick-jump-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.35rem;
    }
    .quick-jump-title span {
        font-size: 11px;
        color: #475569;
    }
    .quick-jump-input {
        width: 80px;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.35rem 0.6rem;
        font-size: 12px;
        font-weight: 700;
        text-align: right;
        color: #0f172a;
        background: #fff;
    }
    .player-finder {
        position: relative;
    }
    .player-finder__input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 0.85rem 1rem 0.85rem 2.75rem;
        background: #f8fafc;
        font-weight: 600;
        color: #0f172a;
    }
    .player-finder__input:focus {
        outline: 2px solid #6366f1;
        outline-offset: 2px;
        background: #fff;
    }
    .player-finder__icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }
    .player-finder__results {
        position: static;
        transform: none;
        margin-top: 0.35rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.85rem;
        box-shadow: none;
        z-index: 1;
        max-height: 140px;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 0.5rem;
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(190px, 1fr);
        gap: 0.5rem;
    }
    .player-finder__card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.75rem;
        border: 1px solid #d9e1f2;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff, #f4f6fb);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .player-finder__results .player-finder__card {
        min-width: 180px;
        padding: 0.6rem 0.65rem;
    }
    .player-finder__results .player-finder__action {
        padding: 0.45rem 0.6rem;
        font-size: 12px;
    }
    .player-finder__avatar {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #e2e8f0;
        flex-shrink: 0;
    }
    .player-finder__meta {
        flex: 1;
        min-width: 0;
    }
    .player-finder__name {
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .player-finder__details {
        font-size: 12px;
        color: #475569;
        margin-top: 2px;
    }
    .player-finder__action {
        border-radius: 999px;
        padding: 0.55rem 0.85rem;
        font-weight: 700;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .player-finder__action:hover {
        border-color: #6366f1;
        color: #312e81;
        background: #eef2ff;
    }
    .player-finder__card:hover,
    .player-queue__item:hover {
        border-color: #b8c4ec;
        box-shadow: 0 10px 26px rgba(99, 102, 241, 0.12);
    }
    .player-finder__empty {
        padding: 1rem;
        text-align: center;
        color: #94a3b8;
        font-weight: 600;
    }
    .player-finder__pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.65rem;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        background: #eef2ff;
        color: #312e81;
    }
    .player-finder__random {
        border-radius: 1rem;
        padding: 0.85rem 1rem;
        border: 1px dashed #cbd5e1;
        background: #fff;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    .player-finder__random:hover {
        border-color: #6366f1;
        color: #312e81;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.12);
    }
    .player-queue {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(200px, 1fr);
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }
    .player-queue__item {
        border: 1px solid #d9e1f2;
        border-radius: 1rem;
        padding: 0.6rem 0.65rem;
        background: linear-gradient(135deg, #ffffff, #f4f6fb);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 200px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .player-queue__meta {
        flex: 1;
        min-width: 0;
    }
    .player-queue__actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .auto-start-options {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        font-size: 12px;
        color: #475569;
    }
    .auto-start-options label {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #f8fafc;
        font-weight: 600;
    }
    .control-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        color: #0f172a;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
        flex-wrap: wrap;
    }
    @media (min-width: 768px) {
        .control-bar {
            flex-wrap: nowrap;
        }
        .control-bar-actions {
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 4px;
        }
    }
    .control-bar h1 {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: 0.01em;
        margin: 0;
        color: #0f172a;
    }
    .control-bar-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    @media (max-width: 640px) {
        .control-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .control-bar-actions {
            justify-content: flex-start;
        }
        .control-bar-actions > * {
            flex-shrink: 0;
        }
    }
    .control-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }
    .control-board {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1rem;
        align-items: start;
    }
    @media (max-width: 1200px) {
        .control-board {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
    }
    @media (max-width: 900px) {
        .control-board {
            grid-template-columns: 1fr;
        }
        .control-board > .control-card {
            grid-column: 1 / -1;
        }
        .control-card--wide {
            grid-column: 1 / -1;
        }
    }
    .hide-shell .top-navbar,
    .hide-shell .sidebar,
    .hide-shell footer,
    .hide-shell .bottom-navigation-buttons {
        display: none !important;
    }
    .hide-shell main#top {
        padding-bottom: 0;
    }
</style>
@endsection

@section('content')
@php
    $currentBidAmount = $currentHighestBid->amount ?? ($currentPlayer->base_price ?? 0);
    $bidIncrementValues = collect($bidIncrements ?? [])
        ->map(function ($increment) {
            if (is_numeric($increment)) {
                return (int) $increment;
            }
            if (is_array($increment) && isset($increment['value']) && is_numeric($increment['value'])) {
                return (int) $increment['value'];
            }
            return null;
        })
        ->filter(fn($increment) => !is_null($increment))
        ->values();
    $firstBidIncrement = $bidIncrementValues->first() ?? 0;
@endphp
<div class="control-room min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-6 space-y-6">
        <input type="hidden" id="controller-league-id" value="{{ $league->id }}">
        <input type="hidden" id="controller-league-slug" value="{{ $league->slug }}">
        <input type="hidden" id="controller-player-id" value="{{ $currentPlayer->player->id ?? '' }}">
        <input type="hidden" id="controller-league-player-id" value="{{ $currentPlayer->id ?? '' }}">
        <input type="hidden" id="controller-base-price" value="{{ $currentBidAmount }}">
        <input type="hidden" id="controller-player-base-price" value="{{ $currentPlayer->base_price ?? 0 }}">
        <input type="hidden" id="controller-default-team" value="{{ $currentHighestBid?->league_team_id ?? '' }}">
        <input type="hidden" id="controller-bid-increments" value='@json($bidIncrements)'>
        <input type="hidden" id="controller-bid-action" value="{{ route('auction.call') }}">
        <input type="hidden" id="controller-sold-action" value="{{ route('auction.sold') }}">
        <input type="hidden" id="controller-unsold-action" value="{{ route('auction.unsold') }}">
        <input type="hidden" id="controller-start-action" value="{{ route('auction.start') }}">
        <script id="controller-available-players" type="application/json">
            {!! json_encode($availablePlayers->map(fn ($leaguePlayer) => [
                'id' => $leaguePlayer->id,
                'user_id' => $leaguePlayer->user_id,
                'name' => $leaguePlayer->player->name,
                'role' => $leaguePlayer->player->primaryGameRole?->gamePosition?->name ?? $leaguePlayer->player->position?->name ?? '',
                'base_price' => $leaguePlayer->base_price,
                'photo' => $leaguePlayer->player->photo ? \Illuminate\Support\Facades\Storage::url($leaguePlayer->player->photo) : asset('images/defaultplayer.jpeg'),
            ])->values(), JSON_UNESCAPED_SLASHES) !!}
        </script>
        <script id="controller-unsold-players" type="application/json">
            {!! json_encode($unsoldPlayers->map(fn ($leaguePlayer) => [
                'id' => $leaguePlayer->id,
                'user_id' => $leaguePlayer->user_id,
                'name' => $leaguePlayer->player->name,
                'role' => $leaguePlayer->player->primaryGameRole?->gamePosition?->name ?? $leaguePlayer->player->position?->name ?? '',
                'base_price' => $leaguePlayer->base_price,
                'photo' => $leaguePlayer->player->photo ? \Illuminate\Support\Facades\Storage::url($leaguePlayer->player->photo) : asset('images/defaultplayer.jpeg'),
            ])->values(), JSON_UNESCAPED_SLASHES) !!}
        </script>

        <script>
            document.body?.classList.add('hide-shell');
        </script>

        <div class="control-bar">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-800 font-bold border border-slate-200">
                    <i class="fa-solid fa-sliders-h text-lg" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-600 flex items-center gap-2">
                        <span>{{ $league->isAuctionActive() ? 'Live Auction' : 'Standby' }}</span>
                        <span>•</span>
                        <span>Season {{ $league->season }}</span>
                    </p>
                    <h1>{{ $league->name }} Control</h1>
                    <p class="text-[12px] text-slate-500">{{ $league->game->name ?? 'Game TBA' }} • {{ $league->league_teams_count }} teams</p>
                    <div class="flex flex-wrap items-center gap-1 text-[11px] text-slate-500 mt-1">
                        <span class="font-semibold text-slate-600">Access:</span>
                        @forelse($league->approvedOrganizers as $organizer)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-200 text-[10px] font-bold text-slate-700">
                                    {{ strtoupper(substr($organizer->name, 0, 1)) }}
                                </span>
                                {{ $organizer->name }}
                            </span>
                        @empty
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">Organizers & Admins</span>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="control-bar-actions">
                <button type="button"
                        onclick="copyTextWithFallback('{{ route('auctions.live', $league) }}', 'Live link copied!')"
                        class="p-2 rounded-full bg-slate-800/70 text-white hover:bg-slate-700 transition"
                        title="Copy Live View">
                    <i class="fa-solid fa-link" aria-hidden="true"></i>
                </button>
                <button type="button"
                        onclick="copyTextWithFallback('{{ route('auctions.live.public', $league) }}', 'Broadcast link copied!')"
                        class="p-2 rounded-full bg-slate-800/70 text-white hover:bg-slate-700 transition"
                        title="Copy Broadcast View">
                    <i class="fa-solid fa-tv" aria-hidden="true"></i>
                </button>
                <button type="button"
                        onclick="openWhatsAppShare('Live link: {{ route('auctions.live', $league) }}')"
                        class="p-2 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 transition"
                        title="WhatsApp Live">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                </button>
                <button type="button"
                        onclick="openWhatsAppShare('Broadcast link: {{ route('auctions.live.public', $league) }}')"
                        class="p-2 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 transition"
                        title="WhatsApp Broadcast">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                </button>
                @if(isset($switchableLeagues) && $switchableLeagues->count() > 0)
                    <select id="leagueSwitchSelect" class="rounded-full border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs font-semibold text-white focus:border-indigo-400 focus:outline-none" onchange="if (this.value) window.location.href = this.value;">
                        @foreach($switchableLeagues as $switchLeague)
                            <option value="{{ route('auction.control-room', $switchLeague) }}" @selected($switchLeague->id === $league->id)>
                                {{ $switchLeague->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full bg-slate-800 text-white hover:bg-slate-700 transition home-btn" title="Dashboard">
                    <i class="fa-solid fa-house" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <div id="controller-feedback" class="control-alert hidden"></div>

        @php
            $mismatchedTeams = collect($teams)->filter(function ($team) {
                $diff = $team->balance_audit['difference'] ?? 0;
                return abs($diff) >= 1; // show meaningful rupee differences
            });
        @endphp
        @if($mismatchedTeams->isNotEmpty())
            <div class="control-card space-y-4 border border-amber-200 bg-amber-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Team balance discrepancy detected</p>
                        <p class="text-xs text-amber-700">Calculated from wallet limit minus sold/retained spend. Click Fix to sync stored wallet.</p>
                    </div>
                    <button type="button" class="text-xs font-semibold text-amber-800 hover:text-amber-900" data-toggle-mismatch>Expand</button>
                </div>
                <div id="mismatch-body" class="space-y-3 hidden">
                    @foreach($mismatchedTeams as $team)
                        @php
                            $audit = $team->balance_audit;
                            $expected = number_format($audit['calculated_balance'] ?? 0, 2, '.', '');
                        @endphp
                        <div class="flex flex-col gap-2 rounded-xl border border-amber-200 bg-white p-3 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $team->team?->name ?? 'Team #' . $team->id }}</p>
                                    <p class="text-[11px] text-slate-500">Stored ₹{{ number_format($audit['stored_balance'] ?? 0) }} • Expected ₹{{ number_format($audit['calculated_balance'] ?? 0) }}</p>
                                </div>
                                <span class="text-xs font-semibold text-amber-700">Δ ₹{{ number_format($audit['difference'] ?? 0) }}</span>
                            </div>
                            <form method="POST" action="{{ route('league-teams.updateWallet', [$league, $team]) }}" class="flex items-center gap-3">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="wallet_balance" value="{{ $expected }}">
                                <button type="submit" class="px-3 py-2 rounded-lg bg-amber-600 text-white text-xs font-semibold hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1">
                                    Fix to ₹{{ number_format($audit['calculated_balance'] ?? 0) }}
                                </button>
                                <span class="text-[11px] text-slate-500">Base ₹{{ number_format($audit['base_wallet'] ?? 0) }} · Spent ₹{{ number_format($audit['spent_amount'] ?? 0) }}</span>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="control-board">
            <div class="control-card space-y-4" style="grid-column: 1 / -1;">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-600">Next Player Tools</p>
                        <p class="text-xs text-slate-500">Search and queue an available player or pull a random pick.</p>
                    </div>
                    <span class="player-finder__pill">{{ $auctionStats['available_players'] ?? $availablePlayers->count() }} available</span>
                </div>
                <div id="controller-player-tools" class="space-y-3">
                    <div class="player-finder">
                        <span class="player-finder__icon">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 105.25 5.25a7.5 7.5 0 001.4 11.4z" />
                            </svg>
                        </span>
                        <input type="text" class="player-finder__input" placeholder="Search available players..." data-controller-player-search autocomplete="off">
                        <div id="controller-search-results" class="player-finder__results hidden"></div>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] text-slate-500">Queue players even when no one is currently auctioning.</p>
                        <button type="button" class="player-finder__random" data-controller-random>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l6 6-6 6m10-12h6m-6 12h6" />
                            </svg>
                            Random available player
                        </button>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-600">Queued Players</p>
                            <button type="button" class="text-xs font-semibold text-slate-500 hover:text-slate-700" data-clear-queue>Clear</button>
                        </div>
                        <div class="auto-start-options">
                            <span class="text-[11px] font-semibold text-slate-600">Auto start next after Sold/Unsold:</span>
                            <label>
                                <input type="radio" name="auto-start" value="on" data-auto-start>
                                <span>On</span>
                            </label>
                            <label>
                                <input type="radio" name="auto-start" value="off" data-auto-start>
                                <span>Off</span>
                            </label>
                        </div>
                        <div id="controller-player-queue" class="player-queue"></div>
                        <div id="controller-queue-empty" class="player-finder__empty">No queued players yet. Add from search or random.</div>
                    </div>
                </div>
            </div>
            <div class="control-card player-card">
                <div class="player-thumb">
                    @if($currentPlayer && $currentPlayer->player?->photo)
                        <img src="{{ Storage::url($currentPlayer->player->photo) }}" alt="{{ $currentPlayer->player->name }}">
                    @else
                        <img src="{{ asset('images/defaultplayer.jpeg') }}" alt="Player">
                    @endif
                </div>
                <div class="player-details">
                    <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Current Player</p>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $currentPlayer?->player?->name ?? 'Awaiting selection' }}</h2>
                    <p class="text-sm text-slate-500">
                        @if($currentPlayer)
                            {{ $currentPlayer->player?->primaryGameRole?->gamePosition?->name ?? $currentPlayer->player?->position?->name ?? 'Role TBA' }}
                            · Base ₹{{ number_format($currentPlayer->base_price ?? 0) }}
                        @else
                            Start an auction to see player details here.
                        @endif
                    </p>
                </div>
                <div class="player-bid">
                    <p class="text-xs font-semibold text-slate-500 uppercase">Current Bid</p>
                    <p id="controller-current-bid-label" class="text-3xl font-bold text-emerald-600">{{ $currentPlayer ? '₹' . number_format($currentBidAmount) : '—' }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $currentHighestBid?->leagueTeam?->team?->name ?? 'No bids yet' }}</p>
                </div>
                <div class="player-actions">
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>Manage result for current player</span>
                        <button type="button" class="font-semibold text-indigo-600 hover:text-indigo-700 {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" data-edit-override {{ $currentPlayer ? '' : 'disabled' }}>
                            Override amount
                        </button>
                    </div>
                    <div class="player-actions__buttons">
                        <button type="button" data-controller-sold class="px-6 py-3 rounded-2xl bg-emerald-500 text-white font-semibold shadow-lg hover:bg-emerald-600 transition {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" onclick="markControllerSold(this)" {{ $currentPlayer ? '' : 'disabled' }}>
                            Sold
                        </button>
                        <button type="button" onclick="markControllerUnsold(this)" class="px-6 py-3 rounded-2xl bg-rose-500 text-white font-semibold shadow-lg hover:bg-rose-600 transition {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" {{ $currentPlayer ? '' : 'disabled' }}>
                            Unsold
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400" data-override-label>Current override: None</p>
                </div>
            </div>

            <div class="control-card space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-600">Bid Desk</p>
                        <p class="text-xs text-slate-500">Choose quick amounts and place bids.</p>
                    </div>
                    <button type="button" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700" data-edit-quick {{ $currentPlayer ? '' : 'disabled' }}>
                        Change amount
                    </button>
                </div>
                <p id="selected-team-label" class="text-xs text-slate-600">
                    Selected team: <span data-selected-team>{{ $currentHighestBid?->leagueTeam?->team?->name ?? 'None' }}</span>
                </p>
                <p class="text-xs text-slate-500">
                    Needs <span data-selected-need>0</span> • Reserve <span data-selected-reserve>₹0</span> • Max bid <span data-selected-max>₹0</span>
                </p>
                <input id="controller-custom-amount" type="hidden" data-default-increment="{{ $firstBidIncrement }}" value="{{ $currentBidAmount ?? 0 }}">
                <div class="quick-grid">
                    @if($bidIncrementValues->isNotEmpty())
                        <button type="button" class="quick-button {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" data-quick-trigger {{ $currentPlayer ? '' : 'disabled' }}>
                            +₹{{ number_format($bidIncrementValues->first()) }}
                        </button>
                    @endif
                </div>
                <div class="space-y-1">
                    <div class="quick-jump-title">
                        <p class="text-xs font-semibold text-slate-600">Jump to amount</p>
                        <div class="flex items-center gap-2">
                            <span id="quick-jump-note" class="text-xs text-slate-500">(steps of ₹50)</span>
                            <input id="quick-jump-step" type="number" min="1" step="1" class="quick-jump-input" value="50" aria-label="Jump step in rupees">
                        </div>
                    </div>
                    <div id="quick-jump-grid" class="quick-grid"></div>
                </div>
                <button type="button" onclick="placeControllerBid(this)" class="next-amount-btn {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" {{ $currentPlayer ? '' : 'disabled' }}>
                    <span class="uppercase text-xs tracking-wide text-white/80">Bid</span>
                    <span id="controller-bid-preview" class="text-2xl">₹{{ number_format($currentBidAmount) }}</span>
                </button>
                <p class="text-center text-xs text-slate-400">Queue quick bid amounts, then press Bid to push to the live room.</p>
            </div>

            <div class="control-card control-card--wide space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-600">Choose Team</p>
                        <p class="text-xs text-slate-500">Seat them around the table and tap to assign the bid target.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="round-table__link" data-round-table-toggle>Compact view</button>
                        <a href="{{ route('auction.index', $league) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Open live auction</a>
                    </div>
                </div>
                <input type="hidden" id="controller-team" value="{{ $currentHighestBid?->league_team_id ?? '' }}">
                @php
                    $currentRequiredBid = $currentPlayer ? ($currentBidAmount ?? 0) : 0;
                    $teamCount = max($teams->count(), 1);
        @endphp
                <div class="round-table">
                    <div class="round-table__orbit" data-team-count="{{ $teamCount }}">
                        @foreach($teams as $team)
                            @php
                                $teamMaxBid = max($team->max_bid_cap ?? 0, 0);
                                $teamDisabled = ($team->players_needed ?? 0) === 0 || ($currentPlayer && $teamMaxBid < $currentRequiredBid);
                                $seatAngle = (($loop->index / $teamCount) * 360);
                                $seatIndex = $loop->iteration;
                            @endphp
                            <div class="round-table__seat" data-seat-default="{{ $seatIndex }}">
                                <button type="button"
                                    class="team-pill team-pill--round {{ $currentHighestBid?->league_team_id === $team->id ? 'active' : '' }} {{ $teamDisabled ? 'team-pill--disabled' : '' }}"
                                    data-team-pill="{{ $team->id }}"
                                    data-team-name="{{ $team->team?->name ?? 'Team #' . $team->id }}"
                                    data-team-reserve="{{ $team->reserve_amount }}"
                                    data-team-max="{{ $team->max_bid_cap }}"
                                    data-team-wallet="{{ $team->display_wallet ?? $team->wallet_balance ?? 0 }}"
                                    data-team-needed="{{ $team->players_needed }}"
                                    data-team-disabled="{{ $teamDisabled ? 'true' : 'false' }}"
                                    data-team-details="{{ json_encode([
                                        'players' => $team->sold_players_count,
                                        'retained' => $team->retained_players_count ?? 0,
                                        'wallet' => number_format($team->display_wallet ?? $team->wallet_balance ?? 0),
                                        'needs' => $team->players_needed,
                                        'reserve' => number_format($team->reserve_amount),
                                        'max' => number_format($team->max_bid_cap)
                                    ]) }}"
                                    {{ $teamDisabled ? 'disabled aria-disabled=true' : '' }}>
                                    <span class="team-pill__badge" data-seat-label>Seat {{ str_pad($seatIndex, 2, '0', STR_PAD_LEFT) }}</span>
                                    <div class="team-pill__header">
                                        <p class="team-pill__name" title="{{ $team->team?->name ?? 'Team #' . $team->id }}">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::limit($team->team?->name ?? 'Team #' . $team->id, 14, '')) }}
                                        </p>
                                        <p class="text-[11px] font-semibold text-slate-700">Wallet ₹{{ number_format($team->display_wallet ?? $team->wallet_balance ?? 0) }}</p>
                                        @if($teamDisabled)
                                            <span class="text-[10px] font-semibold text-rose-500">Cap ₹{{ number_format($teamMaxBid) }}</span>
                                        @endif
                                    </div>
                                    <p class="team-pill__meta">Needs {{ $team->players_needed }} • Reserve ₹{{ number_format($team->reserve_amount) }}</p>
                                    <p class="team-pill__meta text-indigo-600 font-semibold">Max ₹{{ number_format($team->max_bid_cap) }}</p>
                                    <div class="team-pill__details" data-team-details-panel></div>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <p class="round-table__subtitle hidden">Tap any team to lock in the bidder.</p>
                </div>
                <div class="seat-sorter" id="seat-sorter">
                    <div class="seat-sorter__header">
                        <p class="seat-sorter__title">Seat order</p>
                        <div class="flex items-center gap-2">
                            <span class="seat-sorter__hint">Drag to match your table layout</span>
                            <button type="button" class="round-table__link" data-seat-sort-toggle>Hide</button>
                        </div>
                    </div>
                    <div id="seat-sort-list" class="seat-sorter__list">
                        @foreach($teams as $team)
                            @php
                                $seatIndex = $loop->iteration;
                            @endphp
                            <div class="seat-sorter__item" draggable="true" data-seat-sort-item data-team-id="{{ $team->id }}" data-seat-default="{{ $seatIndex }}">
                                <span class="seat-sorter__handle">≡</span>
                                <span class="seat-sorter__name">{{ $team->team?->name ?? 'Team #' . $team->id }}</span>
                                <span class="seat-sorter__badge" data-seat-sort-badge>{{ $seatIndex }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="controller-override-amount" value="">
    </div>
</div>
<div id="controller-rules-modal" class="control-modal hidden" role="dialog" aria-modal="true">
    <div class="control-modal__card space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Quick Bid Rules</h3>
                <p class="quick-rule-note">Set increments by bid range. Saved per league in this browser.</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600" data-quick-rules-close aria-label="Close quick bid rules">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <div class="quick-rules-grid text-[11px] font-semibold text-slate-600">
            <span>From (₹)</span>
            <span>To (₹)</span>
            <span>Increment (₹)</span>
        </div>
        <div id="quick-rules-rows" class="space-y-2"></div>
        <p class="quick-rule-note">Ranges are inclusive of “From” and exclusive of “To”. Leave “To” blank to make a range open-ended.</p>
        <div class="quick-rule-actions">
            <button type="button" class="px-3 py-2 rounded-lg border border-slate-200 text-indigo-600 font-semibold hover:bg-slate-50" data-add-quick-rule>Add range</button>
            <div class="flex items-center gap-2">
                <button type="button" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700" data-save-quick-rules>Save rules</button>
                <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50" data-cancel-quick-rules>Cancel</button>
            </div>
        </div>
    </div>
</div>
<div id="controller-unsold-modal" class="control-modal hidden" role="dialog" aria-modal="true">
    <div class="control-modal__card space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Start Unsold Round?</h3>
                <p class="text-sm text-slate-600">All available players are finished. Move to unsold players?</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600" data-unsold-cancel aria-label="Close unsold confirmation">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <div class="space-y-2 text-sm text-slate-600">
            <p>Random picks will use the unsold pool until everyone is completed.</p>
            <p class="text-[12px] text-slate-500">You can start players manually at any time.</p>
        </div>
        <div class="control-modal__actions">
            <button type="button" class="flex-1 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700" data-unsold-confirm>Start Unsold Round</button>
            <button type="button" class="flex-1 px-4 py-2 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50" data-unsold-cancel>Not Now</button>
        </div>
    </div>
</div>
<div id="controller-modal" class="control-modal hidden" role="dialog" aria-modal="true">
    <div class="control-modal__card">
        <h3 class="text-lg font-semibold text-slate-900 mb-4" data-modal-title>Update value</h3>
        <div class="text-xs text-slate-500 mb-2">Quick presets</div>
        <div class="control-modal__presets">
            @foreach([30,50,100,250,500] as $preset)
                <button type="button" data-modal-value="{{ $preset }}">₹{{ number_format($preset) }}</button>
            @endforeach
        </div>
        <input type="number" min="0" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" data-modal-input>
        <div class="control-modal__actions">
            <button type="button" class="flex-1 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700" data-modal-confirm>Save</button>
            <button type="button" class="flex-1 px-4 py-2 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50" data-modal-cancel>Cancel</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function controllerToast(message, type = 'success') {
    const feedback = document.getElementById('controller-feedback');
    if (feedback) {
        feedback.textContent = message;
        feedback.classList.remove('hidden', 'success', 'error');
        feedback.classList.add(type === 'error' ? 'error' : 'success');
        setTimeout(() => feedback.classList.add('hidden'), 3000);
    } else {
        alert(message);
    }
}

function copyTextWithFallback(text, successMessage = 'Link copied!') {
    if (navigator.clipboard?.writeText) {
        navigator.clipboard.writeText(text)
            .then(() => controllerToast(successMessage, 'success'))
            .catch(() => fallbackCopy(text, successMessage));
    } else {
        fallbackCopy(text, successMessage);
    }
}

function fallbackCopy(text, successMessage) {
    const area = document.createElement('textarea');
    area.value = text;
    area.setAttribute('readonly', '');
    area.style.position = 'fixed';
    area.style.opacity = '0';
    document.body.appendChild(area);
    area.select();
    try {
        document.execCommand('copy');
        controllerToast(successMessage, 'success');
    } catch (e) {
        controllerToast('Copy failed. Please copy manually.', 'error');
    }
    document.body.removeChild(area);
}

function openWhatsAppShare(message) {
    const url = `https://wa.me/?text=${encodeURIComponent(message)}`;
    const popup = window.open(url, '_blank');
    if (!popup || popup.closed || typeof popup.closed === 'undefined') {
        controllerToast('Please allow pop-ups to share on WhatsApp.', 'error');
    }
}
</script>
<script>
(function() {
    const controllerBidInput = document.getElementById('controller-custom-amount');
    const controllerBaseInput = document.getElementById('controller-base-price');
    const playerBasePriceInput = document.getElementById('controller-player-base-price');
    const controllerPreview = document.getElementById('controller-bid-preview');
    const controllerTeamSelect = document.getElementById('controller-team');
    const controllerDefaultTeam = document.getElementById('controller-default-team');
    const controllerStartAction = document.getElementById('controller-start-action');
    const playerSearchInput = document.querySelector('[data-controller-player-search]');
    const playerSearchResults = document.getElementById('controller-search-results');
    const randomPlayerButton = document.querySelector('[data-controller-random]');
    const availablePlayersScript = document.getElementById('controller-available-players');
    const unsoldPlayersScript = document.getElementById('controller-unsold-players');
    const playerQueueList = document.getElementById('controller-player-queue');
    const playerQueueEmpty = document.getElementById('controller-queue-empty');
    const clearQueueButton = document.querySelector('[data-clear-queue]');
    const teamPills = document.querySelectorAll('[data-team-pill]');
    const soldButton = document.querySelector('[data-controller-sold]');
    const quickButton = document.querySelector('[data-quick-trigger]');
    const quickEditButton = document.querySelector('[data-edit-quick]');
    const selectedTeamLabel = document.querySelector('[data-selected-team]');
    const overrideButton = document.querySelector('[data-edit-override]');
    const overrideInput = document.getElementById('controller-override-amount');
    const overrideLabel = document.querySelector('[data-override-label]');
    const quickRulesModal = document.getElementById('controller-rules-modal');
    const quickRulesRows = document.getElementById('quick-rules-rows');
    const addQuickRuleBtn = document.querySelector('[data-add-quick-rule]');
    const saveQuickRulesBtn = document.querySelector('[data-save-quick-rules]');
    const cancelQuickRulesBtn = document.querySelector('[data-cancel-quick-rules]');
    const closeQuickRulesBtn = document.querySelector('[data-quick-rules-close]');
    const quickJumpGrid = document.getElementById('quick-jump-grid');
    const quickJumpNote = document.getElementById('quick-jump-note');
    const quickJumpStepInput = document.getElementById('quick-jump-step');
    const selectedNeedLabel = document.querySelector('[data-selected-need]');
    const selectedReserveLabel = document.querySelector('[data-selected-reserve]');
    const selectedMaxLabel = document.querySelector('[data-selected-max]');
    const modal = document.getElementById('controller-modal');
    const modalTitle = modal?.querySelector('[data-modal-title]');
    const modalInput = modal?.querySelector('[data-modal-input]');
    const modalConfirm = modal?.querySelector('[data-modal-confirm]');
    const modalCancel = modal?.querySelector('[data-modal-cancel]');
    const modalPresetButtons = modal?.querySelectorAll('[data-modal-value]');
    const defaultSoldLabel = soldButton ? soldButton.textContent.trim() : 'Sold';
    const leagueIdValue = document.getElementById('controller-league-id')?.value;
    const defaultPlayerPhoto = "{{ asset('images/defaultplayer.jpeg') }}";
    const queueStorageKey = leagueIdValue ? `league_${leagueIdValue}_queued_players` : null;
    const autoStartStorageKey = leagueIdValue ? `league_${leagueIdValue}_auto_start` : null;
    const playerToolsPanel = document.getElementById('controller-player-tools');
    const playerToolsToggle = document.querySelector('[data-toggle-player-tools]');
    const mismatchToggle = document.querySelector('[data-toggle-mismatch]');
    const mismatchBody = document.getElementById('mismatch-body');
    const autoStartRadios = document.querySelectorAll('[data-auto-start]');
    const quickRulesStorageKey = leagueIdValue ? `league_${leagueIdValue}_quick_rules` : null;
    const legacyQuickStorageKey = leagueIdValue ? `league_${leagueIdValue}_quick_increment` : null;
    const quickJumpStorageKey = leagueIdValue ? `league_${leagueIdValue}_jump_step` : null;
    const availableCompletedStorageKey = leagueIdValue ? `league_${leagueIdValue}_completed_available` : null;
    const unsoldCompletedStorageKey = leagueIdValue ? `league_${leagueIdValue}_completed_unsold` : null;
    const availableServedStorageKey = leagueIdValue ? `league_${leagueIdValue}_served_available` : null;
    const unsoldServedStorageKey = leagueIdValue ? `league_${leagueIdValue}_served_unsold` : null;
    const randomPhaseStorageKey = leagueIdValue ? `league_${leagueIdValue}_random_phase` : null;
    const roundTableToggle = document.querySelector('[data-round-table-toggle]');
    const roundTable = document.querySelector('.round-table');
    const roundTableStorageKey = leagueIdValue ? `league_${leagueIdValue}_round_table_compact` : null;
    const roundTableOrbit = document.querySelector('.round-table__orbit');
    const seatSortList = document.getElementById('seat-sort-list');
    const seatOrderStorageKey = leagueIdValue ? `league_${leagueIdValue}_seat_order` : null;
    const seatSorter = document.getElementById('seat-sorter');
    const seatSortToggle = document.querySelector('[data-seat-sort-toggle]');
    const seatSortToggleStorageKey = leagueIdValue ? `league_${leagueIdValue}_seat_sort_hidden` : null;
    const unsoldModal = document.getElementById('controller-unsold-modal');
    const unsoldConfirmButtons = unsoldModal?.querySelectorAll('[data-unsold-confirm]');
    const unsoldCancelButtons = unsoldModal?.querySelectorAll('[data-unsold-cancel]');
    if (quickJumpStepInput && quickJumpStorageKey) {
        const storedStep = Number(localStorage.getItem(quickJumpStorageKey) || 0);
        if (storedStep > 0) {
            quickJumpStepInput.value = storedStep;
        }
    }
    const defaultQuickRules = [
        { min: 1, max: 1000, increment: 50 },
        { min: 1000, max: 3000, increment: 100 },
        { min: 3000, max: 5000, increment: 200 },
    ];
    let quickRules = loadQuickRules();
    let quickIncrementValue = 0;
    quickIncrementValue = getQuickIncrement(Number(controllerBaseInput?.value || 0));
    let modalState = null;
    let availableCompletedSet = loadIdSet(availableCompletedStorageKey);
    let unsoldCompletedSet = loadIdSet(unsoldCompletedStorageKey);
    let availableServedSet = loadIdSet(availableServedStorageKey);
    let unsoldServedSet = loadIdSet(unsoldServedStorageKey);
    let currentRandomPhase = normalizeRandomPhase(loadRandomPhase());
    let pendingAutoStartAfterConfirm = false;
    let availableRandomPool = [];
    let unsoldRandomPool = [];
    initRoundTableSizing();
    initSeatOrdering();
    initSeatSortToggle();
    const teamNameMap = {};
    teamPills.forEach(button => {
        teamNameMap[button.dataset.teamPill] = button.dataset.teamName || button.textContent.trim();
    });

    function formatCurrency(value) {
        const amount = Number(value) || 0;
        return '₹' + amount.toLocaleString('en-IN');
    }

    function loadIdSet(storageKey) {
        if (!storageKey) return new Set();
        try {
            const raw = localStorage.getItem(storageKey);
            if (!raw) return new Set();
            const parsed = JSON.parse(raw);
            return new Set(Array.isArray(parsed) ? parsed.map(String) : []);
        } catch (error) {
            return new Set();
        }
    }

    function saveIdSet(storageKey, setValue) {
        if (!storageKey) return;
        localStorage.setItem(storageKey, JSON.stringify(Array.from(setValue)));
    }

    function loadRandomPhase() {
        if (!randomPhaseStorageKey) return 'available';
        const stored = localStorage.getItem(randomPhaseStorageKey);
        return stored || 'available';
    }

    function initRoundTableSizing() {
        if (!roundTable || !roundTableToggle) return;
        const storedCompact = roundTableStorageKey ? localStorage.getItem(roundTableStorageKey) === '1' : false;
        setRoundTableCompact(storedCompact);
        roundTableToggle.addEventListener('click', () => {
            const next = !roundTable.classList.contains('round-table--compact');
            setRoundTableCompact(next);
            if (roundTableStorageKey) {
                localStorage.setItem(roundTableStorageKey, next ? '1' : '0');
            }
        });
    }

    function setRoundTableCompact(enabled) {
        if (!roundTable) return;
        roundTable.classList.toggle('round-table--compact', !!enabled);
        if (roundTableToggle) {
            roundTableToggle.textContent = enabled ? 'Expand view' : 'Compact view';
        }
    }

    function initSeatOrdering() {
        if (!roundTableOrbit || !seatSortList) return;
        const storedOrder = loadSeatOrder();
        hydrateSeatSortList(storedOrder);
        applySeatOrder(storedOrder);
        enableSeatDrag();
    }

    function initSeatSortToggle() {
        if (!seatSorter || !seatSortToggle) return;
        const hidden = seatSortToggleStorageKey ? localStorage.getItem(seatSortToggleStorageKey) === '1' : false;
        setSeatSorterHidden(hidden);
        seatSortToggle.addEventListener('click', () => {
            const nextHidden = !seatSorter.classList.contains('hidden');
            setSeatSorterHidden(nextHidden);
            if (seatSortToggleStorageKey) {
                localStorage.setItem(seatSortToggleStorageKey, nextHidden ? '1' : '0');
            }
        });
    }

    function setSeatSorterHidden(hidden) {
        if (!seatSorter || !seatSortToggle) return;
        seatSorter.classList.toggle('hidden', hidden);
        seatSortToggle.textContent = hidden ? 'Show' : 'Hide';
    }

    function hydrateSeatSortList(orderMap = {}) {
        if (!seatSortList) return;
        const items = Array.from(seatSortList.querySelectorAll('[data-seat-sort-item]'));
        items.sort((a, b) => {
            const aOrder = Number(orderMap[a.dataset.teamId] || a.dataset.seatDefault || 0);
            const bOrder = Number(orderMap[b.dataset.teamId] || b.dataset.seatDefault || 0);
            return aOrder - bOrder;
        });
        items.forEach(item => seatSortList.appendChild(item));
        refreshSeatSortBadges();
    }

    function enableSeatDrag() {
        let dragItem = null;
        seatSortList.addEventListener('dragstart', (event) => {
            const target = event.target.closest('[data-seat-sort-item]');
            if (!target) return;
            dragItem = target;
            event.dataTransfer.effectAllowed = 'move';
        });
        seatSortList.addEventListener('dragover', (event) => {
            if (!dragItem) return;
            event.preventDefault();
            const target = event.target.closest('[data-seat-sort-item]');
            if (!target || target === dragItem) return;
            const items = Array.from(seatSortList.children);
            const dragIndex = items.indexOf(dragItem);
            const targetIndex = items.indexOf(target);
            if (dragIndex < targetIndex) {
                seatSortList.insertBefore(dragItem, target.nextSibling);
            } else {
                seatSortList.insertBefore(dragItem, target);
            }
        });
        const commit = () => {
            if (!dragItem) return;
            dragItem = null;
            const map = collectSeatOrder();
            saveSeatOrder(map);
            refreshSeatSortBadges();
            applySeatOrder(map);
        };
        seatSortList.addEventListener('drop', (event) => {
            event.preventDefault();
            commit();
        });
        seatSortList.addEventListener('dragend', commit);
    }

    function collectSeatOrder() {
        const map = {};
        if (!seatSortList) return map;
        const items = Array.from(seatSortList.querySelectorAll('[data-seat-sort-item]'));
        items.forEach((item, index) => {
            map[item.dataset.teamId] = index + 1;
        });
        return map;
    }

    function refreshSeatSortBadges() {
        if (!seatSortList) return;
        const items = Array.from(seatSortList.querySelectorAll('[data-seat-sort-item]'));
        items.forEach((item, index) => {
            const badge = item.querySelector('[data-seat-sort-badge]');
            if (badge) {
                badge.textContent = index + 1;
            }
        });
    }

    function loadSeatOrder() {
        if (!seatOrderStorageKey) return {};
        try {
            const parsed = JSON.parse(localStorage.getItem(seatOrderStorageKey) || '{}');
            return typeof parsed === 'object' && parsed !== null ? parsed : {};
        } catch (e) {
            return {};
        }
    }

    function saveSeatOrder(map) {
        if (!seatOrderStorageKey) return;
        localStorage.setItem(seatOrderStorageKey, JSON.stringify(map || {}));
    }

    function applySeatOrder(orderMap = {}) {
        if (!roundTableOrbit) return;
        const seats = Array.from(roundTableOrbit.querySelectorAll('.round-table__seat'));
        seats.sort((a, b) => {
            const aTeam = a.querySelector('[data-team-pill]')?.dataset.teamPill;
            const bTeam = b.querySelector('[data-team-pill]')?.dataset.teamPill;
            const aOrder = Number(orderMap[aTeam] || a.dataset.seatDefault || 0);
            const bOrder = Number(orderMap[bTeam] || b.dataset.seatDefault || 0);
            return aOrder - bOrder;
        });
        seats.forEach((seat, index) => {
            const label = seat.querySelector('[data-seat-label]');
            const orderNumber = index + 1;
            if (label) {
                label.textContent = `Seat ${String(orderNumber).padStart(2, '0')}`;
            }
            roundTableOrbit.appendChild(seat);
        });
    }

    function normalizeRandomPhase(phase) {
        if (phase === 'unsold' || phase === 'completed') return phase;
        return 'available';
    }

    function normalizeQuickRules(rules) {
        return (Array.isArray(rules) ? rules : [])
            .map(rule => ({
                min: Math.max(0, Number(rule.min ?? rule.from ?? 0)),
                max: rule.max === null || rule.max === undefined || rule.max === ''
                    ? null
                    : Math.max(0, Number(rule.max ?? rule.to)),
                increment: Number(rule.increment ?? rule.value ?? 0)
            }))
            .filter(rule => rule.increment > 0)
            .map(rule => {
                if (rule.max !== null && rule.max <= rule.min) {
                    rule.max = null;
                }
                return rule;
            })
            .sort((a, b) => a.min - b.min);
    }

    function loadQuickRules() {
        if (quickRulesStorageKey) {
            try {
                const stored = JSON.parse(localStorage.getItem(quickRulesStorageKey));
                const normalized = normalizeQuickRules(stored);
                if (normalized.length) {
                    return normalized;
                }
            } catch (error) {
                // ignore parsing errors and fall back to defaults
            }
        }

        if (legacyQuickStorageKey) {
            const legacy = Number(localStorage.getItem(legacyQuickStorageKey));
            if (legacy && legacy > 0) {
                return normalizeQuickRules([{ min: 0, max: null, increment: legacy }]);
            }
        }

        return normalizeQuickRules(defaultQuickRules);
    }

    function saveQuickRules(rules) {
        quickRules = normalizeQuickRules(rules);
        if (quickRulesStorageKey) {
            localStorage.setItem(quickRulesStorageKey, JSON.stringify(quickRules));
        }
    }

    function getQuickIncrement(amount) {
        const price = Number(amount) || 0;
        const match = quickRules.find((rule) => {
            if (rule.max === null || rule.max === undefined) {
                return price >= rule.min;
            }
            return price >= rule.min && price < rule.max;
        });
        let increment = 0;
        if (match) {
            increment = match.increment;
        } else if (quickRules.length) {
            increment = price < quickRules[0].min
                ? quickRules[0].increment
                : quickRules[quickRules.length - 1].increment;
        }
        quickIncrementValue = increment;
        return increment;
    }

    function buildJumpTargets(base) {
        const step = getJumpStep();
        const count = 2;
        const targets = [];
        for (let i = 1; i <= count; i++) {
            targets.push(base + step * i);
        }
        return { targets, step };
    }

    function renderJumpTargets() {
        if (!quickJumpGrid) {
            return;
        }
        const base = Number(controllerBaseInput?.value || 0);
        const { targets, step } = buildJumpTargets(base);
        quickJumpGrid.innerHTML = '';
        if (quickJumpNote) {
            quickJumpNote.textContent = `(steps of ₹${(step || 0).toLocaleString('en-IN')})`;
        }
        if (quickJumpStepInput && !quickJumpStepInput.value) {
            quickJumpStepInput.value = step || 50;
        }
        targets.forEach((target) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `quick-button ${controllerBaseInput?.value ? '' : 'opacity-50 cursor-not-allowed'}`;
            btn.textContent = `₹${target.toLocaleString('en-IN')}`;
            btn.disabled = !controllerBaseInput?.value;
            btn.addEventListener('click', () => {
                if (!controllerBidInput) {
                    return;
                }
                controllerBidInput.value = target;
                controllerBidInput.dataset.defaultIncrement = target - base;
                updatePreview(target);
                updateQuickButtonLabel();
            });
            quickJumpGrid.appendChild(btn);
        });
    }

    function updatePreview(value) {
        if (controllerPreview) {
            controllerPreview.textContent = formatCurrency(value);
        }
    }

    function isTeamDisabled(button) {
        return button?.dataset.teamDisabled === 'true';
    }

    function highlightSelectedTeam(teamId) {
        teamPills.forEach(button => {
            if (button.dataset.teamPill === teamId && !isTeamDisabled(button)) {
                button.classList.add('active');
            } else {
                button.classList.remove('active');
            }
        });
        renderTeamDetails(teamId);
    }

    function renderTeamDetails(teamId) {
        teamPills.forEach(button => {
            const panel = button.querySelector('[data-team-details-panel]');
            if (!panel) {
                return;
            }
            if (button.dataset.teamPill === teamId && button.classList.contains('active')) {
                const details = button.dataset.teamDetails ? JSON.parse(button.dataset.teamDetails) : null;
                if (details) {
                    panel.innerHTML = `
                        <p>Players ${details.players} · Retained ${details.retained}</p>
                        <p>Needs ${details.needs} • Reserve ₹${details.reserve}</p>
                        <p>Max bid ₹${details.max}</p>
                    `;
                    panel.style.display = 'block';
                } else {
                    panel.innerHTML = '';
                    panel.style.display = 'none';
                }
            } else {
                panel.innerHTML = '';
                panel.style.display = 'none';
            }
        });
    }

    function setTeamSelection(teamId) {
        const button = teamId ? getTeamButton(teamId) : null;
        if (button && isTeamDisabled(button)) {
            return;
        }
        if (controllerTeamSelect) {
            controllerTeamSelect.value = teamId || '';
            highlightSelectedTeam(teamId);
        }
        const base = Number(controllerBaseInput?.value || 0);
        if (controllerBidInput) {
            controllerBidInput.value = base;
            updatePreview(base);
        }
        updateSoldButtonLabel();
        if (selectedTeamLabel) {
            selectedTeamLabel.textContent = getSelectedTeamName() || 'None';
        }
        updateTeamBudgetLabels(teamId);
        applyQuickBid();
    }

    function clearTeamSelection() {
        if (controllerTeamSelect) {
            controllerTeamSelect.value = '';
        }
        highlightSelectedTeam(null);
        updateTeamBudgetLabels(null);
        if (selectedTeamLabel) {
            selectedTeamLabel.textContent = 'None';
        }
    }

    teamPills.forEach(button => {
        button.addEventListener('click', () => {
            if (button.disabled || isTeamDisabled(button)) {
                return;
            }
            if (controllerTeamSelect?.value === button.dataset.teamPill) {
                clearTeamSelection();
            } else {
                setTeamSelection(button.dataset.teamPill);
            }
        });
    });

    if (controllerTeamSelect) {
        if (!controllerTeamSelect.value && controllerDefaultTeam && controllerDefaultTeam.value) {
            const defaultButton = getTeamButton(controllerDefaultTeam.value);
            if (defaultButton && !isTeamDisabled(defaultButton)) {
                setTeamSelection(controllerDefaultTeam.value);
            } else {
                clearTeamSelection();
            }
        } else if (controllerTeamSelect.value) {
            const activeButton = getTeamButton(controllerTeamSelect.value);
            if (activeButton && !isTeamDisabled(activeButton)) {
                setTeamSelection(controllerTeamSelect.value);
            } else {
                clearTeamSelection();
            }
        } else {
            clearTeamSelection();
        }
    }
    renderJumpTargets();
    function getTeamButton(teamId) {
        return Array.from(teamPills).find(button => button.dataset.teamPill === teamId);
    }

    function updateTeamBudgetLabels(teamId) {
        const button = teamId ? getTeamButton(teamId) : null;
        const reserve = button ? Number(button.dataset.teamReserve || 0) : 0;
        const maxBid = button ? Number(button.dataset.teamMax || 0) : 0;
        const needed = button ? Number(button.dataset.teamNeeded || 0) : 0;
        if (selectedReserveLabel) {
            selectedReserveLabel.textContent = formatCurrency(reserve);
        }
        if (selectedMaxLabel) {
            selectedMaxLabel.textContent = formatCurrency(maxBid);
        }
        if (selectedNeedLabel) {
            selectedNeedLabel.textContent = needed;
        }
    }

    function getSelectedTeamName() {
        const teamId = controllerTeamSelect?.value;
        if (teamId) {
            return teamNameMap[teamId] || '';
        }
        return '';
    }

    function updateSoldButtonLabel(amountOverride = null) {
        if (!soldButton) {
            return;
        }
        const amount = amountOverride !== null
            ? Number(amountOverride)
            : Number(controllerBaseInput?.value || 0);
        const teamName = getSelectedTeamName();
        if (teamName && amount > 0) {
            soldButton.textContent = `Sold • ${teamName} • ${formatCurrency(amount)}`;
        } else {
            soldButton.textContent = defaultSoldLabel;
        }
    }

    function updateOverrideLabel() {
        if (overrideLabel) {
            const value = Number(overrideInput?.value || 0);
            if (value > 0) {
                overrideLabel.textContent = `Current override: ${formatCurrency(value)}`;
            } else {
                overrideLabel.textContent = 'Current override: None';
            }
        }
    }

    function updateQuickButtonLabel() {
        if (quickButton) {
            const base = Number(controllerBaseInput?.value || 0);
            const increment = getQuickIncrement(base);
            quickIncrementValue = increment;
            quickButton.textContent = increment > 0
                ? `+₹${increment.toLocaleString('en-IN')}`
                : 'Set quick rules';
        }
    }

    function applyQuickBid(showError = false) {
        if (!controllerBidInput || !controllerBaseInput) {
            return;
        }
        const base = Number(controllerBaseInput.value || 0);
        const increment = getQuickIncrement(base);
        if (!increment || increment <= 0) {
            if (showError) {
                showControllerMessage('Add a quick bid rule first.', 'error');
            }
            controllerBidInput.value = base;
            updatePreview(base);
            updateQuickButtonLabel();
            return;
        }
        const target = base + increment;
        controllerBidInput.dataset.defaultIncrement = increment;
        controllerBidInput.value = target;
        updatePreview(target);
        updateQuickButtonLabel();
        renderJumpTargets();
    }

    function setInitialBidValue() {
        if (!controllerBidInput || !controllerBaseInput) return;
        const base = Number(controllerBaseInput.value || 0);
        const hasExistingBid = Boolean(controllerDefaultTeam?.value);
        const increment = getQuickIncrement(base);
        if (hasExistingBid && increment > 0) {
            const target = base + increment;
            controllerBidInput.dataset.defaultIncrement = increment;
            controllerBidInput.value = target;
            updatePreview(target);
        } else {
            controllerBidInput.dataset.defaultIncrement = increment;
            controllerBidInput.value = base;
            updatePreview(base);
        }
        updateQuickButtonLabel();
        renderJumpTargets();
    }

    if (quickButton) {
        quickButton.addEventListener('click', () => {
            applyQuickBid(true);
        });
    }

    if (quickEditButton) {
        quickEditButton.addEventListener('click', () => {
            openQuickRulesModal();
        });
    }

    function renderQuickRuleRows(rules) {
        if (!quickRulesRows) {
            return;
        }
        quickRulesRows.innerHTML = '';
        const rows = (rules && rules.length ? rules : defaultQuickRules);
        rows.forEach(rule => addQuickRuleRow(rule));
        if (!quickRulesRows.children.length) {
            addQuickRuleRow({ min: 0, max: null, increment: 50 });
        }
    }

    function addQuickRuleRow(rule = { min: 0, max: null, increment: 0 }) {
        if (!quickRulesRows) {
            return;
        }
        const row = document.createElement('div');
        row.className = 'quick-rule-row';
        row.dataset.ruleRow = 'true';
        row.innerHTML = `
            <label>
                <span>From</span>
                <input type="number" min="0" inputmode="numeric" value="${rule.min ?? 0}" data-rule-min>
            </label>
            <label>
                <span>To</span>
                <input type="number" min="0" inputmode="numeric" value="${rule.max ?? ''}" data-rule-max>
            </label>
            <label>
                <span>Increment</span>
                <input type="number" min="1" inputmode="numeric" value="${rule.increment ?? ''}" data-rule-increment>
            </label>
            <button type="button" class="text-rose-500 text-sm font-semibold px-2" data-remove-rule aria-label="Remove rule">&times;</button>
        `;
        const removeBtn = row.querySelector('[data-remove-rule]');
        removeBtn?.addEventListener('click', () => {
            const totalRows = quickRulesRows?.querySelectorAll('[data-rule-row]')?.length || 0;
            if (totalRows <= 1) {
                showControllerMessage('Keep at least one quick bid rule.', 'error');
                return;
            }
            row.remove();
        });
        quickRulesRows.appendChild(row);
    }

    function collectQuickRulesFromUI() {
        if (!quickRulesRows) {
            return [];
        }
        const rows = quickRulesRows.querySelectorAll('[data-rule-row]');
        return Array.from(rows).map((row) => {
            const min = Number(row.querySelector('[data-rule-min]')?.value ?? 0);
            const rawMax = row.querySelector('[data-rule-max]')?.value ?? '';
            const increment = Number(row.querySelector('[data-rule-increment]')?.value ?? 0);
            return {
                min: isNaN(min) ? 0 : min,
                max: rawMax === '' ? null : Number(rawMax),
                increment
            };
        });
    }

    function openQuickRulesModal() {
        if (!quickRulesModal || !quickRulesRows) {
            const fallback = window.prompt('Set quick bid increment', quickIncrementValue || defaultQuickRules[0]?.increment || 0);
            if (fallback !== null) {
                const parsed = Number(fallback);
                if (parsed > 0) {
                    saveQuickRules([{ min: 0, max: null, increment: parsed }]);
                    applyQuickBid();
                    showControllerMessage('Quick bid rule updated.');
                } else {
                    showControllerMessage('Enter a valid amount.', 'error');
                }
            }
            return;
        }
        renderQuickRuleRows(quickRules);
        quickRulesModal.classList.remove('hidden');
    }

    function closeQuickRulesModal() {
        quickRulesModal?.classList.add('hidden');
    }

    addQuickRuleBtn?.addEventListener('click', () => addQuickRuleRow());
    saveQuickRulesBtn?.addEventListener('click', () => {
        const parsedRules = normalizeQuickRules(collectQuickRulesFromUI());
        if (!parsedRules.length) {
            showControllerMessage('Add at least one valid quick bid rule.', 'error');
            return;
        }
        saveQuickRules(parsedRules);
        applyQuickBid();
        showControllerMessage('Quick bid rules saved.');
        closeQuickRulesModal();
    });
    cancelQuickRulesBtn?.addEventListener('click', closeQuickRulesModal);
    closeQuickRulesBtn?.addEventListener('click', closeQuickRulesModal);
    quickRulesModal?.addEventListener('click', (event) => {
        if (event.target === quickRulesModal) {
            closeQuickRulesModal();
        }
    });

    if (overrideButton) {
        overrideButton.addEventListener('click', () => {
            openValueModal({
                title: 'Set sold override amount',
                placeholder: 'Leave blank to clear',
                defaultValue: overrideInput?.value || '',
                onConfirm(value) {
                    if (value === '') {
                        if (overrideInput) {
                            overrideInput.value = '';
                        }
                        updateOverrideLabel();
                        showControllerMessage('Override cleared.');
                        return true;
                    }
                    const parsed = Number(value);
                    if (!parsed || parsed <= 0) {
                        showControllerMessage('Enter a valid override amount.', 'error');
                        return false;
                    }
                    if (overrideInput) {
                        overrideInput.value = parsed;
                    }
                    updateOverrideLabel();
                    showControllerMessage('Override updated.');
                    return true;
                }
            });
        });
    }

    function openValueModal(config) {
        if (!modal || !modalInput || !modalTitle) {
            const fallback = window.prompt(config.title || 'Enter value', config.defaultValue ?? '');
            if (fallback !== null && config.onConfirm) {
                config.onConfirm(fallback);
            }
            return;
        }
        modalState = config;
        modalTitle.textContent = config.title || 'Update value';
        modalInput.value = config.defaultValue ?? '';
        modalInput.placeholder = config.placeholder || '';
        modal.classList.remove('hidden');
        setTimeout(() => modalInput.focus(), 50);
    }

    function closeValueModal() {
        if (modal) {
            modal.classList.add('hidden');
        }
        modalState = null;
    }

    modalConfirm?.addEventListener('click', () => {
        if (!modalState) {
            closeValueModal();
            return;
        }
        const shouldClose = modalState.onConfirm?.(modalInput.value);
        if (shouldClose !== false) {
            closeValueModal();
        }
    });

    modalCancel?.addEventListener('click', closeValueModal);
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeValueModal();
        }
    });
    modalPresetButtons?.forEach(button => {
        button.addEventListener('click', () => {
            modalInput.value = button.dataset.modalValue || '';
            modalInput.focus();
        });
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (!modal?.classList.contains('hidden')) {
                closeValueModal();
            }
            if (!quickRulesModal?.classList.contains('hidden')) {
                closeQuickRulesModal();
            }
            if (!unsoldModal?.classList.contains('hidden')) {
                closeUnsoldModal();
            }
        }
    });

    unsoldConfirmButtons?.forEach((button) => button.addEventListener('click', () => startUnsoldRound()));
    unsoldCancelButtons?.forEach((button) => button.addEventListener('click', () => {
        pendingAutoStartAfterConfirm = false;
        closeUnsoldModal();
    }));

    function getJumpStep() {
        const fallback = 50;
        const value = Number(quickJumpStepInput?.value || 0);
        if (value > 0) {
            return value;
        }
        if (quickJumpStorageKey) {
            const stored = Number(localStorage.getItem(quickJumpStorageKey) || 0);
            if (stored > 0) {
                return stored;
            }
        }
        return fallback;
    }

    function saveJumpStep(value) {
        if (!quickJumpStorageKey) return;
        localStorage.setItem(quickJumpStorageKey, String(value));
    }

    quickJumpStepInput?.addEventListener('change', () => {
        const value = Number(quickJumpStepInput.value || 0);
        if (!value || value <= 0) {
            quickJumpStepInput.value = getJumpStep();
            return;
        }
        saveJumpStep(value);
        renderJumpTargets();
    });
    quickJumpStepInput?.addEventListener('blur', () => {
        const value = Number(quickJumpStepInput.value || 0);
        if (!value || value <= 0) {
            quickJumpStepInput.value = getJumpStep();
        }
    });

    updateOverrideLabel();

    function showControllerMessage(message, type = 'success') {
        const feedback = document.getElementById('controller-feedback');
        if (!feedback) {
            alert(message);
            return;
        }

        feedback.textContent = message;
        feedback.classList.remove('hidden', 'success', 'error');
        feedback.classList.add(type === 'success' ? 'success' : 'error');

        setTimeout(() => {
            feedback.classList.add('hidden');
        }, 3500);
    }

    async function placeControllerBid(button) {
        const playerId = document.getElementById('controller-player-id')?.value;
        const leaguePlayerId = document.getElementById('controller-league-player-id')?.value;
        const leagueId = document.getElementById('controller-league-id')?.value;
        const teamId = document.getElementById('controller-team')?.value;
        const action = document.getElementById('controller-bid-action')?.value;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!playerId || !leaguePlayerId) {
            showControllerMessage('No player is currently active for bidding.', 'error');
            return;
        }

        if (!teamId) {
            showControllerMessage('Select a team before placing a bid.', 'error');
            return;
        }

        const baseAmount = Number(controllerBaseInput?.value || 0);
        const targetAmount = Number(controllerBidInput?.value || 0);

        if (!targetAmount || targetAmount <= baseAmount) {
            showControllerMessage('Bid must be greater than the current price.', 'error');
            return;
        }

        if (!action || !token) {
            showControllerMessage('Missing bid configuration. Refresh and try again.', 'error');
            return;
        }

        const increment = targetAmount - baseAmount;

        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.536-3.536A8 8 0 014 12z"></path></svg>Placing...</span>';

        try {
            const response = await fetch(action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    league_id: leagueId,
                    player_id: playerId,
                    league_player_id: leaguePlayerId,
                    base_price: baseAmount,
                    increment: increment,
                    league_team_id: teamId
                })
            });

            const data = await response.json().catch(() => ({ success: false, message: 'Unable to place bid.' }));

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to place bid.');
            }

            controllerBaseInput.value = targetAmount;
            updateSoldButtonLabel(targetAmount);
            const nextIncrement = getQuickIncrement(targetAmount);
            const nextTarget = nextIncrement > 0 ? targetAmount + nextIncrement : targetAmount;
            controllerBidInput.value = nextTarget;
            controllerBidInput.dataset.defaultIncrement = nextIncrement;
            updatePreview(nextTarget);
            updateQuickButtonLabel();
            renderJumpTargets();
            const bidLabel = document.getElementById('controller-current-bid-label');
            if (bidLabel) {
                bidLabel.textContent = formatCurrency(targetAmount);
            }

            showControllerMessage('Bid placed successfully. The live auction has been updated.');
            setTimeout(() => window.location.reload(), 600);
        } catch (error) {
            showControllerMessage(error.message || 'Unable to place bid.', 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    const hasActivePlayer = Boolean(document.getElementById('controller-league-player-id')?.value);
    if (hasActivePlayer) {
        setInitialBidValue();
    } else {
        updatePreview(controllerBidInput?.value || controllerBaseInput?.value || 0);
        updateQuickButtonLabel();
    }
    renderJumpTargets();
    updateSoldButtonLabel();

    window.placeControllerBid = placeControllerBid;
    window.markControllerSold = markControllerSold;
    window.markControllerUnsold = markControllerUnsold;

    function getControllerToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    function ensurePlayerActive() {
        const leaguePlayerId = document.getElementById('controller-league-player-id')?.value;
        if (!leaguePlayerId) {
            showControllerMessage('No player is currently active for this action.', 'error');
            return false;
        }
        return true;
    }

    async function markControllerSold(button) {
        if (!ensurePlayerActive()) {
            return;
        }

        const teamId = controllerTeamSelect?.value || controllerDefaultTeam?.value;
        if (!teamId) {
            showControllerMessage('Select a winning team before marking sold.', 'error');
            return;
        }

        const teamButton = getTeamButton(teamId);
        if (!teamButton) {
            showControllerMessage('Select a valid team before marking sold.', 'error');
            return;
        }
        const teamNeeded = Number(teamButton?.dataset.teamNeeded || 0);
        if (teamNeeded <= 0) {
            showControllerMessage('Team already completed their required players.', 'error');
            return;
        }

        const action = document.getElementById('controller-sold-action')?.value;
        const token = getControllerToken();
        if (!action || !token) {
            showControllerMessage('Missing configuration for sold action.', 'error');
            return;
        }

        const leaguePlayerId = document.getElementById('controller-league-player-id').value;
        const overrideAmount = document.getElementById('controller-override-amount')?.value;
        const playerBasePrice = Number(playerBasePriceInput?.value || 0);
        const currentBidAmount = Number(controllerBaseInput?.value || 0);
        const finalAmount = Number(overrideAmount || currentBidAmount);
        const teamMaxCap = Number(teamButton?.dataset.teamMax || 0);
        const teamWalletBalance = Number(teamButton?.dataset.teamWallet || 0);

        if (!finalAmount || finalAmount < playerBasePrice) {
            showControllerMessage('Bid amount is below base price.', 'error');
            return;
        }

        if (teamMaxCap && finalAmount > teamMaxCap) {
            showControllerMessage("Bid exceeds team's max cap/balance.", 'error');
            return;
        }

        if (!Number.isNaN(teamWalletBalance) && finalAmount > teamWalletBalance) {
            showControllerMessage("Bid exceeds team's max cap/balance.", 'error');
            return;
        }

        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.536-3.536A8 8 0 014 12z"></path></svg>Saving...</span>';

        try {
            const response = await fetch(action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    league_player_id: leaguePlayerId,
                    team_id: teamId,
                    override_amount: overrideAmount || null,
                    final_amount: finalAmount,
                    player_base_price: playerBasePrice,
                    current_bid_amount: currentBidAmount
                })
            });

            const data = await response.json().catch(() => ({ success: false }));

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to mark player as sold.');
            }

            showControllerMessage('Player marked as sold. Refreshing...', 'success');
            const currentPlayerIdInput = document.getElementById('controller-league-player-id');
            if (currentPlayerIdInput) {
                currentPlayerIdInput.value = '';
            }
            const updatedNeeded = Math.max(0, teamNeeded - 1);
            const updatedWallet = Math.max(0, teamWalletBalance - finalAmount);
            teamButton.dataset.teamNeeded = String(updatedNeeded);
            teamButton.dataset.teamWallet = String(updatedWallet);
            updateTeamBudgetLabels(teamId);
            markPlayerCompleted(leaguePlayerId);
            const transition = handlePhaseTransitions({ autoStart: autoStartEnabled });
            if (autoStartEnabled && !transition.pending && startNextQueued(true)) {
                return;
            }
            if (transition.pending) {
                return;
            }
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            showControllerMessage(error.message || 'Unable to mark player as sold.', 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    async function markControllerUnsold(button) {
        if (!ensurePlayerActive()) {
            return;
        }

        const action = document.getElementById('controller-unsold-action')?.value;
        const token = getControllerToken();
        if (!action || !token) {
            showControllerMessage('Missing configuration for unsold action.', 'error');
            return;
        }

        const leaguePlayerId = document.getElementById('controller-league-player-id').value;

        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.536-3.536A8 8 0 014 12z"></path></svg>Updating...</span>';

        try {
            const response = await fetch(action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    league_player_id: leaguePlayerId
                })
            });

            const data = await response.json().catch(() => ({ success: false }));

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to mark player as unsold.');
            }

            showControllerMessage('Player marked as unsold. Refreshing...', 'success');
            const currentPlayerIdInput = document.getElementById('controller-league-player-id');
            if (currentPlayerIdInput) {
                currentPlayerIdInput.value = '';
            }
            markPlayerCompleted(leaguePlayerId);
            const transition = handlePhaseTransitions({ autoStart: autoStartEnabled });
            if (autoStartEnabled && !transition.pending && startNextQueued(true)) {
                return;
            }
            if (transition.pending) {
                return;
            }
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            showControllerMessage(error.message || 'Unable to mark player as unsold.', 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    const availablePlayerPool = parsePlayers(availablePlayersScript);
    const unsoldPlayerPool = parsePlayers(unsoldPlayersScript);
    let playerQueue = loadQueue();
    let autoStartEnabled = loadAutoStartSetting();
    rebuildRandomPools();
    enforcePhaseConsistency();
    handlePhaseTransitions({ showToast: false });
    renderQueue();
    initPlayerToolsToggle();
    initMismatchToggle();
    syncAutoStartUI();

    function parsePlayers(scriptEl) {
        if (!scriptEl?.textContent) {
            return [];
        }
        try {
            const parsed = JSON.parse(scriptEl.textContent);
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    }

    function normalizePlayer(player) {
        return {
            id: player.id ? String(player.id) : '',
            user_id: player.user_id ? String(player.user_id) : '',
            name: player.name || player.player_name || 'Player',
            role: player.role || player.position || 'Role TBA',
            base_price: Number(player.base_price ?? 0),
            photo: player.photo || defaultPlayerPhoto
        };
    }

    function saveRandomPhase(phase) {
        currentRandomPhase = normalizeRandomPhase(phase);
        if (randomPhaseStorageKey) {
            localStorage.setItem(randomPhaseStorageKey, currentRandomPhase);
        }
    }

    function uniqueCount(pool) {
        return new Set((pool || []).map(p => String(p.id))).size;
    }

    function buildRandomPool(pool, completedSet, servedSet) {
        return (pool || []).filter((p) => {
            const id = String(p.id);
            return !completedSet.has(id) && !servedSet.has(id);
        });
    }

    function rebuildRandomPools() {
        availableRandomPool = buildRandomPool(availablePlayerPool, availableCompletedSet, availableServedSet);
        unsoldRandomPool = buildRandomPool(unsoldPlayerPool, unsoldCompletedSet, unsoldServedSet);
    }

    function isAvailablePlayer(id) {
        const target = String(id || '');
        return availablePlayerPool.some((p) => String(p.id) === target);
    }

    function isUnsoldPlayer(id) {
        const target = String(id || '');
        return unsoldPlayerPool.some((p) => String(p.id) === target);
    }

    function markServed(playerId, phase) {
        const id = String(playerId || '');
        if (!id) return;
        if (phase === 'unsold') {
            unsoldServedSet.add(id);
            saveIdSet(unsoldServedStorageKey, unsoldServedSet);
        } else {
            availableServedSet.add(id);
            saveIdSet(availableServedStorageKey, availableServedSet);
        }
        rebuildRandomPools();
    }

    function markPlayerCompleted(playerId) {
        const id = String(playerId || '');
        if (!id) return;
        if (isAvailablePlayer(id)) {
            availableCompletedSet.add(id);
            saveIdSet(availableCompletedStorageKey, availableCompletedSet);
        }
        if (isUnsoldPlayer(id)) {
            unsoldCompletedSet.add(id);
            saveIdSet(unsoldCompletedStorageKey, unsoldCompletedSet);
        }
        rebuildRandomPools();
    }

    function isAllAvailableCompleted() {
        return availableCompletedSet.size >= uniqueCount(availablePlayerPool);
    }

    function isAllUnsoldCompleted() {
        return unsoldCompletedSet.size >= uniqueCount(unsoldPlayerPool);
    }

    function enforcePhaseConsistency() {
        const availableDone = isAllAvailableCompleted();
        const unsoldDone = isAllUnsoldCompleted();

        if (currentRandomPhase === 'unsold' && !availableDone) {
            saveRandomPhase('available');
        }

        if (currentRandomPhase === 'unsold' && !unsoldPlayerPool.length) {
            saveRandomPhase('completed');
        }

        if (currentRandomPhase === 'completed') {
            if (!availableDone) {
                saveRandomPhase('available');
            } else if (!unsoldDone && unsoldPlayerPool.length) {
                saveRandomPhase('unsold');
            }
        }

        if (currentRandomPhase === 'available' && availableDone && !unsoldPlayerPool.length) {
            saveRandomPhase('completed');
        }
    }

    function takeRandomPlayerFromPool(phase) {
        const pool = phase === 'unsold' ? unsoldRandomPool : availableRandomPool;
        if (!pool.length) return null;
        const index = Math.floor(Math.random() * pool.length);
        const [player] = pool.splice(index, 1);
        markServed(player.id, phase);
        return player ? normalizePlayer(player) : null;
    }

    function takeRandomPlayerForCurrentPhase() {
        if (currentRandomPhase === 'completed') {
            return null;
        }
        if (currentRandomPhase === 'unsold') {
            const candidate = takeRandomPlayerFromPool('unsold');
            if (candidate) return candidate;
            return null;
        }
        const candidate = takeRandomPlayerFromPool('available');
        return candidate || null;
    }

    function openUnsoldModal(autoStart = false) {
        pendingAutoStartAfterConfirm = autoStart;
        if (!unsoldModal) {
            const proceed = window.confirm('All available players are finished. Start Unsold players round?');
            if (proceed) {
                startUnsoldRound();
            }
            return;
        }
        unsoldModal.classList.remove('hidden');
    }

    function closeUnsoldModal() {
        unsoldModal?.classList.add('hidden');
    }

    function startUnsoldRound() {
        saveRandomPhase('unsold');
        rebuildRandomPools();
        closeUnsoldModal();
        showControllerMessage('Unsold round started. Random picks will use unsold players.');
        handlePhaseTransitions();
        if (pendingAutoStartAfterConfirm) {
            pendingAutoStartAfterConfirm = false;
            setTimeout(() => startNextQueued(true), 50);
        }
    }

    function handlePhaseTransitions({ autoStart = false, showToast = true } = {}) {
        const availableDone = isAllAvailableCompleted();
        const unsoldDone = isAllUnsoldCompleted();
        const hasUnsold = uniqueCount(unsoldPlayerPool) > 0;

        if (currentRandomPhase === 'available' && availableDone) {
            if (hasUnsold) {
                openUnsoldModal(autoStart);
                return { pending: true };
            }
            saveRandomPhase('completed');
            if (showToast) {
                showControllerMessage('All players are completed. No unsold players remaining.');
            }
            return { pending: false };
        }

        if (currentRandomPhase === 'unsold' && unsoldDone) {
            saveRandomPhase('completed');
            if (showToast) {
                showControllerMessage('All unsold players finished. Auction fully completed.');
            }
            return { pending: false };
        }

        return { pending: false };
    }

    function saveQueue() {
        if (queueStorageKey) {
            localStorage.setItem(queueStorageKey, JSON.stringify(playerQueue));
        }
    }

    function saveAutoStart(value) {
        autoStartEnabled = value;
        if (autoStartStorageKey) {
            localStorage.setItem(autoStartStorageKey, value ? 'on' : 'off');
        }
        syncAutoStartUI();
    }

    function loadAutoStartSetting() {
        if (!autoStartStorageKey) {
            return false;
        }
        const stored = localStorage.getItem(autoStartStorageKey);
        return stored === 'on';
    }

    function syncAutoStartUI() {
        autoStartRadios?.forEach(radio => {
            radio.checked = radio.value === (autoStartEnabled ? 'on' : 'off');
        });
    }

    function loadQueue() {
        if (!queueStorageKey) {
            return [];
        }
        try {
            const raw = localStorage.getItem(queueStorageKey);
            if (!raw) {
                return [];
            }
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    }

    function addToQueue(player) {
        const normalized = normalizePlayer(player);
        if (!normalized.id || !normalized.user_id) {
            showControllerMessage('Cannot queue this player right now.', 'error');
            return;
        }
        const exists = playerQueue.some((p) => p.id === normalized.id);
        if (exists) {
            showControllerMessage('Player already in queue.');
            return;
        }
        playerQueue.push(normalized);
        saveQueue();
        renderQueue();
        showControllerMessage(`${normalized.name} added to queue.`);
    }

    function removeFromQueue(playerId) {
        playerQueue = playerQueue.filter((p) => p.id !== playerId);
        saveQueue();
        renderQueue();
    }

    function renderQueue() {
        if (!playerQueueList || !playerQueueEmpty) {
            return;
        }
        playerQueueList.innerHTML = '';
        if (!playerQueue.length) {
            playerQueueEmpty.classList.remove('hidden');
            return;
        }
        playerQueueEmpty.classList.add('hidden');
        playerQueue.forEach((player) => {
            const card = document.createElement('div');
            card.className = 'player-queue__item';
            card.innerHTML = `
                <div class="player-finder__avatar">
                    <img src="${player.photo}" alt="${player.name}" class="w-full h-full object-cover" onerror="this.src='${defaultPlayerPhoto}'">
                </div>
                <div class="player-queue__meta">
                    <p class="player-finder__name">${player.name}</p>
                    <p class="player-finder__details">${player.role} • ${formatCurrency(player.base_price)}</p>
                </div>
                <div class="player-queue__actions">
                    <button type="button" class="player-finder__action" data-queue-start>Start</button>
                    <button type="button" class="text-[11px] font-semibold text-rose-500" data-queue-remove>Remove</button>
                </div>
            `;
            const startBtn = card.querySelector('[data-queue-start]');
            const removeBtn = card.querySelector('[data-queue-remove]');
            startBtn?.addEventListener('click', () => startAuctionForPlayer(player, startBtn));
            removeBtn?.addEventListener('click', () => removeFromQueue(player.id));
            playerQueueList.appendChild(card);
        });
    }

    async function startAuctionForPlayer(player, triggerButton, forceStart = false) {
        const startAction = controllerStartAction?.value;
        const token = getControllerToken();
        const activeLeaguePlayerId = document.getElementById('controller-league-player-id')?.value;
        if (!forceStart && activeLeaguePlayerId && String(activeLeaguePlayerId) !== String(player.id)) {
            showControllerMessage('Finish the current player before starting another.', 'error');
            return;
        }
        if (!startAction || !leagueIdValue) {
            showControllerMessage('Missing start configuration. Refresh and try again.', 'error');
            return;
        }
        if (!player?.id || !player?.user_id) {
            showControllerMessage('Player details are missing.', 'error');
            return;
        }
        const originalText = triggerButton?.innerHTML;
        if (triggerButton) {
            triggerButton.disabled = true;
            triggerButton.innerHTML = 'Starting...';
        }
        try {
            const response = await fetch(startAction, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    league_id: Number(leagueIdValue),
                    league_player_id: Number(player.id),
                    player_id: Number(player.user_id)
                })
            });
            const data = await response.json().catch(() => null);
            if (!response.ok || !data?.success) {
                throw new Error(data?.message || 'Unable to start auction for this player.');
            }
            showControllerMessage(`${player.name} queued for bidding.`);
            removeFromQueue(String(player.id));
            setTimeout(() => window.location.reload(), 800);
        } catch (error) {
            showControllerMessage(error.message || 'Unable to start auction.', 'error');
        } finally {
            if (triggerButton) {
                triggerButton.disabled = false;
                triggerButton.innerHTML = originalText || 'Start';
            }
        }
    }

    async function searchAvailablePlayers(query) {
        if (!playerSearchResults) {
            return;
        }
        playerSearchResults.innerHTML = '<div class="player-finder__empty">Searching…</div>';
        playerSearchResults.classList.remove('hidden');
        try {
            const response = await fetch(`/auction/search-players?query=${encodeURIComponent(query)}&league_id=${leagueIdValue}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            const data = await response.json().catch(() => null);
            if (data?.success && Array.isArray(data.players) && data.players.length) {
                renderSearchResults(data.players);
            } else {
                renderSearchResults([]);
            }
        } catch (error) {
            renderSearchResults([]);
        }
    }

    function renderSearchResults(players) {
        if (!playerSearchResults) {
            return;
        }
        playerSearchResults.innerHTML = '';
        if (!players || players.length === 0) {
            playerSearchResults.innerHTML = '<div class="player-finder__empty">No players found</div>';
            playerSearchResults.classList.remove('hidden');
            return;
        }
        players.forEach((raw) => {
            const player = normalizePlayer(raw);
            const row = document.createElement('div');
            row.className = 'player-finder__card w-full';
            row.innerHTML = `
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="player-finder__avatar">
                        <img src="${player.photo}" alt="${player.name}" class="w-full h-full object-cover" onerror="this.src='${defaultPlayerPhoto}'">
                    </div>
                    <div class="player-finder__meta">
                        <p class="player-finder__name">${player.name}</p>
                        <p class="player-finder__details">${player.role} • ${formatCurrency(player.base_price)}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="player-finder__action" data-result-queue>Add to queue</button>
                    <button type="button" class="player-finder__action" data-result-start>Start now</button>
                </div>
            `;
            const addBtn = row.querySelector('[data-result-queue]');
            const startBtn = row.querySelector('[data-result-start]');
            addBtn?.addEventListener('click', (event) => {
                event.stopPropagation();
                addToQueue(player);
                playerSearchResults.classList.add('hidden');
            });
            startBtn?.addEventListener('click', (event) => {
                event.stopPropagation();
                playerSearchResults.classList.add('hidden');
                startAuctionForPlayer(player, startBtn);
            });
            playerSearchResults.appendChild(row);
        });
        playerSearchResults.classList.remove('hidden');
    }

    if (playerSearchInput) {
        let searchTimeout;
        playerSearchInput.addEventListener('input', (event) => {
            const query = event.target.value.trim();
            clearTimeout(searchTimeout);
            if (query.length < 2) {
                playerSearchResults?.classList.add('hidden');
                return;
            }
            searchTimeout = setTimeout(() => {
                searchAvailablePlayers(query);
            }, 250);
        });
        document.addEventListener('click', (event) => {
            if (!playerSearchResults) {
                return;
            }
            if (!playerSearchResults.contains(event.target) && !playerSearchInput.contains(event.target)) {
                playerSearchResults.classList.add('hidden');
            }
        });
    }

    function getActivePool() {
        if (currentRandomPhase === 'unsold') {
            return { pool: unsoldRandomPool, label: 'unsold' };
        }
        if (currentRandomPhase === 'completed') {
            return { pool: [], label: 'completed' };
        }
        return { pool: availableRandomPool, label: 'available' };
    }

    randomPlayerButton?.addEventListener('click', () => {
        if (currentRandomPhase === 'completed') {
            showControllerMessage('All unsold players finished. Auction fully completed.');
            return;
        }
        const { pool } = getActivePool();
        if (!pool.length) {
            const transition = handlePhaseTransitions();
            if (transition.pending) return;
            showControllerMessage('No players to pick right now.', 'error');
            return;
        }
        const player = takeRandomPlayerForCurrentPhase();
        if (!player) {
            const transition = handlePhaseTransitions();
            if (!transition.pending && currentRandomPhase !== 'completed') {
                showControllerMessage('No players to pick right now.', 'error');
            }
            return;
        }
        addToQueue(player);
        if (currentRandomPhase === 'unsold') {
            showControllerMessage('Added an unsold player to the queue.');
        }
    });

    clearQueueButton?.addEventListener('click', () => {
        playerQueue = [];
        saveQueue();
        renderQueue();
    });

    function initPlayerToolsToggle() {
        if (!playerToolsToggle || !playerToolsPanel) {
            return;
        }
        const hasActivePlayer = Boolean(document.getElementById('controller-league-player-id')?.value);
        if (hasActivePlayer) {
            playerToolsPanel.classList.add('hidden');
            playerToolsToggle.textContent = 'Expand';
        }
        playerToolsToggle.addEventListener('click', () => {
            const isHidden = playerToolsPanel.classList.toggle('hidden');
            playerToolsToggle.textContent = isHidden ? 'Expand' : 'Collapse';
        });
    }

    function initMismatchToggle() {
        if (!mismatchToggle || !mismatchBody) {
            return;
        }
        mismatchToggle.textContent = 'Expand';
        mismatchBody.classList.add('hidden');
        mismatchToggle.addEventListener('click', () => {
            const isHidden = mismatchBody.classList.toggle('hidden');
            mismatchToggle.textContent = isHidden ? 'Expand' : 'Collapse';
        });
    }

    function startNextQueued(force = false) {
        let next = null;

        if (playerQueue.length) {
            next = playerQueue[0];
            removeFromQueue(next.id);
        } else {
            next = takeRandomPlayerForCurrentPhase();
            if (!next) {
                const transition = handlePhaseTransitions({ autoStart: force });
                if (transition.pending) {
                    return false;
                }
                return false;
            }
        }

        if (!next) {
            return false;
        }

        startAuctionForPlayer(next, null, force);
        return true;
    }

    autoStartRadios?.forEach(radio => {
        radio.addEventListener('change', (event) => {
            const value = event.target.value === 'on';
            saveAutoStart(value);
        });
    });
})();
</script>
@endsection
