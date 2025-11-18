@extends('layouts.app')

@section('title', 'Auction Control Room - ' . $league->name)

@section('styles')
<style>
    .control-room {
        background: #f5f5f5;
    }
    .control-card {
        background: #fff;
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        padding: 1.5rem;
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
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
    }
    @media (max-width: 768px) {
        .team-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    .team-pill {
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        padding: 0.9rem;
        text-align: left;
        background: #fff;
        min-height: 74px;
        transition: all 0.2s ease;
        width: 100%;
    }
    .team-pill.active {
        border-color: #10b981;
        background: #ecfdf5;
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
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-6 space-y-6">
        <input type="hidden" id="controller-league-id" value="{{ $league->id }}">
        <input type="hidden" id="controller-league-slug" value="{{ $league->slug }}">
        <input type="hidden" id="controller-player-id" value="{{ $currentPlayer->player->id ?? '' }}">
        <input type="hidden" id="controller-league-player-id" value="{{ $currentPlayer->id ?? '' }}">
        <input type="hidden" id="controller-base-price" value="{{ $currentBidAmount }}">
        <input type="hidden" id="controller-default-team" value="{{ $currentHighestBid?->league_team_id ?? '' }}">
        <input type="hidden" id="controller-bid-increments" value='@json($bidIncrements)'>
        <input type="hidden" id="controller-bid-action" value="{{ route('auction.call') }}">
        <input type="hidden" id="controller-sold-action" value="{{ route('auction.sold') }}">
        <input type="hidden" id="controller-unsold-action" value="{{ route('auction.unsold') }}">

        <div class="control-card control-header space-y-3">
            <div class="flex items-center justify-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                <span>{{ $league->isAuctionActive() ? 'Live Auction' : 'Standby' }}</span>
                <span>•</span>
                <span>Season {{ $league->season }}</span>
            </div>
            <h1>Control Room</h1>
            <p class="text-sm text-slate-500">{{ $league->name }} • {{ $league->game->name ?? 'Game TBA' }} • {{ $league->league_teams_count }} teams</p>
            @if(isset($switchableLeagues) && $switchableLeagues->count() > 0)
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-center text-sm">
                    <label for="leagueSwitchSelect" class="font-semibold text-slate-600">Switch League</label>
                    <select id="leagueSwitchSelect" class="rounded-full border border-slate-200 px-4 py-2 text-center text-sm font-semibold text-slate-700 focus:border-indigo-500 focus:outline-none" onchange="if (this.value) window.location.href = this.value;">
                        @foreach($switchableLeagues as $switchLeague)
                            <option value="{{ route('auction.control-room', $switchLeague) }}" @selected($switchLeague->id === $league->id)>
                                {{ $switchLeague->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div id="controller-feedback" class="control-alert hidden"></div>

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
        </div>

        <div class="control-card space-y-5">
            <input id="controller-custom-amount" type="hidden" data-default-increment="{{ $firstBidIncrement }}" value="{{ $currentBidAmount > 0 ? $currentBidAmount + $firstBidIncrement : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-600 mb-1">Quick Bid Amount</p>
                    <p id="selected-team-label" class="text-xs text-slate-500">
                        Selected team: <span data-selected-team>{{ $currentHighestBid?->leagueTeam?->team?->name ?? 'None' }}</span>
                    </p>
                    <p class="text-xs text-slate-500">
                        Needs <span data-selected-need>0</span> • Reserve <span data-selected-reserve>₹0</span> • Max bid <span data-selected-max>₹0</span>
                    </p>
                </div>
                <button type="button" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700" data-edit-quick {{ $currentPlayer ? '' : 'disabled' }}>
                    Change amount
                </button>
            </div>
            <div class="quick-grid">
                @if($bidIncrementValues->isNotEmpty())
                    <button type="button" class="quick-button {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" data-quick-trigger {{ $currentPlayer ? '' : 'disabled' }}>
                        +₹{{ number_format($bidIncrementValues->first()) }}
                    </button>
                @endif
            </div>
            <button type="button" onclick="placeControllerBid(this)" class="next-amount-btn {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" {{ $currentPlayer ? '' : 'disabled' }}>
                <span class="uppercase text-xs tracking-wide text-white/80">Bid</span>
                <span id="controller-bid-preview" class="text-2xl">₹{{ number_format($currentBidAmount) }}</span>
            </button>
            <p class="text-center text-xs text-slate-400">Use the quick bid button to queue the next amount, then press Bid.</p>
        </div>

        <div class="control-card space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-600">Choose Team</p>
                    <p class="text-xs text-slate-500">Tap to assign the bid target.</p>
                </div>
                <a href="{{ route('auction.index', $league) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Open live auction</a>
            </div>
            <input type="hidden" id="controller-team" value="{{ $currentHighestBid?->league_team_id ?? '' }}">
            <div class="team-grid">
                @foreach($teams as $team)
                    <button type="button"
                        class="team-pill {{ $currentHighestBid?->league_team_id === $team->id ? 'active' : '' }}"
                        data-team-pill="{{ $team->id }}"
                        data-team-name="{{ $team->team?->name ?? 'Team #' . $team->id }}"
                        data-team-reserve="{{ $team->reserve_amount }}"
                        data-team-max="{{ $team->max_bid_cap }}"
                        data-team-needed="{{ $team->players_needed }}">
                        <p class="text-xs text-slate-400 uppercase">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::limit($team->team?->name ?? 'Team #' . $team->id, 16, '')) }}</p>
                        <p class="font-semibold text-slate-900">{{ $team->team?->name ?? 'Team #' . $team->id }}</p>
                        <p class="text-xs text-slate-500">Players {{ $team->sold_players_count }} · Wallet ₹{{ number_format($team->display_wallet ?? $team->wallet_balance ?? 0) }}</p>
                        <p class="text-xs text-emerald-600">Needs {{ $team->players_needed }} • Reserve ₹{{ number_format($team->reserve_amount) }}</p>
                        <p class="text-xs text-indigo-600">Max bid this player: ₹{{ number_format($team->max_bid_cap) }}</p>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="control-card space-y-4">
            <input type="hidden" id="controller-override-amount" value="">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-slate-600">Override Sold Amount</p>
                    <p class="text-xs text-slate-500" data-override-label>Current override: None</p>
                </div>
                <button type="button" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" data-edit-override {{ $currentPlayer ? '' : 'disabled' }}>
                    Set override
                </button>
            </div>
            <div class="control-footer">
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" data-controller-sold class="flex-1 px-6 py-3 rounded-2xl bg-emerald-500 text-white font-semibold shadow-lg hover:bg-emerald-600 transition {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" onclick="markControllerSold(this)" {{ $currentPlayer ? '' : 'disabled' }}>
                        Sold
                    </button>
                    <button type="button" onclick="markControllerUnsold(this)" class="flex-1 px-6 py-3 rounded-2xl bg-rose-500 text-white font-semibold shadow-lg hover:bg-rose-600 transition {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" {{ $currentPlayer ? '' : 'disabled' }}>
                        Unsold
                    </button>
                </div>
            </div>
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
(function() {
    const controllerBidInput = document.getElementById('controller-custom-amount');
    const controllerBaseInput = document.getElementById('controller-base-price');
    const controllerPreview = document.getElementById('controller-bid-preview');
    const controllerTeamSelect = document.getElementById('controller-team');
    const controllerDefaultTeam = document.getElementById('controller-default-team');
    const teamPills = document.querySelectorAll('[data-team-pill]');
    const soldButton = document.querySelector('[data-controller-sold]');
    const quickButton = document.querySelector('[data-quick-trigger]');
    const quickEditButton = document.querySelector('[data-edit-quick]');
    const selectedTeamLabel = document.querySelector('[data-selected-team]');
    const overrideButton = document.querySelector('[data-edit-override]');
    const overrideInput = document.getElementById('controller-override-amount');
    const overrideLabel = document.querySelector('[data-override-label]');
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
    const quickStorageKey = leagueIdValue ? `league_${leagueIdValue}_quick_increment` : null;
    let quickIncrementValue = Number(controllerBidInput?.dataset.defaultIncrement || 0);
    if (quickStorageKey) {
        const storedQuick = Number(localStorage.getItem(quickStorageKey));
        if (storedQuick && storedQuick > 0) {
            quickIncrementValue = storedQuick;
            const base = Number(controllerBaseInput?.value || 0);
            if (controllerBidInput) {
                controllerBidInput.dataset.defaultIncrement = storedQuick;
                controllerBidInput.value = base + storedQuick;
                updatePreview(base + storedQuick);
            }
        } else if (quickIncrementValue > 0) {
            localStorage.setItem(quickStorageKey, String(quickIncrementValue));
        }
    }
    let modalState = null;
    const teamNameMap = {};
    teamPills.forEach(button => {
        teamNameMap[button.dataset.teamPill] = button.dataset.teamName || button.textContent.trim();
    });

    function formatCurrency(value) {
        const amount = Number(value) || 0;
        return '₹' + amount.toLocaleString('en-IN');
    }

    function updatePreview(value) {
        if (controllerPreview) {
            controllerPreview.textContent = formatCurrency(value);
        }
    }

    function highlightSelectedTeam(teamId) {
        teamPills.forEach(button => {
            if (button.dataset.teamPill === teamId) {
                button.classList.add('active');
            } else {
                button.classList.remove('active');
            }
        });
    }

    function setTeamSelection(teamId) {
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

    teamPills.forEach(button => {
        button.addEventListener('click', () => {
            setTeamSelection(button.dataset.teamPill);
        });
    });

    if (controllerTeamSelect) {
        if (!controllerTeamSelect.value && controllerDefaultTeam && controllerDefaultTeam.value) {
            setTeamSelection(controllerDefaultTeam.value);
        } else if (controllerTeamSelect.value) {
            setTeamSelection(controllerTeamSelect.value);
        } else {
            updateTeamBudgetLabels(null);
        }
        if (selectedTeamLabel) {
            selectedTeamLabel.textContent = getSelectedTeamName() || 'None';
        }
    }
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
            if (quickIncrementValue > 0) {
                quickButton.textContent = `+₹${quickIncrementValue.toLocaleString('en-IN')}`;
            } else {
                quickButton.textContent = 'Set amount';
            }
        }
    }

    function applyQuickBid() {
        if (!controllerBidInput || !controllerBaseInput) {
            return;
        }
        const base = Number(controllerBaseInput.value || 0);
        const target = quickIncrementValue && quickIncrementValue > 0
            ? base + quickIncrementValue
            : base;
        controllerBidInput.value = target;
        updatePreview(target);
    }

    if (quickButton) {
        quickButton.addEventListener('click', () => {
            applyQuickBid();
        });
    }

    if (quickEditButton) {
        quickEditButton.addEventListener('click', () => {
            openValueModal({
                title: 'Set quick bid increment',
                placeholder: 'Amount in ₹',
                defaultValue: quickIncrementValue || '',
                onConfirm(value) {
                    const parsed = Number(value);
                    if (!parsed || parsed <= 0) {
                        showControllerMessage('Enter a valid amount greater than zero.', 'error');
                        return false;
                    }
                    quickIncrementValue = parsed;
                    if (controllerBidInput) {
                        controllerBidInput.dataset.defaultIncrement = parsed;
                    }
                    if (quickStorageKey) {
                        localStorage.setItem(quickStorageKey, String(parsed));
                    }
                    updateQuickButtonLabel();
                    showControllerMessage('Quick amount updated.');
                    applyQuickBid();
                    return true;
                }
            });
        });
    }

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
        if (event.key === 'Escape' && !modal?.classList.contains('hidden')) {
            closeValueModal();
        }
    });

    updateQuickButtonLabel();
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
            const nextTarget = quickIncrementValue > 0 ? targetAmount + quickIncrementValue : targetAmount;
            controllerBidInput.value = nextTarget;
            updatePreview(nextTarget);
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

    updatePreview(controllerBidInput?.value || controllerBaseInput?.value || 0);
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

        const action = document.getElementById('controller-sold-action')?.value;
        const token = getControllerToken();
        if (!action || !token) {
            showControllerMessage('Missing configuration for sold action.', 'error');
            return;
        }

        const leaguePlayerId = document.getElementById('controller-league-player-id').value;
        const overrideAmount = document.getElementById('controller-override-amount')?.value;

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
                    override_amount: overrideAmount || null
                })
            });

            const data = await response.json().catch(() => ({ success: false }));

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to mark player as sold.');
            }

            showControllerMessage('Player marked as sold. Refreshing...', 'success');
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
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            showControllerMessage(error.message || 'Unable to mark player as unsold.', 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
})();
</script>
@endsection
