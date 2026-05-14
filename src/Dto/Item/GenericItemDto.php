<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

/**
 * Concrete BaseItemDto for subjects whose content type is unknown or mixed.
 * Use when no domain-specific DTO subclass applies.
 */
final class GenericItemDto extends BaseItemDto
{
    public static function contentType(): string
    {
        return 'unknown';
    }
}
