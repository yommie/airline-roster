<?php

namespace App\Services\Roster;

use App\DTOs\Roster\RosterActivityDTO;

interface ParseRosterInterface
{
    /**
     * @param string $data
     * @return array<RosterActivityDTO>
     */
    public function parse(string $data): array;
}
