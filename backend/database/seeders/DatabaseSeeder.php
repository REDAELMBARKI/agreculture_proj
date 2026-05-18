<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in order
        $this->call([
            RoleSeeder::class,
            HeroSliderSeeder::class,
            BannerSeeder::class,
            FilterAttributeSeeder::class,
            AnnouncementSeeder::class,
            ReviewSeeder::class,
            OfferSeeder::class,
        ]);
        
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'slug' => 'admin-user',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => 1, // Admin role
        ]);
        
        // Create regular user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'slug' => 'test-user',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // User role
        ]);

        $superCategories = Category::query()->whereNull('parent_id')->get();
        if ($superCategories->isNotEmpty()) {
            $demoTitles = [
                'Tomates fraîches du Souss',
                'Blé dur en sacs',
                'Tracteur d’occasion - bon état',
                'Engrais NPK pour céréales',
                'Système goutte à goutte complet',
            ];

            foreach ($demoTitles as $idx => $title) {
                $product = Product::factory()->create([
                    'user_id' => $testUser->id,
                    'listing_mode' => 'sell',
                    'status' => 'published',
                    'title' => $title,
                    'slug' => Str::slug($title).'-'.$idx,
                    'super_category_id' => $superCategories->random()->id,
                    'views_count' => rand(10, 300),
                    'favorites_count' => rand(0, 25),
                ]);

                Media::query()->create([
                    'mediable_id' => $product->id,
                    'mediable_type' => Product::class,
                    'disk' => 'public',
                    'path' => 'external/'.Str::slug($title).'.jpg',
                    'url' => 'https://picsum.photos/seed/'.Str::slug($title).'/600/450.jpg',
                    'file_name' => 'thumb.jpg',
                    'mime_type' => 'image/jpeg',
                    'size' => 0,
                    'collection' => 'thumbnail',
                    'sort_order' => 0,
                    'is_temporary' => false,
                ]);
            }
        }
    }
}
