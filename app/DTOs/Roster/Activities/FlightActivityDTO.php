<?php

namespace App\DTOs\Roster\Activities;

use App\DTOs\Roster\AbstractRosterActivityDTO;
use App\Enums\ActivityTypeEnum;
use DateTime;

readonly class FlightActivityDTO extends AbstractRosterActivityDTO
{
    public function __construct(
        public string   $flightNumber,
        public string   $departureLocation,
        public DateTime $departureTime,
        public string   $arrivalLocation,
        public DateTime $arrivalTime,
        public int      $blockHours,
        public int      $flightDuration,
        public int      $nightDuration,
        public int      $activityDuration,
        public int      $extensionDuration,
        public string   $aircraftRegistrationNumber,
        public ?int     $passengersOnFlight = null
    ) {
        parent::__construct(ActivityTypeEnum::Flight);
    }

    public function toArray(): array
    {
        return [
            'flight_number'                     => $this->flightNumber,
            'departure_location'                => $this->departureLocation,
            'departure_time'                    => $this->departureTime,
            'arrival_location'                  => $this->arrivalLocation,
            'arrival_time'                      => $this->arrivalTime,
            'block_hours'                       => $this->blockHours,
            'flight_duration'                   => $this->flightDuration,
            'night_duration'                    => $this->nightDuration,
            'activity_duration'                 => $this->activityDuration,
            'extension_duration'                => $this->extensionDuration,
            'passengers_on_flight'              => $this->passengersOnFlight,
            'aircraft_registration_number'      => $this->aircraftRegistrationNumber,
        ];
    }
}
