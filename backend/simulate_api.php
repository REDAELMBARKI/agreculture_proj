<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Http\Controllers\AnnouncementController;

$testUser = User::where('email', 'test@example.com')->first();
if (!$testUser) {
    die("Test user not found\n");
}

echo "Calling getUserAnnouncementsBySlug for test user...\n";
$request = new \Illuminate\Http\Request();
$controller = $app->make(AnnouncementController::class);
$response = $controller->getUserAnnouncementsBySlug($request, $testUser);

$responseData = $response->getData(true);
file_put_contents(__DIR__.'/api_response.json', json_encode($responseData, JSON_PRETTY_PRINT));

echo "Response written to api_response.json\n";
echo "Products in response: " . count($responseData['products']) . "\n";
if (count($responseData['products']) > 0) {
    echo "First product title: " . $responseData['products'][0]['title'] . "\n";
}
