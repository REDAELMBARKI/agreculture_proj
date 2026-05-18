<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Media;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing banners and their media
        Banner::all()->each(function ($banner) {
            $banner->thumbnail()->delete();
            $banner->delete();
        });

        $banners = [
            [
                'type' => 'split',
                'title' => 'How AgriMarket Works',
                'subtitle' => 'Connecting farmers and buyers for a sustainable agricultural future.',
                'image_url' => 'https://images.unsplash.com/photo-1513159419869-623ae1b1a620?auto=format&fit=crop&q=80&w=800',
                'badge_text' => 'Agri Economy',
                'cta_text' => 'Learn more about our mission',
                'cta_link' => '/about',
                'steps' => [
                    [
                        'num' => '01',
                        'title' => 'List Your Products',
                        'description' => 'Easily list your crops, livestock, or equipment for sale or trade.'
                    ],
                    [
                        'num' => '02',
                        'title' => 'Connect Directly',
                        'description' => 'Buyers and sellers communicate directly to negotiate terms and delivery.'
                    ],
                    [
                        'num' => '03',
                        'title' => 'Grow Together',
                        'description' => 'Strengthen local food chains and get fair prices for your hard work.'
                    ]
                ],
                'sort_order' => 1,
            ],
            [
                'type' => 'simple',
                'title' => 'Farm to Table',
                'subtitle' => 'Get fresh, local produce directly from the source.',
                'image_url' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&q=80&w=1200',
                'badge_text' => 'Fresh',
                'cta_text' => 'Shop Produce',
                'cta_link' => '/category/crops',
                'sort_order' => 2,
            ],
            [
                'type' => 'simple',
                'title' => 'List Your Harvest',
                'subtitle' => 'Reach more buyers and grow your farming business with our digital marketplace.',
                'image_url' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&q=80&w=1200',
                'badge_text' => 'Sell',
                'cta_text' => 'Add Announcement',
                'cta_link' => '/add_announcement',
                'sort_order' => 3,
            ],
            [
                'type' => 'simple',
                'title' => 'Quality Seeds & Tools',
                'subtitle' => 'Access the best inputs to ensure a successful growing season.',
                'image_url' => 'https://images.unsplash.com/photo-1596464716127-f2a82984de30?auto=format&fit=crop&q=80&w=1200',
                'badge_text' => 'Inputs',
                'cta_text' => 'Browse Equipment',
                'cta_link' => '/category/equipment',
                'sort_order' => 4,
            ]
        ];

        foreach ($banners as $bannerData) {
            $imageUrl = $bannerData['image_url'];
            unset($bannerData['image_url']);
            
            $banner = Banner::create($bannerData);
            
            if ($imageUrl) {
                $banner->thumbnail()->create([
                    'url' => $imageUrl,
                    'path' => $imageUrl,
                    'collection' => 'thumbnail',
                    'disk' => 'public',
                    'file_name' => basename($imageUrl),
                    'mime_type' => 'image/jpeg',
                    'size' => 0,
                ]);
            }
        }
    }
}
