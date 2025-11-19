<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Player Card - {{ $player->name }}</title>
    @include('admin.documents.partials.card-styles')
    <style>
        * {
            font-family: 'DejaVu Sans' !important;
        }
        body {
            margin: 0;
            padding: 0;
            background: #f5f7fb;
            font-family: 'DejaVu Sans' !important;
        }
    </style>
</head>
<body>
    <div class="player-doc-wrapper">
        @include('admin.documents.partials.card', [
            'photoSource' => $photoDataUri ?? $photoWebUrl,
        ])
    </div>
</body>
</html>
