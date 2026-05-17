<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;

#[ClassMeta(
    label: 'Artifact',
    description: 'Three-dimensional physical objects and artifacts that do not fit a more specific type: tools, vessels, garments, specimens, etc.',
)]
class ArtifactDto extends PhysicalObjectDto
{
    public ?string $objectType = null;

    public static function contentType(): string { return ContentType::OBJECT; }
}
