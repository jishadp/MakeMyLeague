<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>League Player Roster</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'DejaVu Sans' !important;
        }
        body {
            font-family: 'DejaVu Sans' !important;
            color: #0f172a;
            margin: 24px;
            background: #f8fafc;
        }
        .roster-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e2e8f0;
        }
        .eyebrow {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.35em;
            color: #6366f1;
            margin-bottom: 4px;
        }
        .roster-title {
            font-size: 26px;
            margin: 0;
            color: #111827;
        }
        .roster-subtitle {
            margin: 6px 0 0;
            color: #475569;
            font-size: 14px;
        }
        .roster-meta {
            text-align: right;
            color: #475569;
            font-size: 12px;
        }
        .roster-meta span {
            display: block;
            margin-bottom: 4px;
        }
        .roster-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
        }
        .player-card {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 16px;
            background: #fff;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .card-header {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .serial-badge {
            position: absolute;
            top: -6px;
            left: -6px;
            background: #0f172a;
            color: #fff;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }
        .photo-wrapper {
            position: relative;
        }
        .player-photo {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            object-fit: cover;
            border: 2px solid #e0e7ff;
        }
        .photo-placeholder {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: #e0e7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #475569;
            font-size: 18px;
            border: 2px solid #e0e7ff;
        }
        .player-name {
            font-size: 15px;
            font-weight: 700;
            margin: 0;
            color: #0f172a;
        }
        .player-meta {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: #94a3b8;
            margin: 0;
        }
        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .chip {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.08em;
            background: #eef2ff;
            color: #3730a3;
        }
        .chip-retention {
            background: #fef3c7;
            color: #b45309;
        }
        .card-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 8px;
        }
        .label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            color: #94a3b8;
        }
        .value {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }
        .empty-state {
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
            padding: 32px;
        }
    </style>
</head>
<body>
@php
    $leagueLabel = $leagueBadge ?? ($league
        ? trim($league->name . ($league->season ? ' · Season ' . $league->season : ''))
        : 'All Leagues');
@endphp
<div class="roster-header">
    <div>
        <div class="eyebrow">League Documents</div>
        <h1 class="roster-title">{{ $leagueLabel }} Players</h1>
        <p class="roster-subtitle">
            {{ $retentionFilterLabel }}
            @if($searchTerm)
                • Search: “{{ $searchTerm }}”
            @endif
        </p>
    </div>
    <div class="roster-meta">
        <span>Generated: {{ $generatedAt->format('d M Y, g:i A') }}</span>
        <span>Total players: {{ number_format($playerCount) }}</span>
    </div>
</div>

<div class="roster-grid">
    @forelse($players as $player)
        <div class="player-card">
            <div class="card-header">
                <div class="photo-wrapper">
                    @if(!empty($player['photo']))
                        <img src="{{ $player['photo'] }}" alt="{{ $player['name'] }}" class="player-photo">
                    @else
                        <div class="photo-placeholder">{{ strtoupper(\Illuminate\Support\Str::substr($player['name'] ?? '?', 0, 1)) }}</div>
                    @endif
                    <div class="serial-badge">{{ $player['serial'] }}</div>
                </div>
                <div>
                    <p class="player-meta">{{ $player['team'] ?? $player['league_short'] ?? $player['league_name'] ?? 'Free Agent' }}</p>
                    <p class="player-name">{{ $player['name'] }}</p>
                    <div class="chips">
                        <span class="chip">{{ $player['role'] }}</span>
                        @if($player['retained'])
                            <span class="chip chip-retention">Retention</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <p class="label">Place</p>
                    <p class="value">{{ $player['place'] }}</p>
                </div>
                <div>
                    <p class="label">Phone</p>
                    <p class="value">{{ $player['phone'] }}</p>
                </div>
            </div>
        </div>
    @empty
        <p class="empty-state">No players matched the selected filters.</p>
    @endforelse
</div>
</body>
</html>
