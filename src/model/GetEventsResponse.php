<?php

namespace Westy92\HolidayEventApi\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * The Response returned by getEvents
 */
final class GetEventsResponse extends StandardResponse
{
    /**
     * Whether Adult entries can be included
     */
    public bool $adult;
    /**
     * The Date string
     */
    public string $date;
    /**
     * The Timezone used to calculate the Date's Events
     */
    public string $timezone;
    /**
     * The Date's Events
     * @var EventSummary[]
     */
    public array $events;
    /**
     * Multi-day Events that start on Date
     * @var ?EventSummary[]
     */
    #[SerializedName('multiday_starting')]
    public ?array $multidayStarting;
    /**
     * Multi-day Events that are continuing their observance on Date
     * @var ?EventSummary[]
     */
    #[SerializedName('multiday_ongoing')]
    public ?array $multidayOngoing;
}
