<?php

namespace App\Services\Roster;

use App\DTOs\Roster\RosterActivityDTO;

class RosterParser
{
    public function __construct(private ParseRosterInterface $parser)
    {
    }

    /**
     * @param string $data
     *
     * @return array<RosterActivityDTO>
     */
    public function parse(string $data): array
    {
        return $this->parser->parse($data);
    }
}
