<?php

namespace App\Http\Controllers;

use App\Helper\CalculationsHelper;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class GeneralController extends Controller
{
    /**
     * @description Current Stock
     * @return JsonResponse
     */
    public function stock(): JsonResponse
    {
        $ingredients = Ingredient::with('productIngredients.product.orders')->get();
        $data = [];
        foreach ($ingredients as $ingredient) {
            $item = [
                'ingredient' => $ingredient->item,
                'weight' => CalculationsHelper::gramToKgConverter(weight_in_grams: $ingredient->weight_in_grams),
                'ordered_weight' => CalculationsHelper::gramToKgConverter(weight_in_grams: $ingredient->ordered_weight_sum),
            ];
            $threshold = $ingredient->weight_in_grams * 0.5; // 50% of the weight in the Ingredient table
            $item['weight_reached_threshold'] = $ingredient->ordered_weight_sum >= $threshold;
            $item['percentage_used'] = $ingredient->ordered_weight_sum / $ingredient->weight_in_grams * 100 . '%';
            $data[] = $item;
        }

        return Response::json($data);
    }


    /**
     * @description Health check endpoint
     * @return JsonResponse
     */
    public function health(): JsonResponse
    {
        return Response::json([
            'status' => 'ok',
            'message' => 'The application is running'
        ]);
    }
}
