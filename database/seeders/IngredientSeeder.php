<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $items = [
            [
                'item' => 'Beef',
                'weight_in_grams' => 20000
            ],
            [
                'item' => 'Cheese',
                'weight_in_grams' => 5000
            ],
            [
                'item' => 'Onion',
                'weight_in_grams' => 1000
            ],
        ];

        foreach ($items as $item) {
            Ingredient::query()->firstOrCreate($item);
        }
    }
}
