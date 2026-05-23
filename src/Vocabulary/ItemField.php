<?php
declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

use Survos\DataContracts\Attribute\VocabTerm;

/**
 * Canonical field keys for normalized item records — CORE fields only.
 *
 * Use ItemField for: id, title, description, language, date, url, content_type,
 * genre_specific, thumbnail_url, large_image_url, and other record-level fields.
 * Values are lowercase_snake_case strings.
 *
 * NOT for museum authority vocabulary. Those live in MuseumVocab with 3-letter
 * codes (cul, med, mat, tec, pla, per, obj, org). When writing a normalize
 * listener, use MuseumVocab::MEDIUM not 'medium', MuseumVocab::CULTURE not
 * 'culture', etc.
 *
 * Rule of thumb:
 *   "What TYPE is this object?" → ItemField::GENRE_SPECIFIC, ItemField::TYPE
 *   "What AUTHORITY controlled vocab applies?" → MuseumVocab (cul, med, per, …)
 *
 * DC output predicates (dcterms:title, …) → DcTerms enum
 */
interface ItemField
{
    // ── Core descriptive (plain names — common source field names) ────────────
    const ID          = 'id';
    const TITLE       = 'title';
    const DESCRIPTION = 'description';

    /** Detailed physical description: form, materials, markings, condition.
     *  May come from the source catalog or from AI vision (pass 1). */
    const PHYSICAL_DESCRIPTION = 'physical_description';

    /** AI pass 2: physical observation + provenance + cultural context → researcher narrative. */
    const CONTEXT_DESCRIPTION  = 'context_description';

    /** Supplementary notes beyond the main description. */
    const NOTES       = 'notes';

    /** Generic object-type classification (source vocabulary varies per dataset). */
    const TYPE        = 'type';
    const GENRE_SPECIFIC = 'genre_specific';
    const GENRE_BASIC    = 'genre_basic';

    /** Generic keyword list for non-museum datasets; feeds MuseumVocab::SUBJECT bucket. */
    const KEYWORDS    = 'keywords';

    /** Dublin Core creator/author; feeds MuseumVocab::PERSON bucket. */
    const CREATOR     = 'creator';

    const URL         = 'url';
    const CITATION    = 'citation';
    const CITATION_URL = 'citation_url';
    const LICENSE     = 'license';
    const RIGHTS      = 'rights';
    const SOURCE      = 'source';

    // ── Identity / provenance ─────────────────────────────────────────────────
    const SOURCE_ID    = 'source_id';
    const CONTENT_TYPE = 'content_type';
    const AGGREGATOR   = 'aggregator';
    /** Archival Resource Key — persistent identifier, e.g. ark:/13030/tf5p30086k */
    const ARK          = 'ark';

    // ── Media ─────────────────────────────────────────────────────────────────
    /** Provider-supplied small/fast fallback image. Used when imgproxy is unavailable. */
    const THUMBNAIL_URL   = 'thumbnail_url';
    /**
     * Full-resolution image URL for non-IIIF sources (plain JPEG/PNG/TIFF).
     * Fed to the imgproxy Stimulus controller for on-demand resizing as elements
     * enter the viewport — avoids 429s and provider thumbnail quality issues.
     */
    const LARGE_IMAGE_URL = 'large_image_url';
    /**
     * IIIF Image API base URL — a real IIIF endpoint, NOT a plain image URL.
     * Append standard path segments to get any size:
     *   {iiif_base}/full/max/0/default.jpg       → original
     *   {iiif_base}/full/!512,512/0/default.webp → thumbnail
     * Distinct from iiif_manifest (Presentation API) and large_image_url (plain JPEG).
     */
    const IIIF_BASE       = 'iiif_base';
    const IIIF_MANIFEST   = 'iiif_manifest';
    const IIIF_INFO       = 'iiif_info';

    // -- Deterministic search fields ------------------------------------------
    /**
     * Low-cost deterministic summary generated from normalized catalogue fields.
     * This is NOT an AI dense summary. It exists so FTS/BM25 search has a compact,
     * readable text field even when AI enrichment is unavailable for cost or time.
     */
    #[VocabTerm(
        label: 'Search summary',
        aiHint: 'Deterministic BM25-friendly text assembled from normalized catalogue metadata. Do not treat as an AI claim.',
    )]
    const SEARCH_SUMMARY = 'search_summary';

    // ── AI-generated core fields ──────────────────────────────────────────────
    /**
     * Semantically dense summary ≤ 400 characters.
     * Optimised for Meilisearch /chat, RAG retrieval, and chatbot context windows.
     * Distinct from dcterms:abstract (human finding-aid prose) — this is tuned
     * for machine retrieval: entity-rich, factual, no filler words.
     * Predicate mirrors Claim::PRED_DENSE_SUMMARY = 'ai:denseSummary'.
     */
    #[VocabTerm(
        label: 'Dense summary',
        aiHint: 'Write a single retrieval-optimised paragraph of ≤ 400 characters. Pack in who, what, when, where, and material/technique. No filler phrases. Treat it as a search snippet, not a description.',
    )]
    const DENSE_SUMMARY = 'ai:denseSummary';

    // ── Linking ───────────────────────────────────────────────────────────────
    const PAGE_URL = 'page_url';

    // ── Date / language ───────────────────────────────────────────────────────
    const DATE     = 'date';
    const LANGUAGE = 'language';

    // ── Geography — schema: namespace per BaseItemDto.toValueMap() ───────────
    const LATITUDE  = 'schema:latitude';
    const LONGITUDE = 'schema:longitude';
    const COUNTRY   = 'country';
    const CITY      = 'city';
}
