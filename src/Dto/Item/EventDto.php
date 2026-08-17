<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

/**
 * A described EVENT — a sale, voyage, birth, or similar event-core row (Core::EVENT, 'event'), as
 * opposed to a described OBJECT/document/artifact. First real user: App\Aggregator\Provider\
 * WikibaseProvider (harvest, survos-sites/musdig#35) — Enslaved.org's Wikibase has no generic
 * "object" entity at all, just Person/Event/Place/Source, which is what exposed this gap.
 *
 * Deliberately surface-level: $date (inherited from BaseItemDto) + $eventType. NOT modeling
 * event-as-attribute-vs-entity or real participant rosters here — that's real, separate,
 * deferred work (see survos-sites/musdig#35 follow-up discussion).
 */
#[SchemaOrg('Event')]
class EventDto extends BaseItemDto
{
    /**
     * Type/category of event, e.g. "Birth", "Sale or Transfer", "Voyage" — a real, high-value
     * facet for browsing events (what KIND of event is this?).
     */
    #[Field(facet: true, filterable: true, group: 'Identity')]
    public ?string $eventType = null;

    public static function contentType(): string { return ContentType::EVENT; }
}
