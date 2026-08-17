<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

#[ClassMeta(
    label: 'Document',
    description: 'Generic text-primary item: broadsides, lists, clippings, forms, and any textual work that does not fit a more specific type.',
)]
#[SchemaOrg('ArchiveComponent')]
class DocumentDto extends AbstractWorkDto
{
    public ?string $extent           = null;
    public bool    $hasTranscription = false;

    /** Type/format of this source record, e.g. "Bill of Sale, Invoice, or Receipt", "Census or Register". */
    #[Field(facet: true, filterable: true, group: 'Identity')]
    public ?string $sourceType = null;

    /** Contributing project/dataset that sourced this record, e.g. "SlaveVoyages" -- provenance metadata. */
    #[Field(facet: true, filterable: true, group: 'Provenance')]
    public ?string $project = null;

    public static function contentType(): string { return ContentType::DOCUMENT; }
}
