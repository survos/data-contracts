<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto;

use Survos\DataContracts\Metadata\ContentType;
use Survos\DataContracts\Vocabulary\DcTerms;
use Survos\DataContracts\Vocabulary\ItemField;
use Survos\FieldBundle\Attribute\Field;
use Survos\FieldBundle\Attribute\Map;

/**
 * Shared identity fields + class-metadata methods for every entity DTO, regardless of which
 * core it belongs to (obj/doc/event/pla via BaseItemDto, per via BasePersonDto -- and any
 * future core; org is the next one coming). Extracted 2026-08-12 after $id/$sourceUrl/$url/
 * $contentType/$aggregator turned out to be independently duplicated -- with drifting
 * #[Field]/#[PropertyMeta] metadata -- between BaseItemDto and BasePersonDto, which had no
 * common ancestor at all despite needing the exact same identity concepts.
 *
 * The #[Map(source: [...])] alias lists on $id/$sourceUrl are load-bearing for every existing
 * BaseItemDto-descended dataset (objectID, IsShownAt, cite, ResourceURL, ... -- source-specific
 * field name variants BaseItemDto::applyMapAliases() resolves) and are preserved here exactly;
 * reflection on a subclass sees inherited properties' attributes from wherever they're actually
 * declared, so this doesn't break BaseItemDto's alias resolution.
 *
 * Deliberately does NOT carry #[PropertyMeta] (searchable/sortable/facet flags): BaseItemDto and
 * BasePersonDto had drifted to different, incompatible choices for the same fields (e.g.
 * BasePersonDto flagged $aggregator facet:true, BaseItemDto didn't) -- picking a winner here
 * would silently change behavior for whichever side loses. $contentType still facets via
 * FolioFacetFieldResolver::FALLBACK_FACETABLE regardless. Revisit as a deliberate follow-up if
 * losing Person's explicit searchable/sortable id or facetable aggregator turns out to matter.
 */
abstract class AbstractEntityDto
{
    #[Map(source: [ItemField::SOURCE_ID, ItemField::ARK, 'objectID', 'ObjectID', 'codigoDeCatalogacion', 'systemId'])]
    #[Field(group: 'Identity')]
    public ?string $id = null {
        set(mixed $value) => $this->id = $value !== null ? (string) $value : null;
    }

    /** The citable/reader-facing page for this record (e.g. a museum's public object page). */
    #[Map(source: [DcTerms::SOURCE->value, ItemField::CITATION_URL, ItemField::PAGE_URL, 'IsShownAt', 'cite', 'ResourceURL', 'objectURL', 'linkResource', 'frontendUrl', 'recordUrl', 'objectUrl'])]
    #[Field(group: 'Identity')]
    public ?string $sourceUrl = null;

    /**
     * The item's own canonical source URI, DISTINCT from $sourceUrl -- e.g. for a Wikibase-
     * sourced item, the raw wiki page (all statements inspectable) vs $sourceUrl's polished
     * public page. The two can genuinely differ; not every source has both.
     */
    #[Map(source: [ItemField::URL])]
    #[Field(group: 'Identity')]
    public ?string $url = null;

    #[Field(group: 'Identity')]
    public ?string $contentType = null;

    #[Field(group: 'Identity')]
    public ?string $aggregator = null;

    /** The ContentType constant for this DTO class, e.g. ContentType::PERSON, ContentType::PLACE. */
    abstract public static function contentType(): string;

    /** LOC/DCMI URI for the content type. Used as zm ResourceClass and for RDF export. */
    public static function classUri(): ?string
    {
        return ContentType::uri(static::contentType());
    }

    /** Human-readable label for the zm ResourceTemplate. */
    public static function classLabel(): string
    {
        return ucfirst(static::contentType());
    }
}
