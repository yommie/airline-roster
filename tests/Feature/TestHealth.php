<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestHealth extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_health_returns_a_successful_response(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
    }
}
