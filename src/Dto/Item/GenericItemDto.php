<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

/**
 * Concrete BaseItemDto for subjects whose content type is unknown or mixed.
 * Use when no domain-specific DTO subclass applies.
 */
#[SchemaOrg('CreativeWork')]
final class GenericItemDto extends AbstractWorkDto
{
    public static function contentType(): string
    {
        return 'unknown';
    }
}
