<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

/**
 * A painting — oil, tempera, acrylic, watercolour, fresco, etc., where paint is the primary medium.
 *
 * An ArtworkDto (style/movement) over PhysicalObjectDto's material/dimensions/weight.
 */
#[SchemaOrg('Painting')]
class PaintingDto extends ArtworkDto
{
    public static function contentType(): string { return ContentType::PAINTING; }
}
