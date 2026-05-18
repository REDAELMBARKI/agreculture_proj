<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\AnnouncementController;

$controller = $app->make(AnnouncementController::class);
$response = $controller->getMarketplaceInitData();

echo "Marketplace init data response:\n";
echo json_encode($response->getData(true), JSON_PRETTY_PRINT);
