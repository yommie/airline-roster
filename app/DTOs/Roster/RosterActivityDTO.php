<?php

namespace App\DTOs\Roster;

use App\Enums\ActivityTypeEnum;
use DateTime;
use InvalidArgumentException;

readonly class RosterActivityDTO
{
    public function __construct(
        public DateTime                     $date,
        public ActivityTypeEnum             $activityType,
        public ?DateTime                    $activityStart = null,
        public ?DateTime                    $activityEnd = null,
        public ?AbstractRosterActivityDTO   $rosterActivity = null,
        public array                        $extraData = []
    ) {
        $this->validateRosterActivity();
    }

    public function toArray(): array
    {
        return [
            "date"              => $this->date,
            "activity_type"     => $this->activityType->value,
            "activity_start"    => $this->activityStart,
            "activity_end"      => $this->activityEnd,
            "activity_data"     => $this->rosterActivity !== null ?
                $this->rosterActivity->toArray() :
                []
            ,
            "extra_data"        => json_encode($this->extraData),
        ];
    }

    private function validateRosterActivity(): void
    {
        if (
            $this->rosterActivity !== null &&
            $this->activityType !== $this->rosterActivity->activityType
        ) {
            throw new InvalidArgumentException(sprintf(
                "Invalid roster activity type. Roster activity type must be %s",
                $this->activityType->value
            ));
        }
    }
}
