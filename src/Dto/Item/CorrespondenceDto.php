<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;

#[ClassMeta(
    label: 'Correspondence',
    description: 'Letters, telegrams, memos, and other written communications between identified parties.',
)]
class CorrespondenceDto extends DocumentDto
{
    public ?array  $from     = null;
    public ?array  $to       = null;
    public ?string $sentFrom = null;

    public static function contentType(): string { return ContentType::CORRESPONDENCE; }
}
