<?php

namespace Westy92\HolidayEventApi\Model;

/**
 * Your API plan's current Rate Limit and status. Upgrade to increase these limits.
 */
final class RateLimit
{
    /**
     * The amount of requests allowed this month
     */
    public int $limitMonth;
    /**
     * The amount of requests remaining this month
     */
    public int $remainingMonth;

    public function __construct(int $limitMonth, int $remainingMonth) {
        $this->limitMonth = $limitMonth;
        $this->remainingMonth = $remainingMonth;
    }
}
