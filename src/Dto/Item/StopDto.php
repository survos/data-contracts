<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;

/**
 * A tour stop — one narrative unit within a story (see StoryDto), as a first-class, queryable
 * folio row. Ordinal (position within its story) deliberately does NOT live here: the same stop
 * could in principle appear in more than one story with a different position each time, so
 * ordering is a property of the story<->stop relationship, not the stop itself — it lives on the
 * `has_stop` Link's extras instead (see md's CuratescapeProvider::storyNormalize(), link.jsonl).
 * $title/$description/$latitude/$longitude/$thumbnailUrl all reuse BaseItemDto directly; a stop
 * needs nothing beyond what every other row already has, except $subtitle.
 */
#[ClassMeta(
    label: 'Stop',
    description: 'One stop within a tour — a location, object, or narrative beat along a story.',
)]
class StopDto extends BaseItemDto
{
    /** Tour-specific framing distinct from the stop's own title/description — e.g. how this
     *  particular story introduces an object it shares with other stops/stories. */
    #[Field(group: 'Content')]
    public ?string $subtitle = null;

    public static function contentType(): string { return ContentType::STOP; }
}
