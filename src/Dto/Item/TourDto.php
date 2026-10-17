<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;

/**
 * A tour — walking tour, audio tour, curated trail — as a first-class, queryable folio row (one
 * row per tour). Its stops are NOT rows of their own: a tour links straight to the things it visits
 * with `tour —has_stop {ordinal}→ obj` (reverse `stop_of`), so ordering and any per-stop framing
 * (audio, subtitle) live on the Link's extras, and one object can sit on several tours.
 */
#[ClassMeta(
    label: 'Tour',
    description: 'A narrative tour composed of ordered stops — walking tours, audio tours, curated trails.',
)]
class TourDto extends BaseItemDto
{
    /** Number of stops in this tour. Same convention as BaseItemDto::$imageCount/$pageCount. */
    #[Field(group: 'Content')]
    public ?int $stopCount = null;

    public static function contentType(): string
    {
        return ContentType::TOUR;
    }
}
