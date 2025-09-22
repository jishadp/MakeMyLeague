<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $league->name }} - Fixtures</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .league-name { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .season { font-size: 14px; color: #666; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; padding: 5px; background-color: #f5f5f5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; font-weight: bold; }
        .match-teams { font-weight: bold; }
        .status-scheduled { color: #2563eb; }
        .status-unscheduled { color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <div class="league-name">{{ $league->name }}</div>
        <div class="season">Season {{ $league->season }} - Fixtures</div>
        <div style="font-size: 10px; color: #999; margin-top: 10px;">Generated on {{ now()->format('d M Y, H:i') }}</div>
    </div>

    @php
        $groupFixtures = $fixtures->where('match_type', 'group_stage')->groupBy('leagueGroup.name');
        $knockoutFixtures = $fixtures->where('match_type', '!=', 'group_stage')->groupBy('match_type');
    @endphp

    <!-- Group Stage Fixtures -->
    @foreach($groupFixtures as $groupName => $groupMatches)
        <div class="section">
            <div class="section-title">{{ $groupName }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="40%">Match</th>
                        <th width="15%">Date</th>
                        <th width="10%">Time</th>
                        <th width="25%">Venue</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupMatches as $fixture)
                        <tr>
                            <td class="match-teams">{{ $fixture->homeTeam->team->name }} vs {{ $fixture->awayTeam->team->name }}</td>
                            <td>{{ $fixture->match_date ? $fixture->match_date->format('d M Y') : 'TBD' }}</td>
                            <td>{{ $fixture->match_time ? $fixture->match_time->format('H:i') : 'TBD' }}</td>
                            <td>{{ $fixture->venue ?? 'TBD' }}</td>
                            <td class="status-{{ $fixture->status }}">{{ ucfirst(str_replace('_', ' ', $fixture->status)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <!-- Knockout Stage Fixtures -->
    @if($knockoutFixtures->has('quarter_final'))
        <div class="section">
            <div class="section-title">Quarter Finals</div>
            <table>
                <thead>
                    <tr>
                        <th width="40%">Match</th>
                        <th width="15%">Date</th>
                        <th width="10%">Time</th>
                        <th width="25%">Venue</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($knockoutFixtures['quarter_final'] as $fixture)
                        <tr>
                            <td class="match-teams">{{ $fixture->homeTeam->team->name }} vs {{ $fixture->awayTeam->team->name }}</td>
                            <td>{{ $fixture->match_date ? $fixture->match_date->format('d M Y') : 'TBD' }}</td>
                            <td>{{ $fixture->match_time ? $fixture->match_time->format('H:i') : 'TBD' }}</td>
                            <td>{{ $fixture->venue ?? 'TBD' }}</td>
                            <td class="status-{{ $fixture->status }}">{{ ucfirst(str_replace('_', ' ', $fixture->status)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($knockoutFixtures->has('semi_final'))
        <div class="section">
            <div class="section-title">Semi Finals</div>
            <table>
                <thead>
                    <tr>
                        <th width="40%">Match</th>
                        <th width="15%">Date</th>
                        <th width="10%">Time</th>
                        <th width="25%">Venue</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($knockoutFixtures['semi_final'] as $fixture)
                        <tr>
                            <td class="match-teams">{{ $fixture->homeTeam->team->name }} vs {{ $fixture->awayTeam->team->name }}</td>
                            <td>{{ $fixture->match_date ? $fixture->match_date->format('d M Y') : 'TBD' }}</td>
                            <td>{{ $fixture->match_time ? $fixture->match_time->format('H:i') : 'TBD' }}</td>
                            <td>{{ $fixture->venue ?? 'TBD' }}</td>
                            <td class="status-{{ $fixture->status }}">{{ ucfirst(str_replace('_', ' ', $fixture->status)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($knockoutFixtures->has('final'))
        <div class="section">
            <div class="section-title">Final</div>
            <table>
                <thead>
                    <tr>
                        <th width="40%">Match</th>
                        <th width="15%">Date</th>
                        <th width="10%">Time</th>
                        <th width="25%">Venue</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($knockoutFixtures['final'] as $fixture)
                        <tr>
                            <td class="match-teams">{{ $fixture->homeTeam->team->name }} vs {{ $fixture->awayTeam->team->name }}</td>
                            <td>{{ $fixture->match_date ? $fixture->match_date->format('d M Y') : 'TBD' }}</td>
                            <td>{{ $fixture->match_time ? $fixture->match_time->format('H:i') : 'TBD' }}</td>
                            <td>{{ $fixture->venue ?? 'TBD' }}</td>
                            <td class="status-{{ $fixture->status }}">{{ ucfirst(str_replace('_', ' ', $fixture->status)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>