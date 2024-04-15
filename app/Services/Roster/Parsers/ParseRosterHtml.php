<?php

namespace App\Services\Roster\Parsers;

use App\DTOs\Roster\Activities\FlightActivityDTO;
use App\DTOs\Roster\RosterActivityDTO;
use App\Enums\ActivityTypeEnum;
use App\Models\User;
use App\Services\Roster\ParseRosterInterface;
use DateTime;
use Illuminate\Support\Facades\Auth;
use LogicException;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

class ParseRosterHtml implements ParseRosterInterface
{
    private ?Crawler $crawler = null;

    private ?DateTime $periodStart = null;

    private ?DateTime $periodEnd = null;

    /**
     * @param string $data
     * @return array<RosterActivityDTO>
     */
    public function parse(string $data): array
    {
        $this->setCrawler($data);

        $this->setPeriod();

        return $this->parseActivityTable();
    }

    private function getCrawler(): Crawler
    {
        if ($this->crawler === null) {
            throw new LogicException("Crawler not initialised.");
        }

        return $this->crawler;
    }

    private function setCrawler(string $html): void
    {
        $this->crawler = new Crawler($html);
    }

    private function setPeriod(): void
    {
        $periodSelect = $this->getCrawler()->filter("#ctl00_Main_periodSelect option:selected");

        if ($periodSelect->count() < 1) {
            return;
        }

        $periodSelectValue = $periodSelect->attr("value");

        $periods = explode("|", $periodSelectValue);

        if ($periods[0]) {
            $periodStart = DateTime::createFromFormat("Y-m-d", $periods[0]);

            if ($periodStart !== false) {
                $this->periodStart = $periodStart;
            }
        }

        if ($periods[1]) {
            $periodEnd = DateTime::createFromFormat("Y-m-d", $periods[1]);

            if ($periodEnd !== false) {
                $this->periodEnd = $periodEnd;
            }
        }

        if ($this->periodStart === null || $this->periodEnd === null) {
            throw new RuntimeException("Could not detect period start and end dates");
        }
    }

    /**
     * @return array<RosterActivityDTO>
     */
    private function parseActivityTable(): array
    {
        $activities = [];
        $currentDay = null;

        $activityTableRows = $this->getCrawler()
            ->filter('table#ctl00_Main_activityGrid tbody tr:not(.activity-table-header)')
        ;

        $activityTableRows->each(function (Crawler $activityTableRow, $index) use (&$activities, &$currentDay) {
            $activityData = $this->parseActivityTableRow($activityTableRow);

            if (
                $currentDay === null ||
                $activityData["date"] !== null
            ) {
                $currentDay = $activityData["date"];
            } else {
                $activityData["date"] = $currentDay;
            }

            if ($activityData["activity"] === "OFF") {
                $activities[] = $this->activityArrayToDTO($activityData, ActivityTypeEnum::Off);

                return;
            }

            if ($activityData["check_in"] !== null) {
                $activities[] = $this->activityArrayToDTO($activityData, ActivityTypeEnum::CheckIn);
            }

            if ($activityData["check_out"] !== null) {
                $activities[] = $this->activityArrayToDTO($activityData, ActivityTypeEnum::CheckOut);
            }

            if ($activityData["activity"] === "SBY") {
                $activities[] = $this->activityArrayToDTO($activityData, ActivityTypeEnum::StandBy);

                return;
            }

            if ($this->isFlightActivity($activityData["activity"])) {
                $activities[] = $this->activityArrayToDTO($activityData, ActivityTypeEnum::Flight);

                return;
            }

            $activities[] = $this->activityArrayToDTO($activityData, ActivityTypeEnum::Unknown);
        });

        return $activities;
    }

