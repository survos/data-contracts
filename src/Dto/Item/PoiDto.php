<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

/**
 * A named point of interest — a church, school, shop, park, or similar OSM-derived feature — as
 * opposed to {@see PlaceDto}, which is a described place (park, historic site) with its own
 * curatorial content. A POI is typically machine-resolved (survos/poi-bundle, Nominatim search
 * scoped to a known city) rather than hand-curated, so it carries provenance ($osmType/$osmId)
 * and the literal source text it was matched from ($matchedQuery) instead of curatorial prose.
 * $title/$description/$latitude/$longitude/$city/$state/$country all reuse BaseItemDto directly.
 */
#[ClassMeta(
    label: 'Point of interest',
    description: 'A church, school, shop, park, or similar named place resolved from OpenStreetMap.',
)]
#[SchemaOrg('TouristAttraction')]
class PoiDto extends BaseItemDto
{
    /** OSM class/type joined as "class/type", e.g. "amenity/place_of_worship", "amenity/school",
     *  "shop/supermarket". A facet — this is the POI-equivalent of $subjects/$genreBasic. */
    #[Field(facet: true, filterable: true, group: 'Identity')]
    public ?string $category = null;

    /** OSM element kind: node | way | relation. */
    #[Field(group: 'Identity')]
    public ?string $osmType = null;

    /** OSM numeric id within $osmType — together with $osmType, the stable provenance key. */
    #[Field(group: 'Identity')]
    public ?int $osmId = null;

    /**
     * The literal free-text tag/caption this POI was matched from (e.g. "Catedral de Ancud"),
     * kept separate from $title (the OSM display name) so a mismatch between what the source
     * called it and what OSM calls it stays visible instead of one silently overwriting the other.
     */
    #[Field(group: 'Identity')]
    public ?string $matchedQuery = null;

    /**
     * GeoNames geonameId of the resolved parent city this POI was searched within — the join
     * point back to survos/geonames-bundle's hierarchy, without this bundle depending on it.
     */
    #[Field(group: 'Geography')]
    public ?int $parentGeonameId = null;

    public static function contentType(): string { return ContentType::POI; }
}
