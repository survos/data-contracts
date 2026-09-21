<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Attribute\PropertyMeta;
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

    /**
     * Words of text in this record. A newspaper block runs from a one-line notice to a column-long
     * story, and the length is the quickest way to tell them apart, so it is a range facet.
     */
    #[Field(facet: true, filterable: true, sortable: true, group: 'Text')]
    #[PropertyMeta(label: 'Words', description: 'Words of text in the record.', sortable: true, facet: true)]
    public ?int $wordCount = null;

    /**
     * How the page set this block: a headline or running text (paragraph). Read off the layout,
     * so it is reliable; a subhead is not told apart from a headline yet.
     */
    #[Field(facet: true, filterable: true, group: 'Text')]
    #[PropertyMeta(label: 'Type', description: 'Headline or paragraph, as the page set it.', facet: true)]
    public ?string $blockType = null;

    /**
     * What the record is: story, ad, obituary. A heuristic on the page text (ad density, obituary
     * vocabulary), rough on modern papers — a facet to explore with, not a classification to cite.
     */
    #[Field(facet: true, filterable: true, group: 'Text')]
    #[PropertyMeta(label: 'Kind', description: 'Story, ad or obituary — a heuristic, not a review.', facet: true)]
    public ?string $kind = null;

    /** Type/format of this source record, e.g. "Bill of Sale, Invoice, or Receipt", "Census or Register". */
    #[Field(facet: true, filterable: true, group: 'Identity')]
    public ?string $sourceType = null;

    /** Contributing project/dataset that sourced this record, e.g. "SlaveVoyages" -- provenance metadata. */
    #[Field(facet: true, filterable: true, group: 'Provenance')]
    public ?string $project = null;

    public static function contentType(): string { return ContentType::DOCUMENT; }
}
