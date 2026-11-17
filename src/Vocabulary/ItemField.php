<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

use Survos\DataContracts\Attribute\VocabTerm;

/**
 * Canonical field keys for normalized item records — CORE fields only.
 * Values are camelCase, matching PHP DTO property names and JSON payload keys.
 *
 * NOT for museum authority vocabulary. Those live in MuseumVocab with 3-letter
 * codes (cul, med, mat, tec, pla, per, obj, org).
 *
 * DC output predicates (dcterms:title, …) → DcTerms enum
 */
interface ItemField
{
    public const ID = 'id';

    // Structured burial-record fields; person life dates remain on PersonDto.
    public const CEMETERY_NAME = 'cemeteryName';
    public const INTERMENT_DATE = 'intermentDate';
    public const INTERMENT_TIME = 'intermentTime';
    public const SECTION = 'section';
    public const GRAVE = 'grave';
    public const LOCATION_ID = 'locationId';
    public const SERVICE_BRANCH = 'serviceBranch';
    public const MILITARY_RANK = 'militaryRank';
    public const RELATIONSHIP = 'relationship';
    public const WARS = 'wars';
    public const FAITH = 'faith';
    public const RELIGIOUS_SYMBOLS = 'religiousSymbols';
    public const INSCRIPTION_CLAIMS = 'inscriptionClaims';
    /** List of AuthorityReference values; resolved by the host, never embedded authority records. */
    public const AUTHORITIES = 'authorities';
    public const TITLE = 'title';
    /** Display name of a person, organization or place. */
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const PHYSICAL_DESCRIPTION = 'physicalDescription';
    public const CONTEXT_DESCRIPTION = 'contextDescription';
    public const NOTES = 'notes';
    public const TYPE = 'type';
    public const GENRE_SPECIFIC = 'genreSpecific';
    public const GENRE_BASIC = 'genreBasic';
    public const TYPE_OF_RESOURCE = 'typeOfResource';
    public const KEYWORDS = 'keywords';
    public const SUBJECTS = 'subjects';
    public const SUBJECTS_GEOGRAPHIC = 'subjectsGeographic';
    public const CREATOR = 'creators'; // the normalized field is the array `creators` (BaseItemDto::$creators)
    public const COLLECTIONS = 'collections';
    public const URL = 'url';
    public const PDF_URL = 'pdfUrl';
    public const CITATION = 'citation';
    public const CITATION_URL = 'citationUrl';
    public const PUBLIC_URL = 'publicUrl';
    public const ADMIN_URL = 'adminUrl';
    public const SOURCE_API_URL = 'sourceApiUrl';
    public const LICENSE = 'license';
    public const RIGHTS = 'rights';
    public const SOURCE = 'source';

    public const SOURCE_ID = 'sourceId';
    public const CONTENT_TYPE = 'contentType';
    public const AGGREGATOR = 'aggregator';
    public const ARK = 'ark';

    public const THUMBNAIL_URL = 'thumbnailUrl';
    public const LARGE_IMAGE_URL = 'largeImageUrl';
    /** A provider-declared rendition big enough for every display preset (≥ ~700px), used as the
     *  imgproxy source instead of a slow full-size original. Opt-in: thumbnailUrl is often 200-300px. */
    public const DISPLAY_IMAGE_URL = 'displayImageUrl';
    public const IIIF_BASE = 'iiifBase';
    public const IIIF_MANIFEST = 'iiifManifest';
    public const IIIF_INFO = 'iiifInfo';

    /**
     * Ordered list of a row's SOURCE images (each: image url + optional thumb/caption), the input
     * a normalizer hands to PageEmitter — which turns each entry into one page.jsonl row. These are
     * inputs, NOT pages (cf. PAGE_URL, a single rendered page). One shared key so every provider
     * that emits multiple images per row uses the same contract.
     */
    public const IMAGES = 'images';
    public const IMAGE_COUNT = 'imageCount';

    #[VocabTerm(
        label: 'Search summary',
        aiHint: 'Deterministic BM25-friendly text assembled from normalized catalogue metadata.',
    )]
    public const SEARCH_SUMMARY = 'searchSummary';

    #[VocabTerm(
        label: 'Dense summary',
        aiHint: 'Write a single retrieval-optimised paragraph of ≤ 400 characters. Pack in who, what, when, where, and material/technique.',
    )]
    public const DENSE_SUMMARY = 'ai:denseSummary';
    /** ai:observationProse — detailed AI visual observation (markdown prose). */
    public const OBSERVATION_PROSE = 'ai:observationProse';
    /** ai:caption — short AI-generated caption/title. */
    public const CAPTION = 'ai:caption';

    public const PAGE_URL = 'pageUrl';
    public const DATE = 'date';
    public const YEAR = 'year';
    public const LANGUAGE = 'language';

    public const LATITUDE = 'schema:latitude';
    public const LONGITUDE = 'schema:longitude';
    public const COUNTRY = 'country';
    public const CITY = 'city';

    /** Single primary state/province — first-class display value (e.g. the "City, State, Country"
     *  line in PhotoGrid). A record touching more than one still sets this to the primary one. */
    public const STATE = 'state';
    /**
     * Every state/province a record touches, e.g. a National Park spanning two states. A dataset
     * whose source is naturally single-valued should just set STATE; one whose source can return
     * multiple (like NPS) sets both — STATE as the primary/display value, STATES as the full facet.
     */
    public const STATES = 'states';

    // ── Periodicals: one constant per field, value = the DTO property name ──────────────────────────
    public const ISSUE_ID = 'issueId';
    public const ISSUE_TIER = 'issueTier';
    public const ISSUE_KIND = 'issueKind';
    public const ISSUE_NUMBER = 'issueNumber';
    public const ARTICLE_TIER = 'articleTier';
    public const RECORD_KIND = 'recordKind';
    public const BLOCK_TYPE = 'blockType';
    public const SOURCE_KIND = 'sourceKind';
    public const SEGMENTS = 'segments';
    public const SEGMENTATION_BASIS = 'segmentationBasis';
    public const OCR_TEXT = 'ocrText';
    public const OCR_SOURCE = 'ocrSource';
    public const OCR_MODEL = 'ocrModel';
    public const PAGE_START = 'pageStart';
    public const PAGE_END = 'pageEnd';
    public const ARTICLE_IMAGES = 'articleImages';
    public const IA_IDENTIFIER = 'iaIdentifier';
    public const SOURCE_PDF_URL = 'sourcePdfUrl';
    public const PUBLICATION_TITLE = 'publicationTitle';
    public const HOSTING_ARCHIVE = 'hostingArchive';
    public const ISSUE_TITLE = 'issueTitle';
    public const SOURCE_LABEL = 'sourceLabel';
    public const PAGE_URL_TEMPLATE = 'pageUrlTemplate';
    public const GROUPING_MARKING = 'groupingMarking';
    public const GROUPING_REVISION = 'groupingRevision';
    public const GROUPING_SCHEMA_VERSION = 'groupingSchemaVersion';
    public const GROUPING_SOURCE_HASH = 'groupingSourceHash';
    public const GROUPING_ALGORITHM_HASH = 'groupingAlgorithmHash';
    public const GROUPING_INPUT_HASH = 'groupingInputHash';
    public const GROUPING_UNAVAILABLE = 'groupingUnavailable';
    public const EXTRACTION_EVIDENCE = 'extractionEvidence';
    public const PEOPLE = 'people';
    public const SOURCE_ARCHIVE = 'sourceArchive';
    public const MODIFIED_AT = 'modifiedAt';
    public const PLACES = 'places';
    public const ORGANIZATIONS = 'organizations';
}
