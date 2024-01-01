<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_health_returns_a_successful_response()
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200);
    }
}
