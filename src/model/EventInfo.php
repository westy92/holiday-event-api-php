<?php

namespace Westy92\HolidayEventApi\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;
/**
 * Information about an Event
 */
final class EventInfo extends EventSummary
{
    /**
     * Whether this Event is unsafe for children or viewing at work
     */
    public bool $adult;
    /**
     * The Event's Alternate Names
     * @var AlternateName[]
     */
    #[SerializedName('alternate_names')]
    public array $alternateNames;
    /**
     * The Event's hashtags
     * @var ?string[]
     */
    public ?array $hashtags;
    /**
     * The Event's images
     */
    public ?ImageInfo $image;
    /**
     * The Event's sources
     * @var ?string[]
     */
    public ?array $sources;
    /**
     * The Event's description
     */
    public ?RichText $description;
    /**
     * How to observe the Event
     */
    #[SerializedName('how_to_observe')]
    public ?RichText $howToObserve;
    /**
     * Patterns defining when the Event is observed
     * @var ?Pattern[]
     */
    public ?array $patterns;
    /**
     * The Event's founders
     * @var ?FounderInfo[]
     */
    public ?array $founders;
    /**
     * The Event Occurrences (when it occurs)
     * @var ?Occurrence[]
     */
    public ?array $occurrences;

    /**
     * @param AlternateName[] $alternateNames
     * @param string[] $hashtags
     * @param ?string[] $sources
     * @param ?Pattern[] $patterns
     * @param ?FounderInfo[] $founders
     * @param ?Occurrence[] $occurrences
     */
    public function __construct(bool $adult, array $alternateNames, ?array $hashtags, ?ImageInfo $image, ?array $sources, ?RichText $description, ?RichText $howToObserve, ?array $patterns, ?array $founders, ?array $occurrences, string $id, string $name, string $url) {
        $this->adult = $adult;
        $this->alternateNames = $alternateNames;
        $this->hashtags = $hashtags;
        $this->image = $image;
        $this->sources = $sources;
        $this->description = $description;
        $this->howToObserve = $howToObserve;
        $this->patterns = $patterns;
        $this->founders = $founders;
        $this->occurrences = $occurrences;
        parent::__construct(id: $id, name: $name, url: $url);
    }
}
