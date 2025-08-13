<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$players = \App\Models\User::with(['role', 'localBody'])
    ->players()
    ->take(5)
    ->get();

echo "Sample Players:\n";
foreach ($players as $player) {
    echo "Name: {$player->name}, Role: {$player->role->name}, Location: {$player->localBody->name}\n";
}
