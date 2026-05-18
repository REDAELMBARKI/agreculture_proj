<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\ChatController;
use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING CHAT ===" . PHP_EOL;

// Get first product
$product = Product::first();
echo "Product found: " . $product->id . " (" . $product->title . ")" . PHP_EOL;
echo "Product slug: " . $product->slug . PHP_EOL;

// Get two different users
$user1 = User::first();
$user2 = User::skip(1)->first() ?? User::create(['name' => 'Test User 2', 'email' => 'test2@example.com', 'password' => bcrypt('password')]);

Auth::login($user1);

echo PHP_EOL . "Calling ChatService->getOrCreateConversation() with product " . $product->id . PHP_EOL;
$chatService = $app->make(ChatService::class);
$conversation = $chatService->getOrCreateConversation($product);

echo PHP_EOL . "Conversation created/retrieved! Slug: " . $conversation->slug . PHP_EOL;
var_dump($conversation->toArray());
