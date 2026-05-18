<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

$controller = $app->make(AuthController::class);
$request = Request::create('/login', 'POST', [
    'email' => 'test@example.com',
    'password' => 'password',
]);

$response = $controller->login($request);

echo "Login response status: " . $response->status() . "\n";
echo json_encode($response->getData(true), JSON_PRETTY_PRINT);
