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
    public const TITLE = 'title';
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
}
