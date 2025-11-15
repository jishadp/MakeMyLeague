<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Players Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .meta {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 10px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 11px;
        }
        .empty {
            text-align: center;
            padding: 20px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Players Export</h1>
        <div class="meta">
            Generated on {{ $generatedAt->timezone(config('app.timezone'))->format('M d, Y H:i') }}
        </div>
    </div>

    @if($players->isEmpty())
        <div class="empty">No players match the current filters.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 6%;">ID</th>
                    <th style="width: 20%;">Name</th>
                    <th style="width: 14%;">Mobile</th>
                    <th style="width: 22%;">Email</th>
                    <th style="width: 13%;">Position</th>
                    <th style="width: 13%;">Local Body</th>
                    <th style="width: 12%;">Leagues</th>
                </tr>
            </thead>
            <tbody>
                @foreach($players as $player)
                    <tr>
                        <td>{{ $player->id }}</td>
                        <td>{{ $player->name }}</td>
                        <td>{{ $player->mobile }}</td>
                        <td>{{ $player->email }}</td>
                        <td>{{ $player->position->name ?? 'N/A' }}</td>
                        <td>{{ $player->localBody->name ?? 'N/A' }}</td>
                        <td>
                            {{ $player->leaguePlayers->filter(fn ($lp) => $lp->league)->pluck('league.name')->unique()->implode(', ') ?: '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
