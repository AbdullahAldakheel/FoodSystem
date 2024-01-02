<?php

namespace App\Listeners;

use App\Events\OrderPlacedEvent;
use App\Helper\CalculationsHelper;
use App\Mail\IngredientThresholdMail;
use App\Models\Ingredient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use stdClass;

class StockValidatorListener implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @description Check the stock if one of the ingredients is less than 50% of the stock then send an email
     * @param  OrderPlacedEvent  $event
     * @return void
     */
    public function handle(OrderPlacedEvent $event): void
    {
        // Eloquent query
//        $ingredients = Ingredient::query()
//            ->select('id', 'item', 'weight_in_grams')
//            ->with('productIngredients.product.orders')
//            ->get();

        // Optimized query
        $ingredientsData = collect(DB::select(DB::raw("select id, item,weight_in_grams, sum(cc) as ordered_weight_sum   from (
                                                            SELECT i.id, i.item, i.weight_in_grams AS weight_in_grams, pi.weight_in_grams * count(o.id) AS cc
                                                                FROM ingredients i JOIN product_ingredients pi ON i.id = pi.ingredient_id
                                                                JOIN products p ON pi.product_id = p.id
                                                                LEFT JOIN  orders o ON p.id = o.product_id
                                                                GROUP BY i.id, pi.id
                                                                ORDER BY p.id
                                                            ) AS list GROUP BY list.id;")));


        $itemsReachedThreshold = $ingredientsData
            ->filter(fn($ingredient) => $this->is_ingredient_threshold_reached($ingredient))
            ->map(fn($ingredient) => $this->prepare_data_to_send($ingredient));

        if ($itemsReachedThreshold->isNotEmpty()) {
            Mail::to(config('mail.admin_email'))->send(new IngredientThresholdMail($itemsReachedThreshold));
            Log::info('Ingredient Threshold Reached & Mail Sent');
        }
    }

    /**
     * @description Check if the ingredient is less than 50% of the stock or already checked
     *
     * @param  Ingredient|stdClass  $ingredient
     * @return bool
     */
    function is_ingredient_threshold_reached(Ingredient|stdClass $ingredient): bool
    {
        $threshold = $ingredient->weight_in_grams * config('food.ingredient.threshold_percentage');
        return $ingredient->ordered_weight_sum >= $threshold && !Cache::has('threshold_' . $ingredient->id);
    }

    /**
     * @description Set the threshold in the cache to avoid sending the email again for 24 hours or until the stock is refilled & prepare the data to be sent in the email
     *
     * @param  Ingredient|stdClass  $ingredient
     * @return array
     */
    function prepare_data_to_send(Ingredient|stdClass $ingredient): array
    {
        Cache::put('threshold_' . $ingredient->id, true, 60 * 24 * 7);
        return [
            'id' => $ingredient->id,
            'item' => $ingredient->item,
            'weight' => CalculationsHelper::gramToKgConverter(weightInGrams: $ingredient->weight_in_grams),
            'weight_sum' => CalculationsHelper::gramToKgConverter(weightInGrams: $ingredient->ordered_weight_sum),
            'threshold' => $ingredient->ordered_weight_sum / $ingredient->weight_in_grams * 100,
        ];
    }


}
