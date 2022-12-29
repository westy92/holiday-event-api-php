<?php

namespace Westy92\HolidayEventApi\Model;

/**
 * Information about an Event Founder
 */
final class FounderInfo
{
    /**
     * The Founder's name
     */
    public string $name;
    /**
     * A link to the Founder
     */
    public ?string $url;
    /**
     * The date the Event was founded
     */
    public ?string $date;

    public function __construct(string $name, ?string $url, ?string $date) {
        $this->name = $name;
        $this->url = $url;
        $this->date = $date;
    }
}
