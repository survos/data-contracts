<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

#[SchemaOrg('Photograph')]
class PhotographDto extends AbstractWorkDto
{
    /** Who contributed the photo to the archive (e.g. the Fortepan donor). A sidebar facet. */
    #[Field(facet: true, filterable: true, group: 'Agents & Collections')]
    public ?string $donor = null;

    /** Photographic process e.g. "Gelatin silver print", "Albumen print" */
    #[Field(group: 'Description')]
    public ?string $process = null;

    /** Physical format e.g. "Cabinet card", "35mm slide" */
    #[Field(group: 'Description')]
    public ?string $format = null;

    /** Physical dimensions */
    #[Field(group: 'Description')]
    public ?string $dimensions = null;

    /** Genre specific terms (TGM) e.g. "Portrait photographs", "Architectural photographs". A facet. */
    #[Field(facet: true, filterable: true, group: 'Subjects & Genre')]
    public ?array $genreSpecific = null;

    public static function contentType(): string { return ContentType::PHOTOGRAPH; }
}
