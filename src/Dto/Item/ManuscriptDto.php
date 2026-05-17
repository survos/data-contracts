<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;

#[ClassMeta(
    label: 'Manuscript',
    description: 'Handwritten or typed manuscripts: diaries, journals, ledgers, legal documents, and field notes.',
)]
class ManuscriptDto extends DocumentDto
{
    public static function contentType(): string { return ContentType::MANUSCRIPT; }
}
