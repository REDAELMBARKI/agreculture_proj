<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Favorite;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user if doesn't exist
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'slug' => 'test-user',
                'password' => Hash::make('password'),
                'role_id' => 2,
            ]
        );

        // Create other users if not exist
        $otherUserEmails = [
            'fatima@example.com' => 'Fatima Alami',
            'youssef@example.com' => 'Youssef Benkiran',
            'amina@example.com' => 'Amina Rachidi',
            'karim@example.com' => 'Karim El Mardi',
            'sofia@example.com' => 'Sofia Mansouri',
        ];

        $otherUsers = collect();
        foreach ($otherUserEmails as $email => $name) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'password' => Hash::make('password'),
                    'role_id' => 2,
                ]
            );
            $otherUsers->push($user);
        }

        // Ensure categories exist
        $this->createCategories();
        $superCategories = Category::whereNull('parent_id')->get();
        $subCategories = Category::whereNotNull('parent_id')->get();
        if ($superCategories->isEmpty() || $subCategories->isEmpty()) {
            $this->command->error('Categories not found!');
            return;
        }

        // Create products for test user
        $demoTitles = [
            'Tomates fraîches du Souss',
            'Blé dur en sacs de 50kg',
            'Tracteur d’occasion - bon état',
            'Engrais NPK pour céréales',
            'Système goutte à goutte complet',
            'Oranges biologiques de Berkane',
            'Oignons rouges de Meknès',
            'Pommes de terre de saison',
            'Moutons de race Sardi',
            'Poulets de chair fermiers',
        ];

        $products = collect();
        foreach ($demoTitles as $idx => $title) {
            $superCat = $superCategories->random();
            $possibleSubCats = $subCategories->where('parent_id', $superCat->id);
            $subCat = $possibleSubCats->isNotEmpty() ? $possibleSubCats->random() : $subCategories->random();
            $mode = $idx % 2 === 0 ? 'sell' : 'donate';
            $statuses = ['published', 'published', 'published', 'reserved', 'sold', 'closed'];
            $status = $statuses[array_rand($statuses)];
            $createdAt = Carbon::today()->subDays(rand(0, 29));

            // Create product
            $product = Product::create([
                'user_id' => $testUser->id,
                'listing_mode' => $mode,
                'listing_type' => 'single',
                'title' => $title,
                'slug' => Str::slug($title) . '-' . $idx,
                'description' => 'Produit agricole de haute qualité',
                'price' => $mode === 'sell' ? (rand(100, 5000) / 10) : null,
                'currency' => 'MAD',
                'price_negotiable' => false,
                'contact_phone' => '+212' . rand(600000000, 699999999),
                'handover_method' => 'both',
                'status' => $status,
                'condition' => 'fresh',
                'quantity' => rand(1, 100),
                'quantity_unit' => 'kg',
                'super_category_id' => $superCat->id,
                'views_count' => rand(10, 1000),
                'favorites_count' => rand(0, 50),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            
            // Attach subcategory
            $product->categories()->attach($subCat->id);
            
            // Create media
            Media::create([
                'mediable_id' => $product->id,
                'mediable_type' => Product::class,
                'disk' => 'public',
                'path' => 'external/' . Str::slug($title) . '.jpg',
                'url' => 'https://picsum.photos/seed/' . Str::slug($title) . '/600/450.jpg',
                'file_name' => 'thumb.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 0,
                'collection' => 'thumbnail',
                'sort_order' => 0,
                'is_temporary' => false,
            ]);
            
            $products->push($product);
        }

        // Create conversations, messages, favorites, reviews
        $usedConvPairs = [];
        $usedFavPairs = [];
        $usedReviewPairs = [];
        $reviewComments = [
            'Excellent produit, je recommande !',
            'Très bonne qualité, livraison rapide',
            'Produit conforme à la description',
            'Superbe qualité prix',
        ];
        
        foreach ($products as $product) {
            // Conversations
            $numConv = rand(1, 5);
            $selectedBuyers = $otherUsers->random($numConv);
            foreach ($selectedBuyers as $buyer) {
                $key = "{$product->id}-{$buyer->id}-{$testUser->id}";
                if (!in_array($key, $usedConvPairs)) {
                    $usedConvPairs[] = $key;
                    $conv = Conversation::create([
                        'product_id' => $product->id,
                        'buyer_id' => $buyer->id,
                        'seller_id' => $testUser->id,
                        'created_at' => $product->created_at->addDays(rand(0, 5)),
                    ]);
                    Message::create([
                        'conversation_id' => $conv->id,
                        'sender_id' => $buyer->id,
                        'content' => 'Bonjour, ce produit est disponible ?',
                        'created_at' => $conv->created_at,
                    ]);
                    Message::create([
                        'conversation_id' => $conv->id,
                        'sender_id' => $testUser->id,
                        'content' => 'Oui bien sûr !',
                        'created_at' => $conv->created_at->addMinutes(rand(5, 30)),
                    ]);
                }
            }
            
            // Favorites
            $numFav = rand(1, 5);
            $favUsers = $otherUsers->random($numFav);
            foreach ($favUsers as $user) {
                $key = "{$user->id}-{$product->id}";
                if (!in_array($key, $usedFavPairs)) {
                    $usedFavPairs[] = $key;
                    Favorite::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'created_at' => $product->created_at->addDays(rand(0, 5)),
                    ]);
                }
            }
            
            // Reviews
            $numReview = rand(1, 3);
            $reviewUsers = $otherUsers->random($numReview);
            foreach ($reviewUsers as $user) {
                $key = "{$user->id}-{$product->id}";
                if (!in_array($key, $usedReviewPairs)) {
                    $usedReviewPairs[] = $key;
                    Review::create([
                        'product_id' => $product->id,
                        'reviewer_id' => $user->id,
                        'reviewed_id' => $testUser->id,
                        'rating' => rand(4, 5),
                        'comment' => $reviewComments[array_rand($reviewComments)],
                        'created_at' => $product->created_at->addDays(rand(1, 10)),
                    ]);
                }
            }
        }
        
        // Also add a few products where test user is the buyer
        $otherProducts = Product::where('user_id', '!=', $testUser->id)->take(3)->get();
        foreach ($otherProducts as $prod) {
            // Add conversation as buyer
            Conversation::firstOrCreate(
                ['product_id' => $prod->id, 'buyer_id' => $testUser->id, 'seller_id' => $prod->user_id]
            );
        }
        
        $this->command->info('Test user created/updated successfully!');
        $this->command->info("Email: {$testUser->email}");
        $this->command->info('Password: password');
        $this->command->info("Products created: {$products->count()}");
    }
    
    private function createCategories(): void
    {
        if (Category::whereNull('parent_id')->count() === 0) {
            $superCategories = [
                ['name' => 'Crops', 'slug' => 'crops', 'icon' => 'leaf', 'subcategories' => ['Cereals', 'Fruits', 'Vegetables', 'Legumes']],
                ['name' => 'Livestock', 'slug' => 'livestock', 'icon' => 'cow', 'subcategories' => ['Cattle', 'Poultry', 'Sheep & Goats']],
                ['name' => 'Seeds', 'slug' => 'seeds', 'icon' => 'sprout', 'subcategories' => ['Crop Seeds', 'Vegetable Seeds']],
                ['name' => 'Equipment', 'slug' => 'equipment', 'icon' => 'tractor', 'subcategories' => ['Tractors', 'Harvesters', 'Tools']],
                ['name' => 'Fertilizers', 'slug' => 'fertilizers', 'icon' => 'flask', 'subcategories' => ['Organic', 'Chemical']],
            ];
            
            foreach ($superCategories as $superIdx => $superCat) {
                $parent = Category::create([
                    'name' => $superCat['name'],
                    'slug' => $superCat['slug'],
                    'icon' => $superCat['icon'],
                    'is_active' => true,
                    'sort_order' => $superIdx + 1,
                    'parent_id' => null,
                ]);
                
                foreach ($superCat['subcategories'] as $subIdx => $subCatName) {
                    Category::create([
                        'name' => $subCatName,
                        'slug' => Str::slug($subCatName),
                        'is_active' => true,
                        'sort_order' => $subIdx + 1,
                        'parent_id' => $parent->id,
                    ]);
                }
            }
        }
    }
}
