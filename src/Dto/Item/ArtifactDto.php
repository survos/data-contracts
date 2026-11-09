<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Attribute\PropertyMeta;
use Survos\DataContracts\Metadata\ContentType;

#[ClassMeta(
    label: 'Artifact',
    description: 'Three-dimensional physical objects and artifacts that do not fit a more specific type: tools, vessels, garments, specimens, etc.',
)]
class ArtifactDto extends PhysicalObjectDto
{
    public ?string $objectType = null;

    #[PropertyMeta(label: 'Actor', facet: true)]
    public ?string $actor = null;
    #[PropertyMeta(label: 'Character', facet: true)]
    public ?string $character = null;
    #[PropertyMeta(label: 'Film or show', facet: true)]
    public ?string $productionTitle = null;

    #[PropertyMeta(label: 'Costume designer', facet: true)]
    public ?string $costumeDesigner = null;
    #[PropertyMeta(label: 'Studio', facet: true)]
    public ?string $studio = null;
    /** Museum catalog's garment classification; preserve the source wording. */
    #[PropertyMeta(label: 'Sex (catalog)', facet: true)]
    public ?string $costumeSex = null;

    public static function contentType(): string
    {
        return ContentType::OBJECT;
    }
}
