<?php

namespace Tests\Unit\Services\Roster\Factories;

use App\Services\Roster\Factories\ParseRosterFactory;
use App\Services\Roster\ParseRosterInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ParseRosterFactoryTest extends TestCase
{
    public function test_create_roster_parser_returns_instance_of_parse_roster_interface()
    {
        $parser = ParseRosterFactory::createRosterParser('html');
        $this->assertInstanceOf(ParseRosterInterface::class, $parser);
    }

    public function test_create_roster_parser_throws_exception_for_unsupported_format()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("A parser for the format csv does not exist.");

        ParseRosterFactory::createRosterParser('csv');
    }
}
