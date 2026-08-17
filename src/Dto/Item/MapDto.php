<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

#[SchemaOrg('Map')]
class MapDto extends AbstractWorkDto
{
    public ?string $scale       = null;
    public ?string $projection  = null;
    public ?string $pubPlace    = null;
    public ?string $publisher   = null;
    public ?string $dimensions  = null;

    public static function contentType(): string { return ContentType::MAP; }
}
