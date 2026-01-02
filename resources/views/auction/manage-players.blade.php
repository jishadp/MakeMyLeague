@extends('layouts.app')

@section('title', 'Manage Players - ' . $league->name)

@section('styles')
<style>
    .manage-players-container {
        background: radial-gradient(circle at 10% 20%, #eef2ff, #f8fafc 35%, #f1f5f9);
        min-height: 100vh;
        padding: 1.5rem;
    }

    /* User Status Bar */
    .user-status-bar {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .user-status-bar__info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-status-bar__avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 1rem;
    }

    .user-status-bar__name {
        font-weight: 700;
        color: #fff;
        font-size: 0.9rem;
    }

    .user-status-bar__role {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .user-status-bar__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #10b981;
        color: #fff;
    }

    .manage-header {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border-radius: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .manage-header__title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .manage-header__subtitle {
        font-size: 0.875rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    /* Search Bar */
    .search-bar {
        position: relative;
        margin-bottom: 1rem;
    }

    .search-bar__input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 0.85rem 1rem 0.85rem 2.75rem;
        background: #fff;
        font-weight: 600;
        color: #0f172a;
        font-size: 0.875rem;
    }

    .search-bar__input:focus {
        outline: 2px solid #6366f1;
        outline-offset: 2px;
        background: #fff;
    }

    .search-bar__icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .filter-btn {
        padding: 0.65rem 1.25rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.875rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-btn:hover {
        border-color: #6366f1;
        color: #312e81;
        background: #eef2ff;
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
    }

    .filter-btn__count {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .filter-btn:not(.active) .filter-btn__count {
        background: #f1f5f9;
        color: #64748b;
    }

    .players-card {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border-radius: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .players-table {
        width: 100%;
        border-collapse: collapse;
    }

    .players-table thead {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .players-table th {
        padding: 0.875rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .players-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .players-table tbody tr:hover {
        background: #f8fafc;
    }

    .player-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .player-cell__avatar {
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        background: #e2e8f0;
        overflow: hidden;
        flex-shrink: 0;
    }

    .player-cell__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .player-cell__info {
        min-width: 0;
    }

    .player-cell__name {
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .player-cell__role {
        font-size: 0.75rem;
        color: #64748b;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: capitalize;
    }

    .status-badge--available {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge--sold {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-badge--unsold {
        background: #fef3c7;
        color: #92400e;
    }

    .team-cell {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .team-cell__logo {
        width: 24px;
        height: 24px;
        border-radius: 0.375rem;
        background: #e2e8f0;
        overflow: hidden;
        flex-shrink: 0;
    }

    .team-cell__logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .team-cell__name {
        font-weight: 600;
        color: #0f172a;
        font-size: 0.875rem;
    }

    .amount-cell {
        font-weight: 700;
        color: #059669;
    }

    .action-btn {
        padding: 0.5rem 0.85rem;
        border-radius: 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .action-btn--primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }

    .action-btn--primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    }

    .action-btn--warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
    }

    .action-btn--warning:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.35);
    }

    .action-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #64748b;
    }

    .empty-state__icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state__text {
        font-weight: 600;
        font-size: 1rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.875rem;
        background: #fff;
        color: #475569;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .back-link:hover {
        border-color: #6366f1;
        color: #312e81;
        background: #eef2ff;
    }

    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .pagination-info {
        font-size: 0.875rem;
        color: #64748b;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pagination-btn {
        padding: 0.5rem 0.85rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .pagination-btn:hover:not(:disabled) {
        border-color: #6366f1;
        color: #312e81;
        background: #eef2ff;
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Team Statistics Section */
    .team-stats-section {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border-radius: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .team-stats-section__title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.25rem;
    }

    .team-stats-section__subtitle {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0 0 1rem;
    }

    .team-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }

    .team-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        padding: 1rem;
        transition: all 0.2s ease;
    }

    .team-card:hover {
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    }

    .team-card__header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .team-card__logo {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .team-card__logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .team-card__logo-placeholder {
        font-weight: 800;
        color: #475569;
        font-size: 1rem;
    }

    .team-card__name {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.875rem;
    }

    .team-card__label {
        font-size: 0.7rem;
        color: #64748b;
    }

    .team-card__stats {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .team-stat-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.4rem 0.6rem;
        border-radius: 0.5rem;
    }

    .team-stat-row--blue {
        background: #eff6ff;
    }

    .team-stat-row--red {
        background: #fef2f2;
    }

    .team-stat-row--green {
        background: #f0fdf4;
    }

    .team-stat-row__label {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .team-stat-row--blue .team-stat-row__label {
        color: #1e40af;
    }

    .team-stat-row--red .team-stat-row__label {
        color: #991b1b;
    }

    .team-stat-row--green .team-stat-row__label {
        color: #166534;
    }

    .team-stat-row__value {
        font-size: 0.8rem;
        font-weight: 700;
    }

    .team-stat-row--blue .team-stat-row__value {
        color: #1e40af;
    }

    .team-stat-row--red .team-stat-row__value {
        color: #991b1b;
    }

    .team-stat-row--green .team-stat-row__value {
        color: #166534;
    }

    .team-card__squad-title {
        font-size: 0.7rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .team-card__squad {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        max-height: 200px;
        overflow-y: auto;
    }

    .squad-player {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem;
        border-radius: 0.5rem;
        background: #f8fafc;
    }

    /* Replace Modal Styles */
    .user-search-container {
        margin-bottom: 1rem;
    }
    
    .user-list {
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .user-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: all 0.2s;
    }

    .user-item:last-child {
        border-bottom: none;
    }

    .user-item:hover {
        background: #f8fafc;
    }

    .user-item.selected {
        background: #eef2ff;
        box-shadow: inset 3px 0 0 #6366f1;
    }

    .user-item__avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e2e8f0;
        overflow: hidden;
        flex-shrink: 0;
    }

    .user-item__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-item__info {
        flex: 1;
        min-width: 0;
    }

    .user-item__name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #0f172a;
    }

    .user-item__sub {
        font-size: 0.75rem;
        color: #64748b;
    }

    .squad-player__avatar {
        width: 28px;
        height: 28px;
        border-radius: 0.5rem;
        background: #e2e8f0;
        overflow: hidden;
        flex-shrink: 0;
    }

    .squad-player__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .squad-player__info {
        flex: 1;
        min-width: 0;
    }

    .squad-player__name {
        font-size: 0.75rem;
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .squad-player__role {
        font-size: 0.65rem;
        color: #64748b;
    }

    .squad-player__value {
        font-size: 0.75rem;
        font-weight: 700;
        color: #059669;
        white-space: nowrap;
    }

    .confirm-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
        padding: 1rem;
    }

    .confirm-modal__card {
        background: #fff;
        border-radius: 1.25rem;
        padding: 1.5rem;
        width: min(420px, 100%);
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25);
    }

    .confirm-modal__title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.5rem;
    }

    .confirm-modal__message {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0 0 1.25rem;
    }

    .confirm-modal__warning {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
        color: #92400e;
        margin-bottom: 1.25rem;
    }

    .confirm-modal__actions {
        display: flex;
        gap: 0.75rem;
    }

    .confirm-modal__actions button {
        flex: 1;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .confirm-modal__confirm {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
    }

    .confirm-modal__cancel {
        background: #f1f5f9;
        color: #475569;
    }

    .hidden {
        display: none !important;
    }

    .feedback-toast {
        position: fixed;
        top: 1.5rem;
        right: 1.5rem;
        padding: 0.85rem 1.25rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.875rem;
        z-index: 100;
        transition: all 0.3s ease;
    }

    .feedback-toast.success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .feedback-toast.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 40;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #e2e8f0;
        border-top-color: #6366f1;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .manage-players-container {
            padding: 1rem;
        }

        .manage-header {
            padding: 1rem;
        }

        .manage-header__title {
            font-size: 1.25rem;
        }

        .user-status-bar {
            padding: 1rem;
            flex-direction: column;
            align-items: flex-start;
        }

        .user-status-bar__info {
            width: 100%;
        }

        /* Responsive Table - Card View */
        .players-table thead {
            display: none;
        }

        .players-table, 
        .players-table tbody, 
        .players-table tr, 
        .players-table td {
            display: block;
            width: 100%;
        }

        .players-table tr {
            margin-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1rem;
        }

        .players-table tr:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .players-table td {
            text-align: right;
            padding: 0.5rem 1rem;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: none;
        }

        .players-table td:before {
            content: attr(data-label);
            font-weight: 700;
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            text-align: left;
        }

        .players-table td:first-child {
            display: none; /* Hide SL on mobile */
        }

        .players-table td:nth-child(2) {
            /* Player Info Cell */
            justify-content: flex-start;
        }
        
        .players-table td:nth-child(2):before {
            display: none;
        }

        .player-cell {
            width: 100%;
        }

        .filter-bar {
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 0.5rem;
            gap: 0.5rem;
            -webkit-overflow-scrolling: touch;
        }

        .filter-btn {
            flex-shrink: 0;
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .team-stats-grid {
            grid-template-columns: 1fr;
        }

        .pagination-bar {
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .pagination-controls {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="manage-players-container">
    <!-- User Status Bar -->
    <div class="user-status-bar">
        <div class="user-status-bar__info">
            <div class="user-status-bar__avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="user-status-bar__name">{{ $user->name }}</div>
                <div class="user-status-bar__role">{{ $user->role ?? 'Organizer' }} • {{ $league->name }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="user-status-bar__badge">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Authenticated
            </span>
        </div>
    </div>

    <!-- Header -->
    <div class="manage-header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="manage-header__title">Manage Players</h1>
                <p class="manage-header__subtitle">{{ $league->name }} - Organizer Controls</p>
            </div>
            <a href="{{ route('auction.control-room', $league) }}" class="back-link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Control Room
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <svg class="search-bar__icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" 
               id="search-input" 
               class="search-bar__input" 
               placeholder="Search players by name..."
               autocomplete="off">
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar" id="filter-bar">
        <button type="button" class="filter-btn active" data-filter="all">
            All <span class="filter-btn__count" id="count-all">{{ $statusCounts['all'] }}</span>
        </button>
        <button type="button" class="filter-btn" data-filter="available">
            Available <span class="filter-btn__count" id="count-available">{{ $statusCounts['available'] }}</span>
        </button>
        <button type="button" class="filter-btn" data-filter="sold">
            Sold <span class="filter-btn__count" id="count-sold">{{ $statusCounts['sold'] }}</span>
        </button>
        <button type="button" class="filter-btn" data-filter="unsold">
            Unsold <span class="filter-btn__count" id="count-unsold">{{ $statusCounts['unsold'] }}</span>
        </button>
    </div>

    <!-- Players Table -->
    <div class="players-card">
        <table class="players-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Player</th>
                    <th>Status</th>
                    <th class="cursor-pointer hover:bg-slate-100 transition-colors" onclick="sortBy('team_name')" title="Click to sort">
                        <div class="flex items-center gap-1">
                            Team
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </div>
                    </th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="players-tbody">
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="loading-spinner" style="margin: 0 auto;"></div>
                            <p class="empty-state__text" style="margin-top: 1rem;">Loading players...</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination-bar">
            <p class="pagination-info">
                Showing <span id="showing-from">0</span> to <span id="showing-to">0</span> of <span id="total-count">0</span> players
            </p>
            <div class="pagination-controls">
                <button type="button" class="pagination-btn" id="prev-btn" disabled onclick="changePage(-1)">Previous</button>
                <span class="text-sm font-semibold text-slate-700">Page <span id="current-page">1</span> of <span id="total-pages">1</span></span>
                <button type="button" class="pagination-btn" id="next-btn" disabled onclick="changePage(1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Team Statistics Section -->
    <div class="team-stats-section">
        <h2 class="team-stats-section__title">Team Statistics</h2>
        <p class="team-stats-section__subtitle">Overview of all teams with their spending and balance</p>

        <div class="team-stats-grid">
            @foreach($teams as $team)
            <div class="team-card">
                <div class="team-card__header">
                    <div class="team-card__logo">
                        @if($team['logo'])
                            <img src="{{ $team['logo'] }}" alt="{{ $team['name'] }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="team-card__logo-placeholder" style="display: none;">{{ strtoupper(substr($team['name'], 0, 2)) }}</span>
                        @else
                            <span class="team-card__logo-placeholder">{{ strtoupper(substr($team['name'], 0, 2)) }}</span>
                        @endif
                    </div>
                    <div>
                        <div class="team-card__name">{{ $team['name'] }}</div>
                        <div class="team-card__label">Team</div>
                    </div>
                </div>

                <div class="team-card__stats">
                    <div class="team-stat-row team-stat-row--blue">
                        <div class="team-stat-row__label">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Players
                        </div>
                        <div class="team-stat-row__value">{{ $team['sold_count'] }}</div>
                    </div>

                    <div class="team-stat-row team-stat-row--red">
                        <div class="team-stat-row__label">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Spent
                        </div>
                        <div class="team-stat-row__value">₹{{ number_format($team['total_spent']) }}</div>
                    </div>

                    <div class="team-stat-row team-stat-row--green">
                        <div class="team-stat-row__label">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Balance
                        </div>
                        <div class="team-stat-row__value">₹{{ number_format($team['wallet_balance']) }}</div>
                    </div>
                </div>

                @if(count($team['sold_players']) > 0)
                <div class="team-card__squad-title">Squad</div>
                <div class="team-card__squad">
                    @foreach($team['sold_players'] as $player)
                    <div class="squad-player">
                        <div class="squad-player__avatar">
                            <img src="{{ $player['photo'] ?? asset('images/defaultplayer.jpeg') }}" 
                                 alt="{{ $player['name'] }}" 
                                 onerror="this.src='{{ asset('images/defaultplayer.jpeg') }}'">
                        </div>
                        <div class="squad-player__info">
                            <div class="squad-player__name">{{ $player['name'] }}</div>
                            <div class="squad-player__role">{{ $player['role'] }}</div>
                        </div>
                        <div class="squad-player__value">₹{{ number_format($player['bid_price']) }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-slate-400 text-center py-2">No players yet</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div id="confirm-modal" class="confirm-modal hidden">
    <div class="confirm-modal__card">
        <h3 class="confirm-modal__title" id="confirm-title">Confirm Action</h3>
        <p class="confirm-modal__message" id="confirm-message">Are you sure you want to proceed?</p>
        <div class="confirm-modal__warning hidden" id="confirm-warning"></div>
        <div class="confirm-modal__actions">
            <button type="button" class="confirm-modal__confirm" id="confirm-btn">Confirm</button>
            <button type="button" class="confirm-modal__cancel" onclick="closeConfirmModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Replace Player Modal -->
<div id="replace-modal" class="confirm-modal hidden">
    <div class="confirm-modal__card" style="width: min(500px, 100%);">
        <h3 class="confirm-modal__title">Replace Player</h3>
        <p class="confirm-modal__message">Replace <span id="replace-player-name" class="font-bold text-slate-800"></span> with a new player.</p>
        
        <div class="user-search-container">
            <div class="search-bar" style="margin-bottom: 0.5rem;">
                <svg class="search-bar__icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="replace-search-input" class="search-bar__input" placeholder="Search new player by name, email or phone...">
            </div>
            <p class="text-xs text-slate-500 pl-1">Search for users not currently in this league</p>
        </div>

        <div id="replace-user-list" class="user-list hidden">
            <!-- Populated via JS -->
        </div>

        <div id="replace-empty" class="text-center py-4 text-slate-500 hidden">
            No users found
        </div>

        <div class="confirm-modal__actions">
            <button type="button" class="confirm-modal__confirm" id="replace-confirm-btn" disabled>Replace</button>
            <button type="button" class="confirm-modal__cancel" onclick="closeReplaceModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay hidden">
    <div class="loading-spinner"></div>
</div>

<!-- Feedback Toast -->
<div id="feedback-toast" class="feedback-toast hidden"></div>

<!-- Hidden Inputs -->
<input type="hidden" id="league-slug" value="{{ $league->slug }}">
<input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
<script>
(function() {
    const leagueSlug = document.getElementById('league-slug')?.value;
    const csrfToken = document.getElementById('csrf-token')?.value;
    const tbody = document.getElementById('players-tbody');
    const filterBar = document.getElementById('filter-bar');
    const searchInput = document.getElementById('search-input');
    const confirmModal = document.getElementById('confirm-modal');
    const loadingOverlay = document.getElementById('loading-overlay');
    const feedbackToast = document.getElementById('feedback-toast');

    let allPlayers = [];
    let filteredPlayers = [];
    let currentFilter = 'all';
    let currentPage = 1;
    let searchQuery = '';
    const perPage = 15;
    let currentSort = { column: null, direction: 'asc' };

    const defaultPhoto = '{{ asset("images/defaultplayer.jpeg") }}';
    const defaultTeamLogo = '{{ asset("images/default.jpeg") }}';

    // Initialize
    fetchPlayers();

    // Search input handler
    let searchTimeout;
    searchInput?.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchQuery = e.target.value.trim().toLowerCase();
            currentPage = 1;
            applyFilters();
        }, 250);
    });

    // Filter button click handlers
    filterBar?.addEventListener('click', (e) => {
        const btn = e.target.closest('.filter-btn');
        if (!btn) return;

        const filter = btn.dataset.filter;
        if (filter === currentFilter) return;

        currentFilter = filter;
        currentPage = 1;

        // Update button states
        filterBar.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        fetchPlayers(filter);
    });

    async function fetchPlayers(filter = null) {
        if (!leagueSlug) return;

        showLoading();

        const url = new URL(`/api/leagues/${leagueSlug}/all-players`, window.location.origin);
        if (filter && filter !== 'all') {
            url.searchParams.set('status', filter);
        }

        try {
            const response = await fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();

            if (data.success) {
                allPlayers = data.players || [];
                updateCounts(data.counts);
                applyFilters();
            } else {
                showToast('Failed to load players', 'error');
            }
        } catch (error) {
            console.error('Error fetching players:', error);
            showToast('Error loading players', 'error');
        } finally {
            hideLoading();
        }
    }

    function applyFilters() {
        if (searchQuery) {
            filteredPlayers = allPlayers.filter(p => 
                p.name.toLowerCase().includes(searchQuery)
            );
        } else {
            filteredPlayers = [...allPlayers];
        }

        if (currentSort.column) {
            sortPlayers();
        }

        renderPlayers();
    }

    window.sortBy = function(column) {
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.direction = 'asc';
        }
        applyFilters();
    };

    function sortPlayers() {
        const { column, direction } = currentSort;
        if (!column) return;

        filteredPlayers.sort((a, b) => {
            let valA = a[column] || '';
            let valB = b[column] || '';

            if (typeof valA === 'string') valA = valA.toLowerCase();
            if (typeof valB === 'string') valB = valB.toLowerCase();

            if (valA < valB) return direction === 'asc' ? -1 : 1;
            if (valA > valB) return direction === 'asc' ? 1 : -1;
            return 0;
        });
    }

    function updateCounts(counts) {
        if (!counts) return;
        document.getElementById('count-all').textContent = counts.all || 0;
        document.getElementById('count-available').textContent = counts.available || 0;
        document.getElementById('count-sold').textContent = counts.sold || 0;
        document.getElementById('count-unsold').textContent = counts.unsold || 0;
    }

    function renderPlayers() {
        if (!tbody) return;

        if (filteredPlayers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-state__icon">📋</div>
                            <p class="empty-state__text">${searchQuery ? 'No players match your search' : 'No players found for this filter'}</p>
                        </div>
                    </td>
                </tr>
            `;
            updatePagination(0, 0, 0);
            return;
        }

        const totalPages = Math.ceil(filteredPlayers.length / perPage);
        const startIdx = (currentPage - 1) * perPage;
        const endIdx = Math.min(startIdx + perPage, filteredPlayers.length);
        const playersToShow = filteredPlayers.slice(startIdx, endIdx);

        tbody.innerHTML = playersToShow.map((player, idx) => {
            const serialNumber = startIdx + idx + 1;
            const statusClass = `status-badge--${player.status}`;
            const amount = player.status === 'sold' ? player.bid_price : player.base_price;

            let actionButton = '';
            if (player.status === 'unsold') {
                actionButton = `
                    <button type="button" class="action-btn action-btn--primary" onclick="confirmMakeAvailable(${player.id}, '${escapeHtml(player.name)}')">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Make Available
                    </button>
                `;
            } else if (player.status === 'available') {
                actionButton = `
                    <button type="button" class="action-btn" style="background: #f1f5f9; color: #475569;" onclick="confirmReplacePlayer(${player.id}, '${escapeHtml(player.name)}')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Replace
                    </button>
                `;
            } else if (player.status === 'sold') {
                actionButton = `
                    <button type="button" class="action-btn action-btn--warning" onclick="confirmRevertSold(${player.id}, '${escapeHtml(player.name)}', '${escapeHtml(player.team_name || '')}', ${player.bid_price || 0})">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Revert Sale
                    </button>
                `;
            } else {
                actionButton = `<span class="text-xs text-slate-400">No action</span>`;
            }

            return `
                <tr data-player-id="${player.id}">
                    <td class="font-bold text-slate-900" data-label="SL">${serialNumber}</td>
                    <td data-label="Player">
                        <div class="player-cell">
                            <div class="player-cell__avatar">
                                <img src="${player.photo || defaultPhoto}" alt="${escapeHtml(player.name)}" onerror="this.src='${defaultPhoto}'">
                            </div>
                            <div class="player-cell__info">
                                <div class="player-cell__name">${escapeHtml(player.name)}</div>
                                <div class="player-cell__role">${escapeHtml(player.role)}</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Status">
                        <span class="status-badge ${statusClass}">${player.status}</span>
                    </td>
                    <td data-label="Team">
                        ${player.team_name ? `
                            <div class="team-cell">
                                <div class="team-cell__logo">
                                    <img src="${player.team_logo || defaultTeamLogo}" alt="${escapeHtml(player.team_name)}" onerror="this.src='${defaultTeamLogo}'">
                                </div>
                                <span class="team-cell__name">${escapeHtml(player.team_name)}</span>
                            </div>
                        ` : '<span class="text-slate-400">—</span>'}
                    </td>
                    <td class="amount-cell" data-label="Amount">₹${formatNumber(amount || 0)}</td>
                    <td data-label="Actions">${actionButton}</td>
                </tr>
            `;
        }).join('');

        updatePagination(startIdx + 1, endIdx, filteredPlayers.length);
    }

    function updatePagination(from, to, total) {
        document.getElementById('showing-from').textContent = total > 0 ? from : 0;
        document.getElementById('showing-to').textContent = to;
        document.getElementById('total-count').textContent = total;

        const totalPages = Math.max(Math.ceil(total / perPage), 1);
        document.getElementById('current-page').textContent = currentPage;
        document.getElementById('total-pages').textContent = totalPages;

        document.getElementById('prev-btn').disabled = currentPage <= 1;
        document.getElementById('next-btn').disabled = currentPage >= totalPages;
    }

    window.changePage = function(direction) {
        const totalPages = Math.ceil(filteredPlayers.length / perPage);
        currentPage = Math.max(1, Math.min(currentPage + direction, totalPages));
        renderPlayers();
    };

    window.confirmMakeAvailable = function(playerId, playerName) {
        showConfirmModal({
            title: 'Make Player Available',
            message: `Are you sure you want to change "${playerName}" status to available?`,
            onConfirm: () => revertToAvailable(playerId)
        });
    };

    window.confirmRevertSold = function(playerId, playerName, teamName, amount) {
        showConfirmModal({
            title: 'Revert Sold Player',
            message: `Are you sure you want to revert "${playerName}" sale?`,
            warning: `This will refund ₹${formatNumber(amount)} to ${teamName}'s wallet and remove the player from their roster.`,
            onConfirm: () => revertToAvailable(playerId)
        });
    };

    /* --- Replacement Logic --- */
    const replaceModal = document.getElementById('replace-modal');
    const replaceSearchInput = document.getElementById('replace-search-input');
    const replaceUserList = document.getElementById('replace-user-list');
    const replaceEmptyState = document.getElementById('replace-empty');
    const replaceConfirmBtn = document.getElementById('replace-confirm-btn');
    
    let currentReplacePlayerId = null;
    let selectedNewUserId = null;
    let replaceSearchTimeout;

    window.confirmReplacePlayer = function(playerId, playerName) {
        currentReplacePlayerId = playerId;
        selectedNewUserId = null;
        document.getElementById('replace-player-name').textContent = playerName;
        replaceSearchInput.value = '';
        replaceUserList.innerHTML = '';
        replaceUserList.classList.add('hidden');
        replaceEmptyState.classList.add('hidden');
        replaceConfirmBtn.disabled = true;
        
        replaceModal?.classList.remove('hidden');
        setTimeout(() => replaceSearchInput.focus(), 100);
    };

    window.closeReplaceModal = function() {
        replaceModal?.classList.add('hidden');
    };
    
    replaceSearchInput?.addEventListener('input', (e) => {
        clearTimeout(replaceSearchTimeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            replaceUserList.innerHTML = '';
            replaceUserList.classList.add('hidden');
            return;
        }

        replaceSearchTimeout = setTimeout(() => fetchPotentialReplacements(query), 300);
    });

    async function fetchPotentialReplacements(query) {
        if (!query) return;

        replaceUserList.classList.add('hidden');
        replaceEmptyState.classList.add('hidden');
        
        try {
            const response = await fetch(`/leagues/${leagueSlug}/auction/search-replacement-players?query=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success && data.players.length > 0) {
                renderReplacementOptions(data.players);
            } else {
                replaceEmptyState.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    function renderReplacementOptions(users) {
        replaceUserList.innerHTML = users.map(user => `
            <div class="user-item" onclick="selectReplacement(${user.id}, this)">
                <div class="user-item__avatar">
                    <img src="${user.photo || defaultPhoto}" onerror="this.src='${defaultPhoto}'">
                </div>
                <div class="user-item__info">
                    <div class="user-item__name">${escapeHtml(user.name)}</div>
                    <div class="user-item__sub">${escapeHtml(user.role || 'Player')} • ${escapeHtml(user.mobile || '')}</div>
                </div>
            </div>
        `).join('');
        
        replaceUserList.classList.remove('hidden');
    }

    window.selectReplacement = function(userId, el) {
        selectedNewUserId = userId;
        
        // Update UI
        document.querySelectorAll('.user-item').forEach(item => item.classList.remove('selected'));
        el.classList.add('selected');
        
        replaceConfirmBtn.disabled = false;
    };

    replaceConfirmBtn?.addEventListener('click', async () => {
        if (!currentReplacePlayerId || !selectedNewUserId) return;
        
        closeReplaceModal();
        showLoading();
        
        try {
            const response = await fetch('/auction/replace-player', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    league_player_id: currentReplacePlayerId,
                    new_user_id: selectedNewUserId
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                fetchPlayers(currentFilter === 'all' ? null : currentFilter);
            } else {
                showToast(data.message || 'Failed to replace player', 'error');
            }
        } catch (error) {
            console.error('Replace error:', error);
            showToast('Error replacing player', 'error');
        } finally {
            hideLoading();
        }
    });

    async function revertToAvailable(playerId) {
        closeConfirmModal();
        showLoading();

        try {
            const response = await fetch('/auction/revert-to-available', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    league_player_id: playerId
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message || 'Player status updated successfully', 'success');
                // Refresh the list
                fetchPlayers(currentFilter === 'all' ? null : currentFilter);
            } else {
                showToast(data.message || 'Failed to update player status', 'error');
            }
        } catch (error) {
            console.error('Error updating player:', error);
            showToast('Error updating player status', 'error');
        } finally {
            hideLoading();
        }
    }

    function showConfirmModal({ title, message, warning, onConfirm }) {
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-message').textContent = message;

        const warningEl = document.getElementById('confirm-warning');
        if (warning) {
            warningEl.textContent = warning;
            warningEl.classList.remove('hidden');
        } else {
            warningEl.classList.add('hidden');
        }

        const confirmBtn = document.getElementById('confirm-btn');
        confirmBtn.onclick = onConfirm;

        confirmModal?.classList.remove('hidden');
    }

    window.closeConfirmModal = function() {
        confirmModal?.classList.add('hidden');
    };

    function showLoading() {
        loadingOverlay?.classList.remove('hidden');
    }

    function hideLoading() {
        loadingOverlay?.classList.add('hidden');
    }

    function showToast(message, type = 'success') {
        if (!feedbackToast) return;

        feedbackToast.textContent = message;
        feedbackToast.className = `feedback-toast ${type}`;

        setTimeout(() => {
            feedbackToast.classList.add('hidden');
        }, 4000);
    }

    function formatNumber(num) {
        return Number(num).toLocaleString('en-IN');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    // Close modal on outside click
    confirmModal?.addEventListener('click', (e) => {
        if (e.target === confirmModal) {
            closeConfirmModal();
        }
    });
})();
</script>
@endsection
