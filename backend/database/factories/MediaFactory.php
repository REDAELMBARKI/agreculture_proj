<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Fake image URLs from placeholder services
        $imageUrls = [
            'https://picsum.photos/seed/agriculture1/400/300.jpg',
            'https://picsum.photos/seed/agriculture2/400/300.jpg',
            'https://picsum.photos/seed/agriculture3/400/300.jpg',
            'https://picsum.photos/seed/crops1/400/300.jpg',
            'https://picsum.photos/seed/crops2/400/300.jpg',
            'https://picsum.photos/seed/livestock1/400/300.jpg',
            'https://picsum.photos/seed/livestock2/400/300.jpg',
            'https://picsum.photos/seed/tractor1/400/300.jpg',
            'https://picsum.photos/seed/farm1/400/300.jpg',
            'https://picsum.photos/seed/seeds1/400/300.jpg',
        ];

        $collections = ['thumbnail', 'gallery'];
        $collection = fake()->randomElement($collections);
        
        return [
            'mediable_id' => null, // Will be set when creating media for specific model
            'mediable_type' => null, // Will be set when creating media for specific model
            'disk' => 'public',
            'path' => 'images/' . fake()->uuid() . '.jpg',
            'url' => fake()->randomElement($imageUrls),
            'file_name' => fake()->uuid() . '.jpg',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(50000, 500000), // 50KB - 500KB
            'collection' => $collection,
            'sort_order' => $collection === 'thumbnail' ? 0 : fake()->numberBetween(1, 10),
        ];
    }

    /**
     * Create media for a product
     */
    public function forProduct($productId): static
    {
        return $this->state(fn (array $attributes) => [
            'mediable_id' => $productId,
            'mediable_type' => 'App\\Models\\Product',
        ]);
    }

    /**
     * Create media for a user (avatar)
     */
    public function forUser($userId): static
    {
        return $this->state(fn (array $attributes) => [
            'mediable_id' => $userId,
            'mediable_type' => 'App\\Models\\User',
            'collection' => 'avatar',
        ]);
    }

    /**
     * Create a thumbnail
     */
    public function thumbnail(): static
    {
        return $this->state(fn (array $attributes) => [
            'collection' => 'thumbnail',
            'sort_order' => 0,
        ]);
    }

    /**
     * Create gallery images
     */
    public function gallery(): static
    {
        return $this->state(fn (array $attributes) => [
            'collection' => 'gallery',
            'sort_order' => fake()->numberBetween(1, 10),
        ]);
    }
}
