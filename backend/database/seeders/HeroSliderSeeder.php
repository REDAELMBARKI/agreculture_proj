<?php

namespace Database\Seeders;

use App\Models\HeroSlider;
use App\Models\Media;
use Illuminate\Database\Seeder;

class HeroSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing hero sliders and their media
        HeroSlider::all()->each(function ($slider) {
            $slider->thumbnail()->delete();
            $slider->delete();
        });

        $slides = [
            [
                'image_url' => 'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?auto=format&fit=crop&q=80&w=2000',
                'headline' => 'Direct From the Farm to You',
                'subline' => "Fresh crops, healthy livestock, and quality agricultural equipment. Buy, sell, or trade today.",
                'cta1_text' => 'Browse Marketplace',
                'cta1_link' => '/marketplace',
                'cta2_text' => 'Sell Your Harvest',
                'cta2_link' => '/add_announcement',
                'sort_order' => 1,
            ],
            [
                'image_url' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&q=80&w=2000',
                'headline' => 'Empowering Local Agriculture',
                'subline' => 'Connecting farmers with buyers to build a sustainable food future.',
                'cta1_text' => 'Our Vision',
                'cta1_link' => '/about',
                'cta2_text' => 'Join Now',
                'cta2_link' => '/sign_up',
                'sort_order' => 2,
            ],
            [
                'image_url' => 'https://images.unsplash.com/photo-1563513330620-64295f748493?auto=format&fit=crop&q=80&w=2000',
                'headline' => 'Modern Irrigation Solutions',
                'subline' => 'Maximize your yield with the latest in water management technology.',
                'cta1_text' => 'Shop Irrigation',
                'cta1_link' => '/category/irrigation',
                'cta2_text' => 'View Equipment',
                'cta2_link' => '/category/equipment',
                'sort_order' => 3,
            ]
        ];

        foreach ($slides as $slideData) {
            $imageUrl = $slideData['image_url'];
            unset($slideData['image_url']);
            
            $slider = HeroSlider::create($slideData);
            
            $slider->thumbnail()->create([
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
