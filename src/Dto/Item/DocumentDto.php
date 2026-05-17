<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;

#[ClassMeta(
    label: 'Document',
    description: 'Generic text-primary item: broadsides, lists, clippings, forms, and any textual work that does not fit a more specific type.',
)]
class DocumentDto extends BaseItemDto
{
    public ?string $extent           = null;
    public bool    $hasTranscription = false;

    public static function contentType(): string { return ContentType::DOCUMENT; }
}
