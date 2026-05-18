<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== USERS IN DATABASE ===" . PHP_EOL;
echo str_repeat("-", 60) . PHP_EOL;
$users = User::all(['id', 'name', 'email']);
foreach ($users as $user) {
    echo "ID: {$user->id}" . PHP_EOL;
    echo "Name: {$user->name}" . PHP_EOL;
    echo "Email: {$user->email}" . PHP_EOL;
    echo "Password for test users: password" . PHP_EOL;
    echo str_repeat("-", 60) . PHP_EOL;
}
