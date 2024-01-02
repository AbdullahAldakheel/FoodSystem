<?php

namespace App\Http\Controllers;

use App\Helper\CalculationsHelper;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class GeneralController extends Controller
{
    /**
     * @description Current Stock
     * @return JsonResponse
     */
    public function stock(): JsonResponse
    {
        // Retrieve ingredients data with the ordered weight sum
        $ingredientsData = collect(DB::select(DB::raw("select id, item,weight_in_grams, sum(cc) as ordered_weight_sum   from (
                                                            SELECT i.id, i.item, i.weight_in_grams AS weight_in_grams, pi.weight_in_grams * count(o.id) AS cc
                                                                FROM ingredients i JOIN product_ingredients pi ON i.id = pi.ingredient_id
                                                                JOIN products p ON pi.product_id = p.id
                                                                LEFT JOIN  orders o ON p.id = o.product_id
                                                                GROUP BY i.id, pi.id
                                                                ORDER BY p.id
                                                            ) AS list GROUP BY list.id;")));

        $data = [];


        // Process each ingredient
        foreach ($ingredientsData as $ingredient) {
            // Calculate values and store in an array
            $weightInKg = CalculationsHelper::gramToKgConverter(weightInGrams: $ingredient->weight_in_grams);
            $orderedWeightInKg = CalculationsHelper::gramToKgConverter(weightInGrams: $ingredient->ordered_weight_sum);

            // Set threshold as 50% of the weight in the Ingredient table
            $threshold = $ingredient->weight_in_grams * config('food.ingredient.threshold_percentage');

            // Determine if the ordered weight has reached the threshold
            $weightReachedThreshold = $ingredient->ordered_weight_sum >= $threshold;

            // Calculate the percentage of the ingredient used
            $percentageUsed = ($ingredient->ordered_weight_sum / $ingredient->weight_in_grams) * 100;

            // Add the processed data to the result array
            $data[] = [
                'ingredient' => $ingredient->item,
                'weight' => $weightInKg,
                'ordered_weight' => $orderedWeightInKg,
                'weight_reached_threshold' => $weightReachedThreshold,
                'percentage_used' => $percentageUsed . '%',
            ];
        }

        // Return the processed data as a JSON response
        return response()->json($data);
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
