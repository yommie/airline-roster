<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithUserTrait;

    private const SAMPLE_LOGIN_DATA = [
        "email"     => "yommie@airlines.com",
        "password"  => "password"
    ];

    public function test_valid_registration(): void
    {
        $response = $this->json("POST", "/api/v1/register", self::SAMPLE_REGISTRATION_DATA);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_invalid_registration(): void
    {
        $this->makeUser();

        $response = $this->json("POST", "/api/v1/register", self::SAMPLE_REGISTRATION_DATA);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_valid_login(): void
    {
        $this->makeUser();

        $response = $this->json("POST", "/api/v1/login", self::SAMPLE_LOGIN_DATA);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_invalid_login(): void
    {
        $response = $this->json("POST", "/api/v1/login", self::SAMPLE_LOGIN_DATA);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_logout(): void
    {
        $user = $this->makeUser();

        $response = $this
            ->actingAs($user)
            ->json("POST", "/api/v1/logout")
        ;

        $response->assertStatus(Response::HTTP_OK);
    }
}
