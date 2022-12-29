<?php

namespace Westy92\HolidayEventApi\Model;

/**
 * The Response returned by search
 */
final class SearchResponse extends StandardResponse
{
    /**
     * The search query
     */
    public string $query;
    /**
     * Whether Adult entries can be included
     */
    public bool $adult;
    /**
     * The found Events
     * @var EventSummary[]
     */
    public array $events;
}
