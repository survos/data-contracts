<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;

/**
 * A tour/walking-tour/story — the story-contract "story.v1" document as a first-class, queryable
 * folio row (one row per story), not a special-cased blob excluded from ingest. The full nested
 * document (blocks/bookmarks/resources) stays the canonical source of truth, read server-side via
 * exhibit-bundle's StoryFinder; this row is a derived, queryable projection of it — same data,
 * different access path. See ~/sites/rut/docs/PLAN-folio-data-layer.md.
 */
#[ClassMeta(
    label: 'Story',
    description: 'A narrative tour composed of ordered stops — walking tours, driving tours, curated trails.',
)]
class StoryDto extends BaseItemDto
{
    /** Number of stops in this story. Same convention as BaseItemDto::$imageCount/$pageCount. */
    #[Field(group: 'Content')]
    public ?int $stopCount = null;

    public static function contentType(): string { return ContentType::STORY; }
}
