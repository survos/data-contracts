<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;

/**
 * A painting — oil, tempera, acrylic, watercolour, fresco, etc., where paint is the primary medium.
 *
 * An ArtworkDto (style/movement) over PhysicalObjectDto's material/dimensions/weight.
 */
class PaintingDto extends ArtworkDto
{
    public static function contentType(): string { return ContentType::PAINTING; }
}
