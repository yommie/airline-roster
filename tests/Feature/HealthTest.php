<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_health_returns_a_successful_response(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
    }
}
