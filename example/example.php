<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

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
