<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\Media;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeds products, media, and conversations for the primary demo user so
 * /user_dashboard (Marketplace Stats) returns non-zero, realistic data.
 */
class UserImpactDemoSeeder extends Seeder
{
    public const DEMO_EMAIL = 'soufiane@gmail.com';

    public function run(): void
    {
        $seller = User::query()->where('email', self::DEMO_EMAIL)->first();
        if (! $seller) {
            $this->command?->warn('User '.self::DEMO_EMAIL.' not found. Skipping UserImpactDemoSeeder (run DatabaseSeeder first).');

            return;
        }

        DB::transaction(function () use ($seller) {
            $this->removePriorDemoProducts($seller->id);

            $catClothes = Category::query()->firstOrCreate(
                ['slug' => 'impact-demo-clothes'],
                [
                    'name' => 'Kids Clothes & wear',
                    'name_ar' => null,
                    'name_fr' => null,
                    'icon' => 'shirt',
                    'sort_order' => 900,
                    'is_active' => true,
                    'parent_id' => null,
                ]
            );

            $catShoes = Category::query()->firstOrCreate(
                ['slug' => 'impact-demo-shoes'],
                [
                    'name' => 'School shoes & sneakers',
                    'name_ar' => null,
                    'name_fr' => null,
                    'icon' => 'footprints',
                    'sort_order' => 901,
                    'is_active' => true,
                    'parent_id' => null,
                ]
            );

            $catAcc = Category::query()->firstOrCreate(
                ['slug' => 'impact-demo-accessories'],
                [
                    'name' => 'Bags & kid accessories',
                    'name_ar' => null,
                    'name_fr' => null,
                    'icon' => 'palette',
                    'sort_order' => 902,
                    'is_active' => true,
                    'parent_id' => null,
                ]
            );

            $catGeneral = Category::query()->firstOrCreate(
                ['slug' => 'impact-demo-general'],
                [
                    'name' => 'Household, electronics & more',
                    'name_ar' => null,
                    'name_fr' => null,
                    'icon' => 'box',
                    'sort_order' => 903,
                    'is_active' => true,
                    'parent_id' => null,
                ]
            );

            $buyers = $this->ensureImpactBuyers();

            $now = Carbon::now();

            $rows = [
                [
                    'title' => 'Marketplace Demo — Winter coat bundle',
                    'status' => 'published',
                    'super' => $catClothes,
                    'views' => 210,
                    'days_ago' => 4,
                    'price' => 250,
                ],
                [
                    'title' => 'Marketplace Demo — Leather school shoes',
                    'status' => 'sold',
                    'super' => $catShoes,
                    'views' => 85,
                    'days_ago' => 12,
                    'price' => 150,
                ],
                [
                    'title' => 'Marketplace Demo — Kids backpack',
                    'status' => 'published',
                    'super' => $catAcc,
                    'views' => 142,
                    'days_ago' => 1,
                    'price' => 100,
                ],
                [
                    'title' => 'Marketplace Demo — Educational toys',
                    'status' => 'published',
                    'super' => $catGeneral,
                    'views' => 315,
                    'days_ago' => 8,
                    'price' => 300,
                ],
            ];

            foreach ($rows as $row) {
                $created = $now->copy()->subDays($row['days_ago']);
                $p = Product::create([
                    'user_id' => $seller->id,
                    'super_category_id' => $row['super']->id,
                    'title' => $row['title'],
                    'slug' => Str::slug($row['title']).'-'.Str::random(4),
                    'description' => 'Demo item for marketplace dashboard testing.',
                    'status' => $row['status'],
                    'price' => $row['price'],
                    'currency' => 'MAD',
                    'contact_phone' => '0600000000',
                    'created_at' => $created,
                    'updated_at' => $created,
                    'views_count' => $row['views'],
                ]);

                // Fake some conversations
                $numChats = rand(2, 5);
                for ($i = 0; $i < $numChats; $i++) {
                    Conversation::create([
                        'product_id' => $p->id,
                        'buyer_id' => $buyers->random()->id,
                        'seller_id' => $seller->id,
                        'last_message_at' => $created->copy()->addHours(rand(1, 24)),
                    ]);
                }
            }
        });

        $this->command?->info('UserImpactDemoSeeder: Marketplace data ready for '.self::DEMO_EMAIL);
    }

    private function removePriorDemoProducts(int $userId): void
    {
        $query = Product::query()->where('user_id', $userId)->where('title', 'like', '%Demo —%');
        $ids = $query->pluck('id');
        if ($ids->isEmpty()) {
            return;
        }

        Media::query()
            ->where('mediable_type', Product::class)
            ->whereIn('mediable_id', $ids)
            ->delete();

        Product::query()->whereIn('id', $ids)->forceDelete();
    }

    /**
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function ensureImpactBuyers()
    {
        $emails = [
            'demo-buyer-1@test.com',
            'demo-buyer-2@test.com',
            'demo-buyer-3@test.com',
        ];

        $out = collect();
        foreach ($emails as $email) {
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Demo Buyer',
                    'password' => Hash::make('password'),
                ]
            );

            $user->roles()->syncWithoutDetaching([10]);

            $out->push($user);
        }

        return $out;
    }
}
