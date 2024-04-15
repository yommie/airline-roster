<?php

namespace App\Services\Roster\Factories;

use App\Services\Roster\Parsers\ParseRosterHtml;
use App\Services\Roster\ParseRosterInterface;
use InvalidArgumentException;

class ParseRosterFactory
{
    private const FORMAT_MAP = [
        "html" => ParseRosterHtml::class
    ];

    public static function createRosterParser(string $format): ParseRosterInterface
    {
        if (!isset(self::FORMAT_MAP[$format])) {
            throw new InvalidArgumentException(sprintf(
                "A parser for the format %s does not exist.",
                $format
            ));
        }

        $parserClass = self::FORMAT_MAP[$format];

        return new $parserClass();
    }
}
