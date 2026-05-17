<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;

/**
 * DTO for cartoons and caricatures — drawings made for satirical,
 * editorial, or humorous purposes.
 *
 * Nearly all fields live in PhysicalObjectDto (mat, dimensions, etc.)
 * or BaseItemDto (title, description, thumbnailUrl, iiifBase, etc.).
 * This class exists primarily as a content-type marker so the pipeline
 * can route to the right DTO, apply the right AI prompts, and populate
 * the right Omeka-S resource template.
 */
final class CartoonDto extends DrawingDto
{

    /** Object type classification from source, e.g. ['Cartoons', 'Caricatures'] */
    public ?array $type = null;

    /** Descriptive notes beyond the main description */
    public ?string $notes = null;
}
