<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

#[SchemaOrg('Newspaper')]
class NewspaperDto extends AbstractWorkDto
{
    /** Distinguishes ordinary issues from annual indexes. */
    public ?string $issueKind = null;

    /** Declared processing receipt, including stage and review state. */
    public ?array $issueProcessing = null;

    /** basic (page items and blocks) or enhanced (published stories), declared per issue. See IssueTier. */
    #[Field(filterable: true, facet: true, group: 'Publication')]
    public ?string $issueTier = null;

    /** Internet Archive identifier of the issue, when its scans are served from there. */
    public ?string $iaIdentifier = null;

    /** The issue's source PDF, kept apart from pdfUrl so a reader shows the page images instead of a PDF frame. */
    public ?string $sourcePdfUrl = null;

    /** Which state archive hosts the issue, e.g. pennsylvania, oregon (Open ONI sites). */
    #[Field(filterable: true, facet: true, group: 'Availability')]
    public ?string $hostingArchive = null;

    /** Title of the publication the issue belongs to, as the source names it. */
    public ?string $publicationTitle = null;

    /** Where the issue's OCR came from, e.g. loc-alto, vendor-alto. */
    #[Field(filterable: true, facet: true, group: 'Text')]
    public ?string $ocrSource = null;

    /** Name of the place a reader is sent to for the original, e.g. "Virginia Chronicle". */
    public ?string $sourceLabel = null;

    /** sprintf template for the source's page URL; %d is the 1-based page number. */
    public ?string $pageUrlTemplate = null;

    /** Workflow place of the issue's text-sheet grouping, e.g. ready, grouped. */
    #[Field(filterable: true, facet: true, group: 'Publication')]
    public ?string $groupingMarking = null;

    /** Revision of the saved grouping; changes each time the issue is regrouped. */
    public ?string $groupingRevision = null;

    /** Version of the grouping document the group rows were written from. */
    public ?int $groupingSchemaVersion = null;

    /** Fingerprint of the page layouts the grouping was computed from. */
    public ?string $groupingSourceHash = null;

    /** Fingerprint of the grouping code that produced the groups. */
    public ?string $groupingAlgorithmHash = null;

    /** Fingerprint of everything the grouper was given (layouts and article records). */
    public ?string $groupingInputHash = null;

    /** Why the issue has no grouping, e.g. no-pages, incomplete-page-layout; null when it is grouped. */
    public ?string $groupingUnavailable = null;

    public ?string $publisher = null;
    public ?string $pubPlace = null;
    public ?string $volume = null;
    public ?string $issueNumber = null;
    public ?string $edition = null;
    /** Has searchable full-text pages */
    public bool $hasTranscription = false;

    /**
     * Where this title can be read online, e.g. chronicling-america, virginia-chronicle; empty for
     * a title known only from a directory. A title-level fact set by catalog surveys (harvest's
     * survey:newspapers); issue and page rows leave it null.
     *
     * @var list<string>|null
     */
    #[Field(facet: true, filterable: true, group: 'Availability', order: 1)]
    public ?array $digitizedIn = null;

    /**
     * Where to read it, keyed like digitizedIn: ['chronicling-america' => 'https://www.loc.gov/item/…/'].
     *
     * @var array<string, string>|null
     */
    public ?array $digitizedUrls = null;

    /** Publication frequency, grouped for faceting: Daily, Weekly, Semiweekly, Monthly, … */
    #[Field(facet: true, filterable: true, group: 'Publication')]
    public ?string $frequency = null;

    /** The catalog's own wording, e.g. "Daily (except Sun.)"; kept when it says more than the group. */
    public ?string $frequencyNote = null;

    /** Still publishing, per the directory (an end year of 9999). Null when the source doesn't say. */
    #[Field(facet: true, filterable: true, group: 'Publication')]
    public ?bool $ongoing = null;

    /** Issues held online, where a source counts them (Chronicling America does). */
    public ?int $issueCount = null;

    /** @var list<string>|null */
    public ?array $oclc = null;

    /** @var list<string>|null */
    public ?array $lccn = null;

    public static function contentType(): string
    {
        return ContentType::NEWSPAPER;
    }
}
