<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    /**
     * Test Table Exists.
     *
     * @return void
     */
    public function test_creates_products_table()
    {
        // Run your migrations
        $this->artisan('migrate');

        // Assert that the "products" table exists in the database
        $this->assertTrue(Schema::hasTable('products'));
    }

    /**
     * Test Beef Ingredient Exists.
     *
     * @return void
     */
    public function test_beef_ingredient_exists()
    {
        // Run Seeders
        $this->artisan('db:seed');

        // Assert that the "Beef" ingredient exists in the database
        $this->assertDatabaseHas('ingredients', [
            'item' => 'Beef',
            'weight_in_grams' => 20000
        ]);
    }


}
