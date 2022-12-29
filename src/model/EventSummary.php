<?php

namespace Westy92\HolidayEventApi\Model;

/**
 * A summary of an Event
 */
class EventSummary
{
    /**
     * The Event Id
     */
    public string $id;
    /**
     * The Event Name
     */
    public string $name;
    /**
     * The Event URL
     */
    public string $url;

    public function __construct(string $id, string $name, string $url) {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
    }
}
