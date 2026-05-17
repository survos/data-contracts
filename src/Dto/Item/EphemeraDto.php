<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;

#[ClassMeta(
    label: 'Ephemera',
    description: 'Printed or manufactured ephemera: tickets, labels, trade cards, broadsides, programmes, menus, and handbills.',
)]
class EphemeraDto extends PhysicalObjectDto
{
    /** Specific ephemera type e.g. "Ticket", "Trade card", "Broadside" */
    public ?string $ephemeraType = null;

    public static function contentType(): string { return ContentType::EPHEMERA; }
}
