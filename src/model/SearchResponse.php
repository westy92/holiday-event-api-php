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

    /**
     * @param EventSummary[] $events
     */
    public function __construct(string $query, bool $adult, array $events, ?RateLimit $rateLimit = null) {
        $this->query = $query;
        $this->adult = $adult;
        $this->events = $events;
        parent::__construct($rateLimit);
    }
}
