<?php

namespace Westy92\HolidayEventApi\Model;

/**
 * Formatted Text
 */
final class RichText
{
    /**
     * Formatted as plain text
     */
    public ?string $text;
    /**
     * Formatted as HTML
     */
    public ?string $html;
    /**
     * Formatted as Markdown
     */
    public ?string $markdown;

    public function __construct(?string $text, ?string $html, ?string $markdown) {
        $this->text = $text;
        $this->html = $html;
        $this->markdown = $markdown;
    }
}
