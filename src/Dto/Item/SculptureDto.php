<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;

/**
 * A sculpture — a three-dimensional physical artwork (statue, bust, relief, statuette, …).
 *
 * An artwork (style/movement via ArtworkDto) with the structured material / dimensions / weight
 * records from PhysicalObjectDto. Promoted from generic ArtifactDto so sculptures are categorized
 * and facetable; sculpture-specific fields (cast/edition, foundry, patina) can be added as needed.
 */
class SculptureDto extends ArtworkDto
{
    public static function contentType(): string { return ContentType::SCULPTURE; }
}
