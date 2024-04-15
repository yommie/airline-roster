<?php

namespace App\Enums;

enum ActivityTypeEnum: string
{
    case CheckIn    = "check_in";

    case CheckOut   = "check_out";

    case Flight     = "flight";

    case StandBy    = "stand_by";

    case Off        = "off";

    case Unknown    = "unknown";
}
