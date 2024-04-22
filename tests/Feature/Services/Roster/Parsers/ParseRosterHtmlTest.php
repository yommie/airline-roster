<?php

namespace Tests\Feature\Services\Roster\Parsers;

use App\DTOs\Roster\RosterActivityDTO;
use App\Services\Roster\Exceptions\InvalidRosterException;
use App\Services\Roster\Parsers\ParseRosterHtml;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ParseRosterHtmlTest extends TestCase
{
    public function test_parse_empty_html()
    {
        $this->expectException(InvalidRosterException::class);
        $this->expectExceptionMessage("Could not detect period start and end dates");

        $parser = new ParseRosterHtml();
        $parser->parse("");
    }

    public function test_parse_valid_html_with_all_activities()
    {
        $html = Storage::disk("sample")->get("ValidRoster.html");

        $parser = new ParseRosterHtml();
        $activities = $parser->parse($html);

        $this->assertCount(54, $activities);
        $this->assertContainsOnlyInstancesOf(RosterActivityDTO::class, $activities);
    }

    public function test_parse_invalid_html()
    {
        $html = Storage::disk("sample")->get("InvalidPeriodRoster.html");

        $this->expectException(InvalidRosterException::class);
        $this->expectExceptionMessage("Could not detect period start and end dates");

        $parser = new ParseRosterHtml();
        $parser->parse($html);
    }
}
