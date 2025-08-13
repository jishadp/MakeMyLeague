<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$allUsers = \App\Models\User::with(['role', 'localBody'])->get();

echo "All Users:\n";
foreach ($allUsers as $user) {
    $role = $user->role ? $user->role->name : 'No Role (Regular User)';
    $location = $user->localBody ? $user->localBody->name : 'No Location';
    echo "Name: {$user->name}, Role: {$role}, Location: {$location}\n";
}
