<?php

namespace Tests\Feature\Services\Roster;

use App\Services\Roster\ParseRosterInterface;
use App\Services\Roster\RosterParser;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class RosterParserTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_parse_calls_parse_method_of_injected_parser()
    {
        $parserMock = $this->createMock(ParseRosterInterface::class);

        $parserMock->expects($this->once())
            ->method('parse')
            ->willReturn([]);

        $rosterParser = new RosterParser($parserMock);

        $result = $rosterParser->parse("");

        $this->assertIsArray($result);
        $this->assertEquals([], $result);
    }
}
