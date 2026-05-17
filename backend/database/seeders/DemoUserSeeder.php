<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'slug' => 'demo-user',
                'password' => Hash::make('password123'),
            ]
        );
        $demoUser->roles()->sync([2]);

        Product::factory()->count(10)->create([
            'user_id' => $demoUser->id,
            'status' => 'published',
        ]);

        Product::factory()->count(5)->create([
            'user_id' => $demoUser->id,
            'status' => 'sold',
        ]);
    }
}
