<?php
declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

use Survos\DataContracts\Attribute\VocabTerm;

/**
 * Canonical field keys for normalized item records.
 *
 * Two roles:
 *   1. #[Map(source: ItemField::TITLE)] — plain source key in raw API/CSV data
 *   2. Normalized JSONL output keys for non-DC, non-museum fields
 *
 * DC output predicates (dcterms:title, dcterms:creator, …) → DcTerms enum
 * Museum authority codes (cul, tec, mat, …)                → MuseumVocab interface
 *
 * Note: plain names here (e.g. 'title') are distinct from their dcterms: equivalents
 * ('dcterms:title'). Use DcTerms::TITLE->value for the output predicate,
 * ItemField::TITLE as the source/input key name.
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
    const THUMBNAIL_URL   = 'thumbnail_url';
    /** Full-resolution download URL for non-IIIF sources. */
    const LARGE_IMAGE_URL = 'large_image_url';
    const IIIF_BASE       = 'iiif_base';
    const IIIF_MANIFEST   = 'iiif_manifest';
    const IIIF_INFO       = 'iiif_info';

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
