<?php

namespace Westy92\HolidayEventApi\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * Information about an Event's Alternate Name
 */
final class AlternateName
{
    /**
     * An Event's Alternate Name
     */
    public string $name;
    /**
     * The first year this Alternate Name was in effect (null implies none or unknown)
     */
    #[SerializedName('first_year')]
    public ?int $firstYear;
    /**
     * The last year this Alternate Name was in effect (null implies none or unknown)
     */
    #[SerializedName('last_year')]
    public ?int $lastYear;

    public function __construct(string $name, ?int $firstYear, ?int $lastYear) {
        $this->name = $name;
        $this->firstYear = $firstYear;
        $this->lastYear = $lastYear;
    }
}
