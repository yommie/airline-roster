<?php

namespace App\Http\Resources\Api\V1;

use App\Models\DayActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DayActivity */
class DayActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'date'              => $this->date,
            'activity_type'     => $this->activity_type,
            'activity_type_id'  => $this->activity_type_id,
            'activity_start'    => $this->activity_start,
            'activity_end'      => $this->activity_end,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'user'              => [
                "id"    => $this->user->id,
                "name"  => $this->user->name,
            ],
            'activity_data'     => $this->getActivityData(
                $this->activity_type,
                $this->activity_type_id
            ),
            'extra_data'        => json_decode($this->extra_data, true),
        ];
    }

    private function getActivityData(string $activityType, ?string $activityTypeId = null)
    {
        if (
            $activityTypeId === null ||
            !isset(DayActivity::ACTIVITY_TYPE_MAP[$activityType])
        ) {
            return [];
        }

        $activityTypeModel = DayActivity::ACTIVITY_TYPE_MAP[$activityType];

        return $activityTypeModel::where('id', $activityTypeId)->first();
    }
}
