<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminDashboardSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data (keeping Roles)
        Address::query()->delete();
        ProductItem::query()->delete();
        Media::query()->delete();
        DB::table('subcategory_product')->delete();
        Product::query()->delete();
        User::whereDoesntHave('roles', function($q) { $q->where('roles.id', 12); })->delete(); // Keep main admin

        // 1. Create Users with distributed dates
        $sellers = [];
        for ($i = 1; $i <= 15; $i++) {
            $user = User::create([
                'name' => "Seller $i",
                'email' => "seller$i@example.com",
                'password' => Hash::make('password'),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
            $user->roles()->attach(10);
            $sellers[] = $user;
        }

        // 2. Create Products
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->call(AnnouncementSeeder::class);
            $categories = Category::all();
        }

        $statuses = ['reserved', 'sold', 'closed', 'published', 'draft'];
        
        for ($i = 1; $i <= 50; $i++) {
            $status = fake()->randomElement($statuses);
            $createdAt = Carbon::now()->subDays(rand(0, 30));

            $product = Product::create([
                'user_id' => fake()->randomElement($sellers)->id,
                'super_category_id' => $categories->random()->id,
                'title' => "Marketplace Item " . $i,
                'description' => "This is a test product for testing the admin dashboard charts and tables.",
                'price' => rand(50, 1000),
                'status' => $status,
                'condition' => fake()->randomElement(['New', 'Used', 'Like New']),
                'gender' => fake()->randomElement(['Boy', 'Girl', 'Unisex']),
                'age_range' => fake()->randomElement(['0-3m', '1-3y', '5-8y']),
                'contact_phone' => '06' . fake()->numerify('########'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Add Product Items
            ProductItem::create([
                'product_id' => $product->id,
                'item_name' => "Item for " . $product->title,
                'item_quantity' => rand(1, 5),
                'item_condition' => $product->condition,
                'recommended_age' => $product->age_range,
                'item_gender' => $product->gender,
                'created_at' => $createdAt,
            ]);

            // Add Address
            $randomCity = \App\Models\City::inRandomOrder()->first();
            if ($randomCity) {
                Address::create([
                    'addressable_id' => $product->id,
                    'addressable_type' => Product::class,
                    'city_id' => $randomCity->id,
                    'district' => 'Central',
                    'address_line' => 'Test Address ' . $i,
                ]);
            }
        }

        $this->command->info('Admin Dashboard test data seeded successfully!');
    }
}
