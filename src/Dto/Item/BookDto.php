<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Attribute\PropertyMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

#[ClassMeta(
    label: 'Book',
    description: 'Bound volumes: monographs, pamphlets, albums, and catalogues.',
)]
#[SchemaOrg('Book')]
class BookDto extends AbstractWorkDto
{
    public ?string $publisher = null;
    public ?string $pubPlace  = null;
    public ?string $edition   = null;
    public ?string $extent    = null;
    public ?string $isbn      = null;
    #[PropertyMeta(label: 'Has transcription', facet: true)]
    public bool    $hasTranscription = false;

    public static function contentType(): string { return ContentType::BOOK; }
}
