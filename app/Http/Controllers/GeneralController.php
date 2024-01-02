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
        $ingredients = collect(DB::select(DB::raw("select id, item,weight_in_grams, sum(cc) as ordered_weight_sum   from (
                                                            SELECT i.id, i.item, i.weight_in_grams AS weight_in_grams, pi.weight_in_grams * count(o.id) AS cc
                                                                FROM ingredients i JOIN product_ingredients pi ON i.id = pi.ingredient_id
                                                                JOIN products p ON pi.product_id = p.id
                                                                LEFT JOIN  orders o ON p.id = o.product_id
                                                                GROUP BY i.id, pi.id
                                                                ORDER BY p.id
                                                            ) AS list GROUP BY list.id;")));
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
