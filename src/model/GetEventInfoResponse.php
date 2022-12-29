<?php

namespace Westy92\HolidayEventApi\Model;
/**
 * The Response returned by getEventInfo
 */
final class GetEventInfoResponse extends StandardResponse
{
    /**
     * The Event Info
     */
    public EventInfo $event;

    public function __construct(EventInfo $event, ?RateLimit $rateLimit = null) {
        $this->event = $event;
        parent::__construct($rateLimit);
    }
}
