<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use App\Models\Review;
use App\Models\Address;
use App\Models\Favorite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('PRAGMA foreign_keys=OFF');

        // Clear existing data
        $this->clearExistingData();

        // Create data in proper order
        $this->createUsers();
        $this->createCategories();
        $this->createProducts();
        $this->createReviews();
        $this->createFavorites();

        // Verify no empty sections
        // $this->verifyDataIntegrity();

        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys=ON');

        $this->command->info('Announcement data seeded successfully!');
    }

    private function clearExistingData(): void
    {
        DB::table('favorites')->delete();
        DB::table('reviews')->delete();
        DB::table('addresses')->delete();
        DB::table('media')->delete();
        DB::table('product_tag')->delete();
        DB::table('subcategory_product')->delete();
        DB::table('products')->delete();
        DB::table('tags')->delete();
        DB::table('categories')->delete();
        DB::table('users')->delete();
    }

    private function createUsers(): void
    {
        // Create 5 specific Moroccan users
        $users = [
            ['name' => 'Fatima Alami', 'slug' => 'fatima-alami' ,  'email' => 'fatima@example.com', 'rating' => 4.8, 'role_id' => 2],
            ['name' => 'Youssef Benkiran' , 'slug' => 'youssef-benkiran' , 'email' => 'youssef@example.com', 'rating' => 4.5, 'role_id' => 2],
            ['name' => 'Amina Rachidi', 'slug' => 'amina-rachidi' ,  'email' => 'amina@example.com', 'rating' => 4.9, 'role_id' => 2],
            ['name' => 'Karim El Mardi', 'slug' => 'karim-el-mardi' ,  'email' => 'karim@example.com', 'rating' => 4.7, 'role_id' => 2],
            ['name' => 'Sofia Mansouri', 'slug' => 'sofia-mansouri' ,  'email' => 'sofia@example.com', 'rating' => 4.6, 'role_id' => 2],
        ];

        foreach ($users as $userData) {
            User::factory()->create([
                'name' => $userData['name'],
                'slug' => $userData['slug'],
                'email' => $userData['email'],
                'rating' => $userData['rating'],
                'role_id' => $userData['role_id'],
            ]);
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

    private function createProducts(): void
    {
        $categories = Category::whereNotNull('parent_id')->get();
        $users = User::whereIn("id" , [1,6])->get();

        // Realistic Agriculture product names
        $productNames = [
            'Blé Tendre de Qualité', 'Tomates Cerises Bio', 'Bétail Bovin Sélectionné',
            'Tracteur Massey Ferguson', 'Semences de Maïs Hybride', 'Engrais NPK 15-15-15',
            'Système d\'Irrigation Goutte à Goutte', 'Terrain Agricole 5 Hectares',
            'Moutons de Race Sardi', 'Poulets de Chair Fermiers', 'Miel Pur de l\'Atlas',
            'Pommes de Terre de Saison', 'Huile d\'Olive Vierge', 'Matériel de Récolte',
            'Conseils en Agronomie', 'Main d\'œuvre Saisonnière', 'Pesticides Bio',
            'Oignons Rouges de Meknès', 'Citrons Frais du Souss', 'Fourrage pour Bétail'
        ];

        // Create 20 products distributed across categories
        foreach ($productNames as $index => $productName) {
            $category = $categories[$index % $categories->count()];
            $user = $users[$index % $users->count()];
            $parentCategory = Category::find($category->parent_id);

            $mode = fake()->randomElement(['sell', 'donate']);
            $product = Product::factory()->create([
                'title' => $productName,
                'slug' => Str::slug($productName),
                'description' => 'Produit agricole de qualité supérieure au Maroc. ' . fake()->sentence(),
                'price' => fake()->randomFloat(2, 100, 5000),
                'listing_mode' => $mode,
                'status' => 'published',
                'user_id' => $user->id,
                'super_category_id' => $parentCategory->id,
                'views_count' => fake()->numberBetween(10, 1000),
                'favorites_count' => fake()->numberBetween(0, 50),
                'condition' => fake()->randomElement(['Excellent', 'Bon état', 'Standard']),
                'contact_phone' => '06' . fake()->numerify('########'),
            ]);

            // Link to category (sub-category)
            $product->categories()->attach($category->id);

            // Create Moroccan address
            $randomCity = \App\Models\City::inRandomOrder()->first();
            $product->address()->create([
                'city_id' => $randomCity->id,
                'district' => fake()->word(),
                'address_line' => fake()->streetAddress(),
            ]);

            // Create thumbnail
            Media::factory()->create([
                'mediable_id' => $product->id,
                'mediable_type' => Product::class,
                'collection' => 'thumbnail',
                'url' => 'https://picsum.photos/seed/' . Str::slug($productName) . '/400/300.jpg',
            ]);

             Media::factory()->create([
                'mediable_id' => $product->id,
                'mediable_type' => Product::class,
                'collection' => 'gallery',
                'url' => 'https://picsum.photos/seed/' . Str::slug($productName . "1") . '/400/300.jpg',
            ]);

             Media::factory()->create([
                'mediable_id' => $product->id,
                'mediable_type' => Product::class,
                'collection' => 'gallery',
                'url' => 'https://picsum.photos/seed/' . Str::slug($productName . "2")  . '/400/300.jpg',
            ]);

        }
    }

    private function createReviews(): void
    {
        $products = Product::all();
        $users = User::all();

        $reviewComments = [
            'Excellent produit, mon enfant adore!', 'Très bonne qualité, je recommande',
            'Produit conforme à la description', 'Superbe, livraison rapide',
            'Qualité professionnelle', 'Parfait pour les enfants', 'Très satisfait',
            'Bon rapport qualité/prix', 'Produit artisanal magnifique'
        ];

        $usedPairs = [];
        
        foreach ($products as $product) {
            // Create 3-6 reviews per product
            $reviewCount = fake()->numberBetween(3, 6);
            for ($i = 0; $i < $reviewCount; $i++) {
                $user = $users->random();
                $pairKey = $user->id . '-' . $product->id;
                
                // Ensure unique reviewer-product pair
                if (!in_array($pairKey, $usedPairs)) {
                    $usedPairs[] = $pairKey;
                    Review::factory()->create([
                        'product_id' => $product->id,
                        'reviewer_id' => $user->id,
                        'reviewed_id' => $product->user_id,
                        'rating' => fake()->numberBetween(4, 5), // Mostly positive reviews
                        'comment' => $reviewComments[array_rand($reviewComments)],
                    ]);
                }
            }
        }
    }

    private function createFavorites(): void
    {
        $products = Product::all();
        $users = User::all();
        $usedPairs = [];

        foreach ($products as $product) {
            // Create 1-5 favorites per product
            $favoriteCount = rand(1, 5);
            
            for ($i = 0; $i < $favoriteCount; $i++) {
                $user = $users->random();
                $pairKey = $user->id . '-' . $product->id;
                
                // Ensure unique user-product pair
                if (!in_array($pairKey, $usedPairs)) {
                    $usedPairs[] = $pairKey;
                    Favorite::factory()->create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                    ]);
                }
            }
        }
    }

    private function verifyDataIntegrity(): void
    {
        // Verify minimum data requirements
        if (User::count() < 5) {
            throw new Exception('Insufficient users seeded');
        }
        if (Category::count() < 8) {
            throw new Exception('Insufficient categories seeded');
        }
        if (Product::count() < 20) {
            throw new Exception('Insufficient products seeded');
        }
        if (Review::count() < 60) {
            throw new Exception('Insufficient reviews seeded');
        }
        
        // Verify each category has products
        $categories = Category::all();
        foreach ($categories as $category) {
            if ($category->products()->count() < 2) {
                throw new Exception("Category {$category->name} has insufficient products");
            }
        }
    }
}
