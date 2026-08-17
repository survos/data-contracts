<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

/**
 * A collection / series — an aggregation of items rather than a single object.
 *
 * Two producers: the "folio of folios" (each row is a whole folio) and aggregators like the
 * Smithsonian that expose collections and series alongside objects. Unlike a generic item, a
 * collection is browsed by its dataset-level descriptors — provider, country, language, size —
 * so those are promoted to facets here. `subjects` (a parent facet) carries the dtoType mix.
 */
#[SchemaOrg('Collection')]
class CollectionDto extends AbstractWorkDto
{
    /** Provider / aggregator (e.g. dc, smith, fortepan) — the "source" to browse collections by. */
    #[Field(facet: true, filterable: true)]
    public ?string $aggregator = null;

    #[Field(facet: true, filterable: true)]
    public ?string $country = null;

    #[Field(facet: true, filterable: true)]
    public ?string $language = null;

    /** Number of items in the collection — a sortable facet (small / large collections). */
    #[Field(facet: true, sortable: true)]
    public ?int $itemCount = null;

    /** Pipeline status of the underlying folio (working / archive / etc.). */
    #[Field(facet: true, filterable: true)]
    public ?string $status = null;

    public static function contentType(): string { return ContentType::COLLECTION; }
}
