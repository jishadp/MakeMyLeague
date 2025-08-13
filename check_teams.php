<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$teams = \App\Models\Team::select('id', 'name', 'slug')->get();

foreach ($teams as $team) {
    echo "ID: {$team->id}, Name: {$team->name}, Slug: {$team->slug}" . PHP_EOL;
}
