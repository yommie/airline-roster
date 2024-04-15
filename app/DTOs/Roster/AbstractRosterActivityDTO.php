<?php

namespace App\DTOs\Roster;

use App\Enums\ActivityTypeEnum;

readonly abstract class AbstractRosterActivityDTO
{
    public function __construct(public ActivityTypeEnum $activityType = ActivityTypeEnum::Unknown)
    {
    }

    abstract public function toArray(): array;
}
