@php
    use Illuminate\Support\Str;

    $playerIdDisplay = str_pad((string) $player->id, 4, '0', STR_PAD_LEFT);
    $primaryLeagueName = $primaryLeague->name ?? 'Independent Player';
    $leagueSeason = $primaryLeague?->season ? 'Season ' . $primaryLeague->season : 'Multi-league profile';
    $teamName = $primaryTeam->name ?? 'Not Assigned';
    $statusLabel = $leaguePlayerContext?->status ? Str::headline($leaguePlayerContext->status) : 'Registration Pending';
    $bidValue = $leaguePlayerContext?->bid_price ?? $leaguePlayerContext?->base_price;
    $basePrice = $leaguePlayerContext?->base_price;
    $retained = $leaguePlayerContext?->retention ? 'Retention Player' : 'Open Pool';
    $leagueList = $leagueNames ?? collect();
    $generatedOn = $generatedAt ?? now();
    $contactNumber = $player->formatted_phone_number ?? trim(($player->country_code ?? '') . ' ' . ($player->mobile ?? ''));
    $contactNumber = $contactNumber ?: 'Not provided';
    $email = $player->email ?? 'Not provided';
    $localBody = optional($player->localBody)->name ?? 'Not provided';
    $district = optional(optional($player->localBody)->district)->name;
    $state = optional(optional(optional($player->localBody)->district)->state)->name;
    $locationLine = trim(collect([$localBody !== 'Not provided' ? $localBody : null, $district, $state])->filter()->implode(', '));
    $locationLine = $locationLine ?: 'No location submitted';
    $registeredDate = optional($player->created_at)?->format('F j, Y') ?? 'Not available';
    $contextLeagueSeason = $primaryLeague?->season ? 'Season ' . $primaryLeague->season : null;
@endphp

<div class="player-doc-card">
    <div class="player-doc-header">
        <div>
            <div class="player-doc-eyebrow">Player Document</div>
            <h1 class="player-doc-title">{{ $player->name }}</h1>
            <p class="player-doc-subtitle">
                {{ $primaryLeagueName }} · {{ $leagueSeason }}
            </p>
        </div>
        <div class="player-doc-badge">
            <span>Printable ID</span>
            <strong>#{{ $playerIdDisplay }}</strong>
            <small>{{ $player->slug }}</small>
        </div>
    </div>

    <div class="player-doc-body">
        <div class="player-doc-profile">
            <div class="player-doc-photo">
                @if(!empty($photoSource))
                    <img src="{{ $photoSource }}" alt="{{ $player->name }}">
                @else
                    <div style="width: 180px; height: 180px; border-radius: 999px; background:#e2e8f0; display:flex; align-items:center; justify-content:center; font-size:64px; font-weight:700; color:#94a3b8;">
                        {{ Str::upper(Str::substr($player->name, 0, 1)) }}
                    </div>
                @endif
                <span>{{ $primaryLeagueName }}</span>
            </div>
            <div class="player-doc-details">
                <div class="player-doc-detail">
                    <div class="label">Role</div>
                    <div class="value">{{ optional($player->position)->name ?? 'Not assigned' }}</div>
                </div>
                <div class="player-doc-detail">
                    <div class="label">Team</div>
                    <div class="value">{{ $teamName }}</div>
                </div>
                <div class="player-doc-detail">
                    <div class="label">League Focus</div>
                    <div class="value">{{ $primaryLeagueName }}</div>
                </div>
                <div class="player-doc-detail">
                    <div class="label">Contact</div>
                    <div class="value">{{ $contactNumber }}</div>
                </div>
                <div class="player-doc-detail">
                    <div class="label">Email</div>
                    <div class="value">{{ $email }}</div>
                </div>
                <div class="player-doc-detail">
                    <div class="label">Base Location</div>
                    <div class="value">{{ $locationLine }}</div>
                </div>
                <div class="player-doc-detail">
                    <div class="label">Registered On</div>
                    <div class="value">{{ $registeredDate }}</div>
                </div>
                <div class="player-doc-detail">
                    <div class="label">Document Scope</div>
                    <div class="value">{{ $contextLeagueSeason ?? 'Global Profile' }}</div>
                </div>
            </div>
        </div>

        <div class="player-doc-grid">
            <div class="player-doc-tile">
                <h4>League Assignment</h4>
                <div class="value">{{ $primaryLeagueName }}</div>
                <div class="meta">{{ $statusLabel }}</div>
            </div>
            <div class="player-doc-tile">
                <h4>Contract Value</h4>
                <div class="value">
                    @if($bidValue)
                        ₹{{ number_format($bidValue, 2) }}
                    @else
                        Not finalized
                    @endif
                </div>
                @if($basePrice)
                    <div class="meta">Base ₹{{ number_format($basePrice, 2) }} · {{ $retained }}</div>
                @else
                    <div class="meta">{{ $retained }}</div>
                @endif
            </div>
        </div>

        <div class="player-doc-columns">
            <div class="player-doc-list">
                <h5>League Participation</h5>
                @if($leagueList->isNotEmpty())
                    <ul>
                        @foreach($leagueList as $leagueName)
                            <li>{{ $leagueName }}</li>
                        @endforeach
                    </ul>
                @else
                    <p style="margin:0;color:#94a3b8;font-size:14px;">No league registrations recorded yet.</p>
                @endif
            </div>
            <div class="player-doc-list">
                <h5>Document Details</h5>
                <ul>
                    <li>Generated {{ $generatedOn->format('F j, Y g:i A') }}</li>
                    <li>Player Ref: PLY-{{ $playerIdDisplay }}</li>
                    <li>Status: {{ $statusLabel }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="player-doc-footer">
        <span>{{ config('app.name') }} · Printable ID {{ $playerIdDisplay }}</span>
        <span>{{ strtoupper(config('app.env')) }} • {{ $generatedOn->format('d M Y') }}</span>
    </div>
</div>
