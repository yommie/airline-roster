<?php

namespace Tests\Feature\Services\Roster;

use App\DTOs\Roster\RosterActivityDTO;
use App\Enums\ActivityTypeEnum;
use App\Models\User;
use App\Services\Roster\RosterImporter;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class RosterImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_import(): void
    {
        $user = User::factory()->create();

        /**
         * @var array<RosterActivityDTO> $activities
         */
        $activities = [
            new RosterActivityDTO(now(), ActivityTypeEnum::CheckIn),
            new RosterActivityDTO(now(), ActivityTypeEnum::CheckOut),
        ];

        $dbMock = Mockery::mock('alias:Illuminate\Support\Facades\DB');
        $dbMock->shouldReceive('beginTransaction')->once();
        $dbMock->shouldReceive('commit')->once();

        $importer = new RosterImporter();
        $importer->import($user, $activities);

        foreach ($activities as $activity) {
            $this->assertDatabaseHas('day_activities', [
                'user_id'       => $user->id,
                'activity_type' => $activity->activityType->value
            ]);
        }
    }

    public function test_import_rolls_back_transaction_on_error()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Import failed");

        $user = User::factory()->create();

        User::destroy($user->id);

        $activities = [
            new RosterActivityDTO(now(), ActivityTypeEnum::CheckIn),
            new RosterActivityDTO(now(), ActivityTypeEnum::CheckOut),
        ];
        $dbMock = Mockery::mock('alias:Illuminate\Support\Facades\DB');
        $dbMock->shouldReceive('beginTransaction')->once();
        $dbMock->shouldReceive('rollback')->once();

        $importer = new RosterImporter();
        $importer->import($user, $activities);
    }
}
