<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlaceOrderTest extends TestCase
{
    /**
     * @description Test the place order endpoint
     * @return void
     */
    public function test_place_order_endpoint(): void
    {
        // Run your migrations
        $this->artisan('migrate');
        // Run Seeders
        $this->artisan('db:seed');
        // Get Product id
        $product_id = \App\Models\Product::query()->first()->id;
        $response = $this->postJson('/api/orders', [
            'products' => [
                [
                    'product_id' => $product_id,
                    'quantity' => 1
                ],
                [
                    'product_id' => $product_id,
                    'quantity' => 1
                ]
            ]
        ]);
        $response->assertStatus(200);
    }
}
