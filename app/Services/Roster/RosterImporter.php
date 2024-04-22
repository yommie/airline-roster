<?php

namespace App\Services\Roster;

use App\DTOs\Roster\RosterActivityDTO;
use App\Enums\ActivityTypeEnum;
use App\Models\DayActivity;
use App\Models\User;
use App\Services\Roster\Exceptions\InvalidRosterException;
use App\Services\Roster\Exceptions\RosterImportException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RosterImporter
{
    /**
     * @param User $user
     * @param array<RosterActivityDTO> $activities
     * @return void
     * @throws RosterImportException
     */
    public function import(User $user, array $activities): void
    {
        try {
            DB::beginTransaction();

            foreach ($activities as $activity) {
                $this->importActivity($user, $activity);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            if ($e instanceof InvalidRosterException) {
                throw $e;
            }

            throw new RosterImportException(
                message: "Import failed",
                previous: $e
            );
        }
    }

    private function importActivity(User $user, RosterActivityDTO $activity): void
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

        $activityData["user_id"]            = $user->id;
        $activityData["activity_type_id"]   = $activityTypeId;

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
