<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;

/**
 * A source record of an interment, not the person or the physical monument.
 * Link to per using folio links; photographs remain evidence pages on this record.
 * Birth/death dates belong to PersonDto. Shared grave location alone is not kinship.
 * FTM interchange: Person for people, Event for interments; source-specific grave
 * identifiers remain in this richer contract rather than invented FTM properties.
 */
#[ClassMeta(label: 'Burial record', description: 'A documented interment with cemetery location and source-recorded service details.')]
final class BurialRecordDto extends BaseItemDto
{
    #[Field(facet: true, filterable: true, group: 'Burial')]
    public ?string $cemeteryName = null;

    /** ISO date, preserving source precision; no timezone is inferred. */
    #[Field(sortable: true, group: 'Burial')]
    public ?string $intermentDate = null;

    /** Local time only when actually supplied; placeholder midnight may be omitted. */
    #[Field(group: 'Burial')]
    public ?string $intermentTime = null;

    #[Field(facet: true, filterable: true, group: 'Location')]
    public ?string $section = null;

    /** Cemetery-scoped identifier: retain letters, punctuation and leading zeros. */
    #[Field(group: 'Location')]
    public ?string $grave = null;

    #[Field(group: 'Location')]
    public ?string $locationId = null;

    #[Field(facet: true, filterable: true, group: 'Military service')]
    public ?string $serviceBranch = null;

    #[Field(facet: true, filterable: true, group: 'Military service')]
    public ?string $militaryRank = null;

    /** Source relationship/category (e.g. Wife); does not identify a relative by itself. */
    #[Field(facet: true, filterable: true, group: 'Burial')]
    public ?string $relationship = null;

    /** Named conflicts explicitly recorded or read from the inscription; not inferred from life dates. @var list<string>|null */
    #[Field(facet: true, filterable: true, group: 'Inscription')]
    public ?array $wars = null;

    /** Faith explicitly stated by a source/inscription, not inferred solely from an emblem. @var list<string>|null */
    #[Field(facet: true, filterable: true, group: 'Inscription')]
    public ?array $faith = null;

    /** Observed emblems such as a cross or Star of David, without asserting personal belief. @var list<string>|null */
    #[Field(facet: true, filterable: true, group: 'Inscription')]
    public ?array $religiousSymbols = null;

    /**
     * Image-derived assertions use existing ClaimDTO wire values (predicate/value/confidence/basis).
     * Basis must identify the photo and supporting inscription or visual observation.
     * Source-only imports leave this unset; tags are projections, not unqualified source facts.
     *
     * @var list<array{predicate:string,value:mixed,confidence?:int,basis?:string}>|null
     */
    public ?array $inscriptionClaims = null;

    public static function contentType(): string
    {
        return ContentType::BURIAL_RECORD;
    }
}
