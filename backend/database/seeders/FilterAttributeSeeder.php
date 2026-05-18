<?php

namespace Database\Seeders;

use App\Models\FilterAttribute;
use Illuminate\Database\Seeder;

class FilterAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            'quantityUnits' => [
                ['id' => 1, 'label' => 'Kilogramme (kg)', 'value' => 'kg'],
                ['id' => 2, 'label' => 'Tonne (T)', 'value' => 'ton'],
                ['id' => 3, 'label' => 'Hectare (Ha)', 'value' => 'hectare'],
                ['id' => 4, 'label' => 'Litre (L)', 'value' => 'litre'],
                ['id' => 5, 'label' => 'Unité', 'value' => 'unit'],
            ],
            'harvestSeasons' => [
                ['id' => 1, 'label' => 'Printemps', 'value' => 'spring'],
                ['id' => 2, 'label' => 'Été', 'value' => 'summer'],
                ['id' => 3, 'label' => 'Automne', 'value' => 'autumn'],
                ['id' => 4, 'label' => 'Hiver', 'value' => 'winter'],
                ['id' => 5, 'label' => 'Toute l\'année', 'value' => 'year-round'],
            ],
            'soilTypes' => [
                ['id' => 1, 'label' => 'Sableux', 'value' => 'sandy'],
                ['id' => 2, 'label' => 'Argileux', 'value' => 'clayey'],
                ['id' => 3, 'label' => 'Limoneux', 'value' => 'loamy'],
                ['id' => 4, 'label' => 'Calcaire', 'value' => 'calcareous'],
            ],
            'conditions' => [
                ['id' => 1, 'label' => 'Frais', 'value' => 'fresh', 'color' => '#00b894'],
                ['id' => 2, 'label' => 'Séché', 'value' => 'dried', 'color' => '#0984e3'],
                ['id' => 3, 'label' => 'Transformé', 'value' => 'processed', 'color' => '#fdcb6e'],
                ['id' => 4, 'label' => 'Standard', 'value' => 'standard', 'color' => '#e17055'],
            ],
            'listingTypes' => [
                ['id' => 1, 'label' => 'À vendre', 'icon' => '🛒', 'value' => 'sell'],
                ['id' => 2, 'label' => 'À donner / Gratuit', 'icon' => '🎁', 'value' => 'donate'],
                ['id' => 3, 'label' => 'Échange', 'icon' => '🔄', 'value' => 'swap'],
            ],
            'regions' => [
                ['id' => 1, 'label' => 'Gharb-Chrarda-Beni Hssen', 'value' => 'gharb'],
                ['id' => 2, 'label' => 'Souss-Massa', 'value' => 'souss'],
                ['id' => 3, 'label' => 'Marrakech-Safi', 'value' => 'marrakech-safi'],
                ['id' => 4, 'label' => 'Fès-Meknès', 'value' => 'fes-meknes'],
                ['id' => 5, 'label' => 'Tanger-Tétouan-Al Hoceïma', 'value' => 'tanger'],
            ],
            'colors' => [
                ['id' => 1, 'label' => 'Noir', 'value' => 'Noir', 'hex' => '#000000'],
                ['id' => 2, 'label' => 'Blanc', 'value' => 'Blanc', 'hex' => '#FFFFFF'],
                ['id' => 3, 'label' => 'Gris', 'value' => 'Gris', 'hex' => '#808080'],
                ['id' => 4, 'label' => 'Rouge', 'value' => 'Rouge', 'hex' => '#FF0000'],
                ['id' => 5, 'label' => 'Bleu', 'value' => 'Bleu', 'hex' => '#0000FF'],
                ['id' => 6, 'label' => 'Vert', 'value' => 'Vert', 'hex' => '#008000'],
                ['id' => 7, 'label' => 'Jaune', 'value' => 'Jaune', 'hex' => '#FFFF00'],
                ['id' => 8, 'label' => 'Rose', 'value' => 'Rose', 'hex' => '#FFC0CB'],
                ['id' => 9, 'label' => 'Violet', 'value' => 'Violet', 'hex' => '#800080'],
                ['id' => 10, 'label' => 'Orange', 'value' => 'Orange', 'hex' => '#FFA500'],
            ],
        ];

        foreach ($attributes as $group => $data) {
            FilterAttribute::updateOrCreate(
                ['group' => $group],
                ['data' => $data]
            );
        }
    }
}
