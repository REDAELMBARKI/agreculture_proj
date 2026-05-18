<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Testing User::find(2)... \n";
$userById = User::find(2);
echo "Found user by ID 2: " . ($userById?->email) . "\n";

echo "Testing User::where('slug', '2')... \n";
$userBySlug2 = User::where('slug', '2')->first();
echo "Found user by slug '2': " . ($userBySlug2?->email ?? 'none') . "\n";

echo "\nTesting getUserAnnouncementsBySlug with user ID 2...\n";
use App\Http\Controllers\AnnouncementController;
$controller = $app->make(AnnouncementController::class);
$response = $controller->getUserAnnouncementsBySlug(new \Illuminate\Http\Request(), $userById);
echo "Products in response: " . count($response->getData(true)['products']) . "\n";
