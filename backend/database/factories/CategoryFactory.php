<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Crops' => ['icon' => 'leaf', 'children' => ['Cereals', 'Fruits', 'Vegetables', 'Legumes']],
            'Livestock' => ['icon' => 'cow', 'children' => ['Cattle', 'Poultry', 'Sheep & Goats']],
            'Equipment' => ['icon' => 'tractor', 'children' => ['Tractors', 'Harvesters', 'Tools']],
            'Land' => ['icon' => 'map', 'children' => ['Farm Land', 'Orchards', 'Grazing Land']],
            'Seeds' => ['icon' => 'sprout', 'children' => ['Crop Seeds', 'Vegetable Seeds']],
        ];

        $categoryName = fake()->randomElement(array_keys($categories));
        $isParent = fake()->boolean(70); // 70% chance of being a parent category

        if ($isParent) {
            return [
                'parent_id' => null,
                'name' => $categoryName,
                'name_ar' => fake()->word(),
                'name_fr' => fake()->word(),
                'slug' => strtolower(str_replace(' ', '-', $categoryName)) . '-' . uniqid(),
                'icon' => $categories[$categoryName]['icon'],
                'sort_order' => fake()->numberBetween(1, 100),
                'is_active' => true,
            ];
        } else {
            $parentCategory = Category::whereNull('parent_id')->inRandomOrder()->first();
            if (!$parentCategory) {
                // If no parent exists, create one first
                $parentName = fake()->randomElement(array_keys($categories));
                $parentCategory = Category::factory()->parent()->create([
                    'name' => $parentName,
                    'slug' => strtolower(str_replace(' ', '-', $parentName)) . '-' . uniqid(),
                    'icon' => $categories[$parentName]['icon'],
                ]);
            }
            $childCategories = $categories[$parentCategory->name]['children'] ?? ['General'];
            $childName = fake()->randomElement($childCategories);

            return [
                'parent_id' => $parentCategory->id,
                'name' => $childName,
                'name_ar' => fake()->word(),
                'name_fr' => fake()->word(),
                'slug' => strtolower(str_replace(' ', '-', $childName)) . '-' . uniqid(),
                'icon' => null,
                'sort_order' => fake()->numberBetween(1, 100),
                'is_active' => true,
            ];
        }
    }

    /**
     * Create a parent category
     */
    public function parent(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }

    /**
     * Create a child category
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => Category::whereNull('parent_id')->inRandomOrder()->first()->id,
        ]);
    }
}
