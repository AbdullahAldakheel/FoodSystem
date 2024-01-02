<?php

namespace App\Http\Controllers\v1;

use App\Events\OrderPlacedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\CreateOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    /**
     * Create a new Order
     *
     * @param CreateOrderRequest $request
     * @return JsonResponse
     */
    public function create(CreateOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $timestamp = now();

        // Use array_map to efficiently generate records for each product
        $dataToInsert = Arr::map($data['products'], fn($product) => array_fill(0, $product['quantity'], [
            'product_id' => $product['product_id'],
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]));

        // Flatten the array of arrays into a single array
        $dataToInsert = array_merge(...$dataToInsert);

        // Insert the prepared data into the 'orders' table
        Order::query()->insert($dataToInsert);

        // Trigger the OrderPlacedEvent to handle additional actions
        event(new OrderPlacedEvent());

        return Response::json([
            'status' => 'ok',
            'message' => 'Order placed successfully'
        ]);
    }
}
