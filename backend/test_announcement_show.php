<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Http\Controllers\AnnouncementController;
use Illuminate\Http\Request;

echo "=== TESTING AnnouncementController::showBySlug ===" . PHP_EOL;

$product = Product::first();
echo "Using product slug: " . $product->slug . PHP_EOL;

$controller = $app->make(AnnouncementController::class);
$response = $controller->showBySlug($product);

echo PHP_EOL . "Response data:" . PHP_EOL;
echo json_encode($response->getData(true), JSON_PRETTY_PRINT);
echo PHP_EOL;

echo PHP_EOL . "Product data from response (product):" . PHP_EOL;
$productData = $response->getData(true)['product'];
echo json_encode($productData, JSON_PRETTY_PRINT);
echo PHP_EOL;

echo "Product has 'id' key? " . (isset($productData['id']) ? "YES" : "NO") . PHP_EOL;
echo "Product id value: " . ($productData['id'] ?? "NULL") . PHP_EOL;