    private function parseActivityTableRow(Crawler $row): array
    {
        $activityData = [
            "date"                          => null,
            "check_in"                      => null,
            "check_out"                     => null,
            "activity"                      => null,
            "remark"                        => null,
            "departure_location"            => null,
            "activity_start"                => null,
            "arrival_location"              => null,
            "activity_end"                  => null,
            "block_hours"                   => null,
            "flight_duration"               => null,
            "night_duration"                => null,
            "extension_duration"            => null,
            "activity_duration"             => null,
            "passengers_on_flight"          => null,
            "aircraft_registration_number"  => null,
        ];

        $columns = $row->filter("td");

        $columns->each(function (Crawler $column, $index) use (&$activityData) {
            $class = $column->attr("class");

            switch (true) {
                case str_contains($class, "activitytablerow-date"):
                    $dateNode = $column->filter("nobr");

                    if ($dateNode->count() === 0) {
                        return;
                    }

                    $date = $dateNode->text();

                    if ($date !== "") {
                        $year           = $this->periodStart->format("Y");
                        $month          = $this->periodStart->format("m");
                        $dateParts      = explode(" ", $date);
                        $datePartsCount = count($dateParts);

                        switch ($datePartsCount) {
                            case 2:
                                $d = str_pad($dateParts[1], 2, "0");

                                $activityData["date"] = DateTime::createFromFormat(
                                    "Y-m-d",
                                    "$year-$month-$d"
                                )->setTime(0,0);

                                break;

                            case 3:
                                $d = str_pad($dateParts[1], 2, "0");

                                $activityData["date"] = DateTime::createFromFormat(
                                    "Y-M-d",
                                    "$year-$dateParts[2]-$d"
                                )->setTime(0,0);

                                break;

                            case 4:
                                $d = str_pad($dateParts[1], 2, "0");
                                $y = str_pad($dateParts[3], 2, "0");

                                $activityData["date"] = DateTime::createFromFormat(
                                    "y-M-d",
                                    "$y-$dateParts[2]-$d"
                                )->setTime(0,0);

                                break;
                        }
                    }

                    break;

                case str_contains($class, "activitytablerow-checkinutc"):
                    $activityData["check_in"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-checkoututc"):
                    $activityData["check_out"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-activityRemark"):
                    $activityData["remark"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-activity"):
                    $activityData["activity"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-fromstn"):
                    $activityData["departure_location"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-stdutc"):
                    $activityData["activity_start"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-tostn"):
                    $activityData["arrival_location"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-stautc"):
                    $activityData["activity_end"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-blockhours"):
                    $activityData["block_hours"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-flighttime"):
                    $activityData["flight_duration"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-nighttime"):
                    $activityData["night_duration"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-counter1"):
                    $activityData["extension_duration"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-duration"):
                    $activityData["activity_duration"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-Paxbooked"):
                    $activityData["passengers_on_flight"] = $this->formatColumnValue($column->text());
                    break;

                case str_contains($class, "activitytablerow-Tailnumber"):
                    $activityData["aircraft_registration_number"] = $this->formatColumnValue($column->text());
                    break;
            }
        });

        return $activityData;
    }

    private function activityArrayToDTO(array $activity, ActivityTypeEnum $activityType): RosterActivityDTO
    {
        $user = Auth::user();

        switch ($activityType) {
            case ActivityTypeEnum::Off:
                return $this->createOffActivity($user, $activity["date"]);

            case ActivityTypeEnum::CheckIn:
                return $this->createCheckInActivity(
                    $user,
                    $activity["date"],
                    $this->timeToDateTime($activity["check_in"], $activity["date"])
                );

            case ActivityTypeEnum::CheckOut:
                return $this->createCheckOutActivity(
                    $user,
                    $activity["date"],
                    $this->timeToDateTime($activity["check_out"], $activity["date"])
                );

            case ActivityTypeEnum::Flight:
                return $this->createFlightActivity(
                    $user,
                    $activity
                );

            case ActivityTypeEnum::StandBy:
                return $this->createStandByActivity(
                    $user,
                    $activity["date"],
                    $this->timeToDateTime($activity["activity_start"], $activity["date"]),
                    $this->timeToDateTime($activity["activity_end"], $activity["date"]),
                );
        }

        return $this->createUnknownActivity($user, $activity);
    }

    private function createOffActivity(User $user, DateTime $date): RosterActivityDTO
    {
        return new RosterActivityDTO(
            $user,
            $date,
            ActivityTypeEnum::Off
        );
    }

    private function createUnknownActivity(User $user, array $activityData): RosterActivityDTO
    {
        $date   = $activityData["date"];
        $end    = $this->timeToDateTime($activityData["activity_end"], $date);
        $start  = $this->timeToDateTime($activityData["activity_start"], $date);

        unset($activityData["date"]);
        unset($activityData["check_in"]);
        unset($activityData["check_out"]);
        unset($activityData["activity_end"]);
        unset($activityData["activity_start"]);
        unset($activityData["flight_duration"]);
        unset($activityData["night_duration"]);
        unset($activityData["passengers_on_flight"]);
        unset($activityData["aircraft_registration_number"]);

        return new RosterActivityDTO(
            $user,
            $date,
            ActivityTypeEnum::Unknown,
            $start,
            $end,
            extraData: $activityData
        );
    }

    private function createCheckInActivity(
        User $user,
        DateTime $date,
        DateTime $checkInTime
    ): RosterActivityDTO {
        return new RosterActivityDTO(
            $user,
            $date,
            ActivityTypeEnum::CheckIn,
            $checkInTime
        );
    }

    private function createCheckOutActivity(
        User $user,
        DateTime $date,
        DateTime $checkOutTime
    ): RosterActivityDTO {
        return new RosterActivityDTO(
            $user,
            $date,
            ActivityTypeEnum::CheckOut,
            $checkOutTime
        );
    }

    private function createFlightActivity(
        User $user,
        array $activity
    ): RosterActivityDTO {
        $activityEnd    = $this->timeToDateTime($activity["activity_end"], $activity["date"]);
        $activityStart  = $this->timeToDateTime($activity["activity_start"], $activity["date"]);

        $flightActivity = new FlightActivityDTO(
            $activity["remark"],
            $activity["departure_location"],
            $activityStart,
            $activity["arrival_location"],
            $activityEnd,
            $this->hoursToMins($activity["block_hours"]),
            $this->hoursToMins($activity["flight_duration"]),
            $this->hoursToMins($activity["night_duration"]),
            $this->hoursToMins($activity["activity_duration"]),
            $this->hoursToMins($activity["extension_duration"]),
            $activity["aircraft_registration_number"],
            $activity["passengers_on_flight"],
        );

        return new RosterActivityDTO(
            $user,
            $activity["date"],
            ActivityTypeEnum::Flight,
            $activityStart,
            $activityEnd,
            $flightActivity
        );
    }

    private function createStandByActivity(
        User $user,
        DateTime $date,
        DateTime $start,
        DateTime $end
    ): RosterActivityDTO {
        return new RosterActivityDTO(
            $user,
            $date,
            ActivityTypeEnum::StandBy,
            $start,
            $end
        );
    }

    private function timeToDateTime(string $time, DateTime $dateTime): DateTime
    {
        $mins = substr($time, 2, 2);
        $hour = substr($time, 0, 2);

        return DateTime::createFromFormat(
            "Y-m-d H:i:s",
            $dateTime->format("Y-m-d") . " $hour:$mins:00"
        );
    }

    private function isFlightActivity(string $activityType): bool
    {
        return preg_match('/^[A-Za-z]{2}\d+$/', $activityType);
    }

    private function formatColumnValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value, " \t\n\r\0\x0B" . chr(160) . chr(194));

        return empty($value) ? null : $value;
    }

    private function hoursToMins(?string $hours = null): int
    {
        if ($hours === null) {
            return 0;
        }

        $parts = explode(":", $hours);

        if (count($parts) !== 2) {
            return 0;
        }

        $h = (int) $parts[0];
        $m = (int) $parts[1];

        return ($h * 60) + $m;
    }
}
