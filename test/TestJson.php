<?php

namespace Westy92\HolidayEventApi\Tests;

class TestJson
{
    public static function getEventsDefaultJson(): string
    {
        return file_get_contents(__DIR__ . '/getEvents-default.json');
    }

    public static function getEventsParametersJson(): string
    {
        return file_get_contents(__DIR__ . '/getEvents-parameters.json');
    }

    public static function searchDefaultJson(): string
    {
        return file_get_contents(__DIR__ . '/search-default.json');
    }

    public static function searchParametersJson(): string
    {
        return file_get_contents(__DIR__ . '/search-parameters.json');
    }

    public static function getEventInfoDefaultJson(): string
    {
        return file_get_contents(__DIR__ . '/getEventInfo.json');
    }

    public static function getEventInfoParametersJson(): string
    {
        return file_get_contents(__DIR__ . '/getEventInfo-parameters.json');
    }
}
