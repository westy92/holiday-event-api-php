<?php

namespace Westy92\HolidayEventApi\Model;

/**
 * Information about an Event's Occurrence
 */
final class Occurrence
{
    /**
     * The date or timestamp the Event occurs
     */
    public string $date;
    /**
     * The length (in days) of the Event occurrence
     */
    public int $length;

    public function __construct(string $date, int $length) {
        $this->date = $date;
        $this->length = $length;
    }
}
