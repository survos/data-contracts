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
    const ID          = 'id';
    const TITLE       = 'title';
    const DESCRIPTION = 'description';
    const PHYSICAL_DESCRIPTION = 'physicalDescription';
    const CONTEXT_DESCRIPTION  = 'contextDescription';
    const NOTES       = 'notes';
    const TYPE        = 'type';
    const GENRE_SPECIFIC = 'genreSpecific';
    const GENRE_BASIC    = 'genreBasic';
    const KEYWORDS    = 'keywords';
    const CREATOR     = 'creator';
    const URL         = 'url';
    const CITATION    = 'citation';
    const CITATION_URL = 'citationUrl';
    const LICENSE     = 'license';
    const RIGHTS      = 'rights';
    const SOURCE      = 'source';

    const SOURCE_ID    = 'sourceId';
    const CONTENT_TYPE = 'contentType';
    const AGGREGATOR   = 'aggregator';
    const ARK          = 'ark';

    const THUMBNAIL_URL   = 'thumbnailUrl';
    const LARGE_IMAGE_URL = 'largeImageUrl';
    const IIIF_BASE       = 'iiifBase';
    const IIIF_MANIFEST   = 'iiifManifest';
    const IIIF_INFO       = 'iiifInfo';

    #[VocabTerm(
        label: 'Search summary',
        aiHint: 'Deterministic BM25-friendly text assembled from normalized catalogue metadata.',
    )]
    const SEARCH_SUMMARY = 'searchSummary';

    #[VocabTerm(
        label: 'Dense summary',
        aiHint: 'Write a single retrieval-optimised paragraph of ≤ 400 characters. Pack in who, what, when, where, and material/technique.',
    )]
    const DENSE_SUMMARY = 'ai:denseSummary';

    const PAGE_URL = 'pageUrl';
    const DATE     = 'date';
    const LANGUAGE = 'language';

    const LATITUDE  = 'schema:latitude';
    const LONGITUDE = 'schema:longitude';
    const COUNTRY   = 'country';
    const CITY      = 'city';
}
