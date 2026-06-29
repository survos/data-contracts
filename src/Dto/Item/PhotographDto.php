<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;

class PhotographDto extends BaseItemDto
{
    /** Who contributed the photo to the archive (e.g. the Fortepan donor). A sidebar facet. */
    #[Field(facet: true, filterable: true)]
    public ?string $donor = null;

    /** Photographic process e.g. "Gelatin silver print", "Albumen print" */
    public ?string $process = null;

    /** Physical format e.g. "Cabinet card", "35mm slide" */
    public ?string $format = null;

    /** Physical dimensions */
    public ?string $dimensions = null;

    /** Genre specific terms (TGM) e.g. "Portrait photographs", "Architectural photographs". A facet. */
    #[Field(facet: true, filterable: true)]
    public ?array $genreSpecific = null;

    public static function contentType(): string { return ContentType::PHOTOGRAPH; }
}
