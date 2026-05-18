<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Category;
use App\Models\Charity;
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
        // Disable foreign key checks for SQLite
        DB::statement('PRAGMA foreign_keys=OFF');

        // Clear existing data (keeping Roles)
        Address::query()->delete();
        ProductItem::query()->delete();
        Media::query()->delete();
        DB::table('subcategory_product')->delete();
        Product::query()->delete();
        Charity::query()->delete();
        User::where('role_id', '!=', 12)->delete(); // Keep main admin

        // 1. Create Users with distributed dates
        $sellers = [];
        for ($i = 1; $i <= 15; $i++) {
            $sellers[] = User::create([
                'name' => "Farmer $i",
                'email' => "farmer$i@example.com",
                'password' => Hash::make('password'),
                'role_id' => 10,
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }

        // 2. Create Agriculture Organizations and their Staff
        $organizationsData = [
            ['name' => 'Green Fields Coop', 'email' => 'contact@greenfields.org', 'contact' => 'Ahmed'],
            ['name' => 'Sustainable Agri', 'email' => 'info@sustainableagri.ma', 'contact' => 'Sanaa'],
            ['name' => 'Atlas Farming', 'email' => 'help@atlasfarming.com', 'contact' => 'Youssef'],
        ];

        foreach ($organizationsData as $index => $data) {
            $staff = User::create([
                'name' => "Staff " . $data['name'],
                'email' => "staff$index@agri.com",
                'password' => Hash::make('password'),
                'role_id' => 11,
                'created_at' => Carbon::now()->subDays(rand(10, 40)),
            ]);

            Charity::create([
                'name' => $data['name'],
                'address' => 'Agriculture Zone ' . ($index + 1),
                'email' => $data['email'],
                'contact_person' => $data['contact'],
                'user_id' => $staff->id,
                'created_at' => $staff->created_at,
            ]);
        }

        // 3. Create Products (Donate & Sell)
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->call(AnnouncementSeeder::class);
            $categories = Category::all();
        }

        $statuses = ['donate', 'sell', 'reserved', 'sold', 'donated', 'closed'];
        
        for ($i = 1; $i <= 50; $i++) {
            $mode = ($i % 2 == 0) ? 'donate' : 'sell';
            $status = fake()->randomElement($statuses);
            
            // Ensure status makes sense for mode
            if ($mode == 'donate' && $status == 'sold') $status = 'donated';
            if ($mode == 'sell' && $status == 'donated') $status = 'sold';
            // Default to mode status if not specific
            if ($status != 'reserved' && $status != 'sold' && $status != 'donated' && $status != 'closed') {
                $status = $mode;
            }
            
            $createdAt = Carbon::now()->subDays(rand(0, 30));

            $product = Product::create([
                'user_id' => fake()->randomElement($sellers)->id,
                'super_category_id' => $categories->random()->id,
                'listing_mode' => $mode,
                'listing_type' => 'single',
                'title' => ($mode == 'donate' ? "Agri Donation " : "Agri Sale ") . $i,
                'description' => "This is a test agricultural product for testing the marketplace and dashboard.",
                'price' => $mode == 'sell' ? rand(100, 5000) : null,
                'status' => $status,
                'condition' => fake()->randomElement(['Fresh', 'Standard', 'Processed']),
                'gender' => null,
                'age_range' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Add Product Items (Inventory)
            ProductItem::create([
                'product_id' => $product->id,
                'item_name' => "Unit for " . $product->title,
                'item_quantity' => rand(1, 100),
                'item_condition' => $product->condition,
                'recommended_age' => null,
                'item_gender' => null,
                'created_at' => $createdAt,
            ]);

            // Add Address
            Address::create([
                'addressable_id' => $product->id,
                'addressable_type' => Product::class,
                'city' => fake()->randomElement(['Casablanca', 'Rabat', 'Marrakech', 'Tanger']),
                'district' => 'Central',
                'address_line' => 'Test Address ' . $i,
            ]);
        }

        DB::statement('PRAGMA foreign_keys=ON');
        $this->command->info('Admin Dashboard test data seeded successfully!');
    }
}
