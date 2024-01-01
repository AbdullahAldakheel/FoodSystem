<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductIngredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'name' => 'Burger',
                'ingredients' => [
                    [
                        'item' => 'Beef',
                        'weight_in_grams' => 150
                    ],
                    [
                        'item' => 'Cheese',
                        'weight_in_grams' => 30
                    ],
                    [
                        'item' => 'Onion',
                        'weight_in_grams' => 20
                    ],
                ]
            ]
        ];

        foreach ($items as $item) {
            // Create a product with individual ingredients related to it
            $product = Product::query()->firstOrCreate(['name' => $item['name']]);
            foreach ($item['ingredients'] as $ingredient) {
                $product->productIngredients()->firstOrCreate([
                    'ingredient_id' => Ingredient::query()->where('item', $ingredient['item'])->first()->id,
                    'weight_in_grams' => $ingredient['weight_in_grams']
                ]);
            }

        }
    }
}
