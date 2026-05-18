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
use App\Models\Role;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run initial seeders
        $this->call([
            RoleSeeder::class,
            HeroSliderSeeder::class,
            BannerSeeder::class,
            FilterAttributeSeeder::class,
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
        
        // Create test user with lots of data
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'slug' => 'test-user',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // User role
        ]);
        
        // Create a few other Moroccan users
        $users = [
            ['name' => 'Fatima Alami', 'slug' => 'fatima-alami' ,  'email' => 'fatima@example.com', 'rating' => 4.8, 'role_id' => 2],
            ['name' => 'Youssef Benkiran' , 'slug' => 'youssef-benkiran' , 'email' => 'youssef@example.com', 'rating' => 4.5, 'role_id' => 2],
            ['name' => 'Amina Rachidi', 'slug' => 'amina-rachidi' ,  'email' => 'amina@example.com', 'rating' => 4.9, 'role_id' => 2],
            ['name' => 'Karim El Mardi', 'slug' => 'karim-el-mardi' ,  'email' => 'karim@example.com', 'rating' => 4.7, 'role_id' => 2],
            ['name' => 'Sofia Mansouri', 'slug' => 'sofia-mansouri' ,  'email' => 'sofia@example.com', 'rating' => 4.6, 'role_id' => 2],
        ];

        $otherUsers = collect();
        foreach ($users as $userData) {
            $otherUsers->push(User::factory()->create([
                'name' => $userData['name'],
                'slug' => $userData['slug'],
                'email' => $userData['email'],
                'rating' => $userData['rating'],
                'role_id' => $userData['role_id'],
            ]));
        }
        
        // Create categories
        $this->createCategories();

        $superCategories = Category::query()->whereNull('parent_id')->get();
        $subCategories = Category::query()->whereNotNull('parent_id')->get();
        
        if ($superCategories->isNotEmpty() && $subCategories->isNotEmpty()) {
            // Create lots of product titles in French for agriculture
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
                'Miel pur de l’Atlas',
                'Huile d’olive vierge extra',
                'Semences de maïs hybride',
                'Foin de haute qualité',
                'Pesticides bio pour légumes',
                'Tracteur Massey Ferguson 399',
                'Remorque agricole',
                'Bétail bovin sélectionné',
                'Citrons frais du Souss',
                'Fourrage pour bétail',
                'Ail de qualité supérieure',
                'Carottes bio',
                'Courgettes fraîches',
                'Poivrons rouges',
                'Aubergines',
                'Pastèques sucrées',
                'Melons jaunes',
                'Riz long grain',
                'Orge pour animaux',
                'Sorgho',
                'Tournesols pour huile',
                'Coton',
                'Lin',
                'Chanvre industriel',
                'Chevaux de race',
                'Chèvres laitières',
                'Dindes',
                'Canards',
                'Oies',
                'Pigeons',
                'Lapins',
                'Alpaga',
                'Laine de mouton',
                'Cuir de bovin',
                'Engrais organique',
                'Compost',
                'Paillis',
                'Plantes aromatiques',
                'Menthe fraîche',
                'Basilic',
                'Persil',
                'Coriandre',
                'Romarin',
                'Thym',
                'Laurier',
                'Safran',
                'Gingembre',
                'Curcuma',
                'Poivre noir',
                'Piment rouge',
                'Paprika',
                'Cumin',
                'Cannelle',
                'Clous de girofle',
                'Noix de muscade',
                'Vanille',
                'Café',
                'Thé',
                'Cacao',
            ];

            $products = [];

            foreach ($demoTitles as $idx => $title) {
                $superCategory = $superCategories->random();
                $possibleSubCategories = $subCategories->where('parent_id', $superCategory->id);
                $subCategory = $possibleSubCategories->isNotEmpty() ? $possibleSubCategories->random() : $subCategories->random();
                $mode = $idx % 2 === 0 ? 'sell' : 'donate';
                $statuses = ['published', 'published', 'published', 'reserved', 'sold', 'closed', 'donated'];
                $status = $statuses[array_rand($statuses)];
                
                // Random date in last 30 days for activity chart
                $createdAt = Carbon::today()->subDays(rand(0, 29))->addHours(rand(0, 23));
                
                $product = Product::factory()->create([
                    'user_id' => $testUser->id,
                    'listing_mode' => $mode,
                    'status' => $status,
                    'title' => $title,
                    'slug' => Str::slug($title).'-'.$idx,
                    'super_category_id' => $superCategory->id,
                    'views_count' => rand(10, 1500),
                    'favorites_count' => rand(0, 100),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                
                $product->categories()->attach($subCategory->id);
                
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
                
                $products[] = $product;
            }
            
            // Add conversations (clicks)
            $usedConversationPairs = [];
            foreach ($products as $product) {
                $conversationCount = rand(1, min(10, $otherUsers->count()));
                $selectedBuyers = $otherUsers->random($conversationCount);
                foreach ($selectedBuyers as $buyer) {
                    $pairKey = $product->id . '-' . $buyer->id . '-' . $testUser->id;
                    if (!in_array($pairKey, $usedConversationPairs)) {
                        $usedConversationPairs[] = $pairKey;
                        $conv = Conversation::create([
                            'product_id' => $product->id,
                            'buyer_id' => $buyer->id,
                            'seller_id' => $testUser->id,
                            'created_at' => $product->created_at->copy()->addDays(rand(0, 7)),
                        ]);
                        
                        // Add a few messages
                        Message::create([
                            'conversation_id' => $conv->id,
                            'sender_id' => $buyer->id,
                            'content' => 'Bonjour, est-ce que ce produit est toujours disponible ?',
                            'created_at' => $conv->created_at,
                        ]);
                        Message::create([
                            'conversation_id' => $conv->id,
                            'sender_id' => $testUser->id,
                            'content' => 'Oui bien sûr, il est toujours disponible !',
                            'created_at' => $conv->created_at->copy()->addMinutes(rand(5, 30)),
                        ]);
                    }
                }
            }
            
            // Add favorites from other users
            $usedFavoritePairs = [];
            foreach ($products as $product) {
                $favoriteCount = rand(1, min(15, $otherUsers->count()));
                $favoritedUsers = $otherUsers->random($favoriteCount);
                foreach ($favoritedUsers as $user) {
                    $pairKey = $user->id . '-' . $product->id;
                    if (!in_array($pairKey, $usedFavoritePairs)) {
                        $usedFavoritePairs[] = $pairKey;
                        Favorite::create([
                            'user_id' => $user->id,
                            'product_id' => $product->id,
                            'created_at' => $product->created_at->copy()->addDays(rand(0, 10)),
                        ]);
                    }
                }
            }
            
            // Add reviews from other users
            $reviewComments = [
                'Excellent produit, je recommande !',
                'Très bonne qualité, livraison rapide',
                'Produit conforme à la description',
                'Superbe qualité prix',
                'Très satisfait de mon achat',
                'Produit artisanal de qualité',
                'Merci pour ce produit exceptionnel',
                'Je repasserai commande !',
            ];
            $usedReviewPairs = [];
            foreach ($products as $product) {
                $reviewCount = rand(2, min(8, $otherUsers->count()));
                $selectedReviewers = $otherUsers->random($reviewCount);
                foreach ($selectedReviewers as $reviewer) {
                    $pairKey = $reviewer->id . '-' . $product->id;
                    if (!in_array($pairKey, $usedReviewPairs)) {
                        $usedReviewPairs[] = $pairKey;
                        Review::create([
                            'product_id' => $product->id,
                            'reviewer_id' => $reviewer->id,
                            'reviewed_id' => $testUser->id,
                            'rating' => rand(4, 5),
                            'comment' => $reviewComments[array_rand($reviewComments)],
                            'created_at' => $product->created_at->copy()->addDays(rand(1, 14)),
                        ]);
                    }
                }
            }
        }
    }
    
    private function createCategories(): void
    {
        // Create top-level agriculture super categories with their sub-categories
        $superCategories = [
            [
                'name' => 'Crops', 'slug' => 'crops', 'icon' => 'leaf',
                'image' => 'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?auto=format&fit=crop&q=80&w=800',
                'subcategories' => [
                    ['name' => 'Cereals', 'slug' => 'cereals'],
                    ['name' => 'Fruits', 'slug' => 'fruits'],
                    ['name' => 'Vegetables', 'slug' => 'vegetables'],
                    ['name' => 'Legumes', 'slug' => 'legumes'],
                ]
            ],
            [
                'name' => 'Livestock', 'slug' => 'livestock', 'icon' => 'cow',
                'image' => 'https://images.unsplash.com/photo-1547496502-affa22d38842?auto=format&fit=crop&q=80&w=800',
                'subcategories' => [
                    ['name' => 'Cattle', 'slug' => 'cattle'],
                    ['name' => 'Poultry', 'slug' => 'poultry'],
                    ['name' => 'Sheep & Goats', 'slug' => 'sheep-goats'],
                    ['name' => 'Honeybees', 'slug' => 'honeybees'],
                ]
            ],
            [
                'name' => 'Seeds', 'slug' => 'seeds', 'icon' => 'sprout',
                'image' => 'https://images.unsplash.com/photo-1530507629858-e4977d30e9e0?auto=format&fit=crop&q=80&w=800',
                'subcategories' => [
                    ['name' => 'Crop Seeds', 'slug' => 'crop-seeds'],
                    ['name' => 'Vegetable Seeds', 'slug' => 'vegetable-seeds'],
                    ['name' => 'Fruit Seeds', 'slug' => 'fruit-seeds'],
                ]
            ],
            [
                'name' => 'Equipment', 'slug' => 'equipment', 'icon' => 'tractor',
                'image' => 'https://images.unsplash.com/photo-1530268576344-966953713f01?auto=format&fit=crop&q=80&w=800',
                'subcategories' => [
                    ['name' => 'Tractors', 'slug' => 'tractors'],
                    ['name' => 'Harvesters', 'slug' => 'harvesters'],
                    ['name' => 'Plows', 'slug' => 'plows'],
                    ['name' => 'Tools', 'slug' => 'tools'],
                ]
            ],
            [
                'name' => 'Land', 'slug' => 'land', 'icon' => 'map',
                'image' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&q=80&w=800',
                'subcategories' => [
                    ['name' => 'Farm Land', 'slug' => 'farm-land'],
                    ['name' => 'Orchards', 'slug' => 'orchards'],
                    ['name' => 'Grazing Land', 'slug' => 'grazing-land'],
                ]
            ],
            [
                'name' => 'Services', 'slug' => 'services', 'icon' => 'handshake',
                'image' => 'https://images.unsplash.com/photo-1589923188900-85dae523342b?auto=format&fit=crop&q=80&w=800',
                'subcategories' => [
                    ['name' => 'Consulting', 'slug' => 'consulting'],
                    ['name' => 'Labor', 'slug' => 'labor'],
                    ['name' => 'Transportation', 'slug' => 'transportation'],
                ]
            ],
            [
                'name' => 'Fertilizers', 'slug' => 'fertilizers', 'icon' => 'flask',
                'image' => 'https://images.unsplash.com/photo-1628352081506-83c43123ed6d?auto=format&fit=crop&q=80&w=800',
                'subcategories' => [
                    ['name' => 'Organic Fertilizers', 'slug' => 'organic-fertilizers'],
                    ['name' => 'Chemical Fertilizers', 'slug' => 'chemical-fertilizers'],
                    ['name' => 'Pesticides', 'slug' => 'pesticides'],
                ]
            ],
            [
                'name' => 'Irrigation', 'slug' => 'irrigation', 'icon' => 'water',
                'image' => 'https://images.unsplash.com/photo-1563513330620-64295f748493?auto=format&fit=crop&q=80&w=800',
                'subcategories' => [
                    ['name' => 'Drip Systems', 'slug' => 'drip-systems'],
                    ['name' => 'Sprinklers', 'slug' => 'sprinklers'],
                    ['name' => 'Pumps', 'slug' => 'pumps'],
                ]
            ],
            [
                'name' => 'Organic', 'slug' => 'organic', 'icon' => 'check-circle',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80&w=800',
                'subcategories' => [
                    ['name' => 'Organic Produce', 'slug' => 'organic-produce'],
                    ['name' => 'Eco-friendly Supplies', 'slug' => 'eco-supplies'],
                ]
            ],
        ];

        foreach ($superCategories as $index => $superCategory) {
            $parent = Category::factory()->create([
                'name' => $superCategory['name'],
                'slug' => $superCategory['slug'],
                'icon' => $superCategory['icon'],
                'is_active' => true,
                'sort_order' => $index + 1,
                'parent_id' => null,
            ]);

            // Create category image
            Media::factory()->create([
                'mediable_id' => $parent->id,
                'mediable_type' => Category::class,
                'collection' => 'thumbnail',
                'url' => $superCategory['image'],
            ]);

            // Create sub-categories for this super category
            foreach ($superCategory['subcategories'] as $subIndex => $subCategory) {
                Category::factory()->create([
                    'name' => $subCategory['name'],
                    'slug' => $subCategory['slug'],
                    'icon' => null,
                    'is_active' => true,
                    'sort_order' => $subIndex + 1,
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
