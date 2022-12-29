<?php

namespace Westy92\HolidayEventApi\Model;

/**
 * Information about an Event image
 */
final class ImageInfo
{
    /**
     * A small image
     */
    public string $small;
    /**
     * A medium image
     */
    public string $medium;
    /**
     * A large image
     */
    public string $large;

    public function __construct(string $small, string $medium, string $large) {
        $this->small = $small;
        $this->medium = $medium;
        $this->large = $large;
    }
}
