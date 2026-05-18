<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$testUser = User::where('email', 'test@example.com')->first();
if (!$testUser) {
    die("Test user not found!\n");
}

echo "Test User Found: {$testUser->name} (ID: {$testUser->id})\n";
echo "Email: {$testUser->email}\n";

echo "\n--- Products --- \n";
$products = $testUser->products()->with(['thumbnail', 'superCategory'])->get();
echo "Total products: {$products->count()}\n";
foreach ($products as $p) {
    echo "- {$p->title} (status: {$p->status}, mode: {$p->listing_mode})\n";
}

echo "\n--- Conversations --- \n";
$conversationsAsSeller = $testUser->conversationsAsSeller()->count();
$conversationsAsBuyer = $testUser->conversationsAsBuyer()->count();
echo "As seller: {$conversationsAsSeller}\n";
echo "As buyer: {$conversationsAsBuyer}\n";

echo "\n--- Favorites --- \n";
$favorites = \App\Models\Favorite::where('user_id', $testUser->id)->count();
echo "Favorites given: {$favorites}\n";
echo "Favorites received: " . $testUser->products()->sum('favorites_count') . "\n";

echo "\n--- Reviews --- \n";
$reviewsReceived = \App\Models\Review::where('reviewed_id', $testUser->id)->count();
echo "Reviews received: {$reviewsReceived}\n";
