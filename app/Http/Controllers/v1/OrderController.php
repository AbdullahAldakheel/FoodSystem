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
        $data_to_insert = [];
        $timestamp = date('Y-m-d H:i:s');

        // Loop through the products and add them to the data to insert
        foreach ($data['products'] as $product) {
            $quantity = $product['quantity'];
            $record = [
                'product_id' => $product['product_id'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
            $flatten_product = array_fill(0, $quantity, $record);
            $data_to_insert = array_merge($data_to_insert, $flatten_product);
        }

        Order::query()->insert($data_to_insert);
        // Trigger the event
        event(new OrderPlacedEvent());
        return Response::json([
            'status' => 'ok',
            'message' => 'Order placed successfully'
        ]);
    }
}
