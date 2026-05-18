<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING ChatController::getOrCreateConversation ===" . PHP_EOL;

// Get first product
$product = Product::first();
echo "Product: id={$product->id}, slug={$product->slug}" . PHP_EOL;

// Get two different users
$user1 = User::first();
$user2 = User::skip(1)->first() ?? User::create(['name' => 'Test User 2', 'email' => 'test2@example.com', 'password' => bcrypt('password')]);
echo "Users: {$user1->id} (buyer), {$user2->id} (seller)" . PHP_EOL;

Auth::login($user1);

// Call ChatController
$controller = $app->make(ChatController::class);
echo PHP_EOL . "Calling controller method..." . PHP_EOL;
try {
    $response = $controller->getOrCreateConversation($product);
    echo "Response status code: " . $response->getStatusCode() . PHP_EOL;
    echo "Response data: " . json_encode($response->getData(true), JSON_PRETTY_PRINT) . PHP_EOL;
} catch (\Exception $e) {
    echo PHP_EOL . "EXCEPTION CAUGHT!" . PHP_EOL;
    echo "Message: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    echo "Trace:\n" . $e->getTraceAsString() . PHP_EOL;
}
