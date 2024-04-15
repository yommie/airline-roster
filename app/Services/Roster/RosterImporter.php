<?php

namespace App\Services\Roster;

use App\DTOs\Roster\RosterActivityDTO;
use App\Enums\ActivityTypeEnum;
use App\Models\DayActivity;
use Exception;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Illuminate\Support\Facades\DB;

class RosterImporter
{
    /**
     * @param array<RosterActivityDTO> $activities
     * @return void
     */
    public function import(array $activities): void
    {
        try {
            DB::beginTransaction();

            foreach ($activities as $activity) {
                $this->importActivity($activity);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new RuntimeException(
                message: "Import failed",
                previous: $e
            );
        }
    }

    private function importActivity(RosterActivityDTO $activity): void
    {
        $activityTypeId     = null;
        $activityData       = $activity->toArray();
        $activityTypeModel  = $this->getActivityTypeModel($activity->activityType);

        if (
            $activityTypeModel !== null &&
            $activity->rosterActivity !== null
        ) {
            $activityTypeModel->fill($activity->rosterActivity->toArray());

            $activityTypeModel->save();

            $activityTypeId = $activityTypeModel->id;
        }

        unset($activityData["activity_data"]);

        $activityData["activity_type_id"] = $activityTypeId;

        DayActivity::create($activityData);
    }

    private function getActivityTypeModel(ActivityTypeEnum $activityType): Model|null
    {
        if (isset(DayActivity::ACTIVITY_TYPE_MAP[$activityType->value])) {
            $activityTypeModel = DayActivity::ACTIVITY_TYPE_MAP[$activityType->value];
            return new $activityTypeModel();
        }

        return null;
    }
}
