<?php

namespace App\Listeners;

use App\Events\OrderPlacedEvent;
use App\Helper\CalculationsHelper;
use App\Mail\IngredientThresholdMail;
use App\Models\Ingredient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        $threshold_percentage = 0.5; // 50%
        $ingredients = Ingredient::with('productIngredients.product.orders')->get();
        $items_reached_threshold = [];
        foreach ($ingredients as $ingredient) {
            $threshold = $ingredient->weight_in_grams * $threshold_percentage;
            if ($ingredient->ordered_weight_sum >= $threshold && !Cache::has('threshold_' . $ingredient->id)){
                $items_reached_threshold [] = [
                    'id' => $ingredient->id,
                    'item' => $ingredient->item,
                    'weight' => CalculationsHelper::gramToKgConverter(weight_in_grams: $ingredient->weight_in_grams),
                    'weight_sum' => CalculationsHelper::gramToKgConverter(weight_in_grams: $ingredient->ordered_weight_sum),
                    'threshold' => $ingredient->ordered_weight_sum / $ingredient->weight_in_grams * 100,
                ];
                // Save the threshold in the cache to avoid sending the email again for 24 hours or until the stock is refilled
                Cache::put('threshold_' . $ingredient->id, $threshold, 60 * 24 * 7);
            }

        }

        if (count($items_reached_threshold) > 0) {
            Log::emergency('Ingredient Threshold Reached', $items_reached_threshold);
            Mail::to(config('mail.admin_email'))->send(new IngredientThresholdMail($items_reached_threshold));
            Log::info('Ingredient Threshold Mail Sent');
        }
    }


}
