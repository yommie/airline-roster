<?php

namespace App\Filters\Api\V1;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;

class RosterActivitiesFilter
{
    public static function apply(Builder $query, array $params): Builder
    {
        self::filterDate($query, $params);

        if (isset($params['event'])) {
            $query->where('activity_type', $params['event']);
        }

        return $query;
    }

    private static function filterDate(Builder $query, array $params): void
    {
        if (isset($params["date"])) {
            $query->whereDate(
                "date",
                DateTime::createFromFormat("Y-m-d", $params["date"])
            );

            return;
        }

        if (isset($params["week"])) {
            self::filterWeek($query, $params);

            return;
        }

        if (isset($params["start_date"])) {
            $startDate = Carbon::createFromFormat("Y-m-d", $params["start_date"]);

            if ($startDate !== null) {
                $query->whereDate(
                    "date",
                    ">=",
                    $startDate
                );
            }
        }

        if (isset($params["end_date"])) {
            $endDate = Carbon::createFromFormat("Y-m-d", $params["end_date"]);

            if ($endDate !== null) {
                $query->whereDate(
                    "date",
                    "<=",
                    $endDate
                );
            }
        }
    }

    private static function filterWeek(Builder $query, array $params): void
    {
        if (!isset($params["week"])) {
            return;
        }

        if ($params["week"] === "next") {
            $startOfWeek = (new Carbon())->endOfWeek()->addMicrosecond();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();

            $query->whereBetween(
                'date',
                [
                    $startOfWeek,
                    $endOfWeek
                ]
            );

            return;
        }

        if ($params["week"] === "previous") {
            $endOfWeek = (new Carbon())->startOfWeek()->subMicrosecond();
            $startOfWeek = $endOfWeek->copy()->startOfWeek();

            $query->whereBetween(
                'date',
                [
                    $startOfWeek,
                    $endOfWeek
                ]
            );

            return;
        }

        if ($params["week"] === "current") {
            $query->whereBetween(
                'date',
                [
                    (new Carbon())->startOfWeek(),
                    (new Carbon())->endOfWeek()
                ]
            );

            return;
        }

        $date = Carbon::createFromFormat("Y-m-d", $params["week"]);

        if ($date !== false) {
            $query->whereBetween(
                'date',
                [
                    $date->copy()->startOfWeek(),
                    $date->copy()->endOfWeek()
                ]
            );
        }
    }
}
