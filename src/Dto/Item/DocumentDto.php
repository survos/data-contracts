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
    /** Stable local identifier of the containing newspaper issue. */
    #[Field(filterable: true, index: true, group: 'Publication')]
    public ?string $issueId = null;

    /** The containing issue's source PDF, so a viewer can open it at the record's first page (pageStart). */
    public ?string $sourcePdfUrl = null;

    /** Title of the containing issue, for display without a join. */
    public ?string $issueTitle = null;

    /** Extraction tier of a newspaper record, such as block or full. */
    #[Field(filterable: true, facet: true, group: 'Publication')]
    public ?string $articleTier = null;

    /** Source record classification, independent of heuristic kind. */
    #[Field(filterable: true, facet: true, group: 'Publication')]
    public ?string $recordKind = null;

    /** scan (OCR of a page image) or digital (the publisher's own text, markdown). See SourceKind. */
    #[Field(filterable: true, facet: true, group: 'Publication')]
    public ?string $sourceKind = null;

    /**
     * The record's text cut into ordered segments, each anchored to the page blocks it came from.
     *
     * @var list<array<string, mixed>>|null each entry is a {@see \Survos\DataContracts\Dto\Periodical\SegmentDto} array
     */
    public ?array $segments = null;

    /** What decided the record's boundaries: publisher ranges, a model, headline spans or the block heuristic. */
    #[Field(filterable: true, facet: true, group: 'Publication')]
    public ?string $segmentationBasis = null;

    /** Where the OCR came from, e.g. vendor-alto, page-layout. */
    #[Field(filterable: true, facet: true, group: 'Text')]
    public ?string $ocrSource = null;

    /** The model or engine that produced the OCR text. */
    public ?string $ocrModel = null;

    /** First page of the record within its issue, 1-based. */
    #[Field(filterable: true, sortable: true, group: 'Publication')]
    public ?int $pageStart = null;

    /** Last page of the record within its issue, 1-based. */
    public ?int $pageEnd = null;

    /**
     * Images published with a digital article.
     *
     * @var list<array<string, mixed>>|null each entry is an {@see \Survos\DataContracts\Dto\Periodical\ArticleImageDto} array
     */
    public ?array $articleImages = null;

    /** Name of the publisher's archive file a born-digital article came from. */
    public ?string $sourceArchive = null;

    /** When the publisher last revised a born-digital article (ISO 8601). */
    public ?string $modifiedAt = null;

    /** @var list<string>|null people named in the record, as an enrichment pass read them */
    #[Field(filterable: true, facet: true, group: 'Names')]
    public ?array $people = null;

    /** @var list<string>|null places named in the record */
    #[Field(filterable: true, facet: true, group: 'Names')]
    public ?array $places = null;

    /** @var list<string>|null organizations named in the record */
    #[Field(filterable: true, facet: true, group: 'Names')]
    public ?array $organizations = null;

    /** @var array<string, mixed>|null how the record was cut out of its issue; diagnostics for the producer */
    public ?array $extractionEvidence = null;

    public ?string $extent = null;
    public bool $hasTranscription = false;

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

    public static function contentType(): string
    {
        return ContentType::DOCUMENT;
    }
}
