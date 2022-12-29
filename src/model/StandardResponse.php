<?php

namespace Westy92\HolidayEventApi\Model;

/**
 * The API's standard response
 */
abstract class StandardResponse
{
    /**
     * The API plan's current rate limit and status
     */
    public RateLimit $rateLimit;
}
