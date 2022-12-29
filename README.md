# The Official Holiday and Event API for PHP

[![Packagist Version](https://img.shields.io/packagist/v/westy92/holiday-event-api)](https://packagist.org/packages/westy92/holiday-event-api)
[![Packagist PHP Version](https://img.shields.io/packagist/dependency-v/westy92/holiday-event-api/php)](https://php.net/)
[![Build Status](https://github.com/westy92/holiday-event-api-php/actions/workflows/ci.yml/badge.svg)](https://github.com/westy92/holiday-event-api-php/actions)
[![Code Coverage](https://codecov.io/gh/westy92/holiday-event-api-php/branch/main/graph/badge.svg)](https://codecov.io/gh/westy92/holiday-event-api-php)
[![Type Coverage](https://shepherd.dev/github/westy92/holiday-event-api-php/coverage.svg)](https://shepherd.dev/github/westy92/holiday-event-api-php)
[![Funding Status](https://img.shields.io/github/sponsors/westy92)](https://github.com/sponsors/westy92)

Industry-leading Holiday and Event API for PHP. Over 5,000 holidays and thousands of descriptions. Trusted by the World’s leading companies. Built by developers for developers since 2011.

## Supported PHP Versions

The latest version of the the Holiday and Event API supports all currently-supported PHP [releases](https://endoflife.date/php).

## Authentication

Access to the Holiday and Event API requires an API Key. You can get for one for FREE [here](https://apilayer.com/marketplace/checkiday-api#pricing), no credit card required! Note that free plans are limited. To access more data and have more requests, a paid plan is required.

## Installation

The recommended way to install the Holiday and Event API is through [Composer](https://getcomposer.org/).

```
composer require westy92/holiday-event-api
```

## Example

```php
try {
    // Get a FREE API key from https://apilayer.com/marketplace/checkiday-api#pricing
    $client = new Westy92\HolidayEventApi\Client('<your API key>');

    // Get Events for a given Date
    $events = $client->getEvents(
        // These parameters are the defaults but can be specified:
        // date: 'today',
        // timezone: 'America/Chicago',
        // adult: false,
    );

    $event = $events->events[0];
    echo "Today is {$event->name}! Find more information at: {$event->url}." . PHP_EOL;
    echo "Rate limit remaining: {$events->rateLimit?->remainingMonth}/{$events->rateLimit?->limitMonth} (month)." . PHP_EOL;

    // Get Event Information
    $eventInfo = $client->getEventInfo(
        id: $event->id,
        // These parameters can be specified to calculate the range of eventInfo->Event->Occurrences
        // start: 2020,
        // end: 2030,
    );

    $hashtags = implode(', ', $eventInfo->event->hashtags);
    echo "The Event's hashtags are {$hashtags}." . PHP_EOL;

    // Search for Events
    $query = "zucchini";
    $search = $client->search(
        query: $query,
        // These parameters are the defaults but can be specified:
        // adult: false,
    );

    $count = count($search->events);
    echo "Found {$count} events, including '{$search->events[0]->name}', that match the query '{$query}'." . PHP_EOL;
} catch (\Exception $e) {
    echo $e;
}
```

## License

The Holiday and Event API is made available under the MIT License (MIT). Please see the [License File](LICENSE) for more information.
