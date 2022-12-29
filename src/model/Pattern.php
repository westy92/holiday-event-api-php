<?php

namespace Westy92\HolidayEventApi\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * Information about an Event's Pattern
 */
final class Pattern
{
    /**
     * The first year this event is observed (null implies none or unknown)
     */
    #[SerializedName('first_year')]
    public ?int $firstYear;
    /**
     * The last year this event is observed (null implies none or unknown)
     */
    #[SerializedName('last_year')]
    public ?int $lastYear;
    /**
     * A description of how this event is observed (formatted as plain text)
     */
    public string $observed;
    /**
     * A description of how this event is observed (formatted as HTML)
     */
    #[SerializedName('observed_html')]
    public string $observedHtml;
    /**
     * A description of how this event is observed (formatted as Markdown)
     */
    #[SerializedName('observed_markdown')]
    public string $observedMarkdown;
    /**
     * For how many days this event is celebrated
     */
    public int $length;

    public function __construct(?int $firstYear, ?int $lastYear, string $observed, string $observedHtml, string $observedMarkdown, int $length) {
        $this->firstYear = $firstYear;
        $this->lastYear = $lastYear;
        $this->observed = $observed;
        $this->observedHtml = $observedHtml;
        $this->observedMarkdown = $observedMarkdown;
        $this->length = $length;
    }
}
