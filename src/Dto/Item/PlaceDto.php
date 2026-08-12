<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;

/**
 * A described PLACE — a park, site, or similar place-core row (Core::PLACE, 'pla'), as opposed to
 * a described OBJECT/document/artifact. First real user: App\Singleton\Nps (musdig#26), a National
 * Park unit — these fields are place-generic, not NPS-specific, so any future place-oriented
 * provider (a museum campus, a historic site, a trail) gets a real home for them instead of
 * falling into $unmapped/extras.
 */
class PlaceDto extends BaseItemDto
{
    /**
     * Type/category of place, e.g. "Plantation, Estate, or Ranch", "Port", "County or Parish" --
     * a real, high-value facet for browsing places (what KIND of place is this?), distinct from
     * $activities (what can you DO here?). First real source: Enslaved.org's Wikibase dump
     * (App\Aggregator\Provider\WikibaseProvider in harvest, EntityVocab::PLACE_TYPE).
     */
    #[Field(facet: true, filterable: true, group: 'Identity')]
    public ?string $placeType = null;

    /** Typical/seasonal weather conditions a visitor should expect. Free text, not a facet — highly
     *  source-specific prose, not a normalized value worth faceting on. */
    #[Field(group: 'Visiting')]
    public ?string $weatherInfo = null;

    /** How to get there — driving directions, nearest airport, etc. Free text. */
    #[Field(group: 'Visiting')]
    public ?string $directionsInfo = null;

    /** Link to a fuller directions page on the source's own site. */
    #[Field(group: 'Visiting')]
    public ?string $directionsUrl = null;

    /**
     * Things a visitor can do here (hiking, camping, guided tours, …) — a facet, distinct from
     * $subjects (topical/thematic keywords, e.g. "World War II", "Civil Rights"): activities are
     * what you can DO at the place, subjects are what the place is ABOUT.
     *
     * @var list<string>|null
     */
    #[Field(facet: true, filterable: true, group: 'Visiting')]
    public ?array $activities = null;

    public static function contentType(): string { return ContentType::PLACE; }
}
