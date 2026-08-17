<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

/**
 * DTO for drawn works: pencil, ink, charcoal, watercolour, pastel, etc.
 * Covers any work where drawing is the primary medium.
 *
 * Subclass for more specific drawn types (CartoonDto, SketchDto, etc.)
 */
#[SchemaOrg('Drawing')]
class DrawingDto extends ArtworkDto
{
    public static function contentType(): string
    {
        return ContentType::DRAWING;
    }
}
