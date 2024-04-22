<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RosterTest extends TestCase
{
    use RefreshDatabase, WithUserTrait;

    public function test_valid_roster_upload(): void
    {
        $user = $this->makeUser();

        $roster = UploadedFile::fake()->createWithContent(
            "ValidRoster.html",
            Storage::disk("sample")->get("ValidRoster.html")
        );

        $response = $this
            ->actingAs($user)
            ->withHeaders([
                "Accept" => "application/json"
            ])
            ->post("/api/v1/upload-roster", [
                "roster" => $roster
            ])
        ;

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_invalid_roster_upload(): void
    {
        $user = $this->makeUser();

        $roster = UploadedFile::fake()->createWithContent(
            "InvalidPeriodRoster.html",
            Storage::disk("sample")->get("InvalidPeriodRoster.html")
        );

        $response = $this
            ->actingAs($user)
            ->withHeaders([
                "Accept" => "application/json"
            ])
            ->post("/api/v1/upload-roster", [
                "roster" => $roster
            ])
        ;

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
