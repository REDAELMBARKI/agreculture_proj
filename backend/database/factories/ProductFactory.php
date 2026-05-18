<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $listingModes = ['sell', 'donate'];
        $listingTypes = ['single', 'collection'];
        $statuses = ['published', 'draft', 'reserved', 'sold', 'donated', 'closed'];
        $conditions = ['fresh', 'dried', 'processed', 'standard'];
        $brands = ['John Deere', 'Massey Ferguson', 'New Holland', 'Kubota', 'Claas', 'Fendt', 'Case IH'];
        $seasons = ['spring', 'summer', 'autumn', 'winter', 'year-round'];
        $handoverMethods = ['pickup', 'delivery', 'both'];
        $quantityUnits = ['kg', 'ton', 'hectare', 'litre', 'unit'];

        $listingMode = fake()->randomElement($listingModes);
        $price = $listingMode === 'sell' ? fake()->randomFloat(2, 50, 5000) : null;

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'listing_mode' => $listingMode,
            'listing_type' => fake()->randomElement($listingTypes),
            'title' => fake()->words(3, true) . ' - ' . fake()->randomElement(['Crops', 'Livestock', 'Equipment', 'Harvest']),
            'description' => fake()->sentences(3, true),
            'price' => $price,
            'currency' => 'MAD',
            'price_negotiable' => fake()->boolean(30),
            'handover_method' => fake()->randomElement($handoverMethods),
            'status' => fake()->randomElement($statuses),
            'condition' => fake()->randomElement($conditions),
            'quantity' => fake()->randomFloat(2, 1, 100),
            'quantity_unit' => fake()->randomElement($quantityUnits),
            'harvest_date' => fake()->dateTimeBetween('-6 months', '+6 months'),
            'region' => fake()->randomElement(['Gharb', 'Haouz', 'Souss', 'Oriental', 'Loukkos']),
            'brand' => fake()->randomElement($brands),
            'season' => fake()->randomElement($seasons),
            'contact_phone' => '+2126' . fake()->numerify('########'),
            'sizes' => null,
            'colors' => null,
            'views_count' => fake()->numberBetween(0, 1000),
            'favorites_count' => fake()->numberBetween(0, 50),
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Create a sell listing
     */
    public function sell(): static
    {
        return $this->state(fn (array $attributes) => [
            'listing_mode' => 'sell',
            'price' => fake()->randomFloat(2, 5, 150),
        ]);
    }

    /**
     * Create a donate listing
     */
    public function donate(): static
    {
        return $this->state(fn (array $attributes) => [
            'listing_mode' => 'donate',
            'price' => null,
        ]);
    }

    /**
     * Create an active listing
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $attributes['listing_mode'] === 'donate' ? 'donate' : 'sell',
        ]);
    }

    /**
     * Create a popular listing (with high views)
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'views_count' => fake()->numberBetween(500, 2000),
            'favorites_count' => fake()->numberBetween(20, 100),
        ]);
    }

    /**
     * Create a recently listed product
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
