<?php

namespace Tests\Feature\Services\Roster;

use App\DTOs\Roster\Activities\FlightActivityDTO;
use App\DTOs\Roster\RosterActivityDTO;
use App\Enums\ActivityTypeEnum;
use App\Models\User;
use App\Services\Roster\Exceptions\RosterImportException;
use App\Services\Roster\RosterImporter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class RosterImporterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_valid_import(): void
    {
        $user = User::factory()->create();

        /**
         * @var array<RosterActivityDTO> $activities
         */
        $activities = [
            new RosterActivityDTO(now(), ActivityTypeEnum::CheckIn),
            new RosterActivityDTO(now(), ActivityTypeEnum::CheckOut),
            new RosterActivityDTO(
                now(),
                ActivityTypeEnum::Flight,
                Carbon::create(2024, 2, 4, 12, 30),
                Carbon::create(2024, 2, 4, 16, 30),
                new FlightActivityDTO(
                    "DX 9876",
                    "JFK",
                    Carbon::create(2024, 2, 4, 12, 30),
                    "LAX",
                    Carbon::create(2024, 2, 4, 16, 30),
                    0,
                    240,
                    0,
                    240,
                    0,
                    "RJX 45"
                )
            ),
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

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_import_rolls_back_transaction_on_error()
    {
        $this->expectException(RosterImportException::class);
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
