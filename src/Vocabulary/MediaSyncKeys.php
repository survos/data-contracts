<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/**
 * Keys of the media:sync batch protocol — the contract between the producer
 * (MediaBatchDispatcher, fed by media:ensure) and the consumer (mediary's
 * batch endpoint). Defined once here so writer and reader can never drift.
 *
 * The batch payload is: {client, urls, dispatch, context, sourceClaims}, where
 * `context` and `sourceClaims` are keyed by image URL. DATASET and RECORD_KEY
 * live inside a URL's context entry; SOURCE_CLAIMS is its own top-level map.
 */
final class MediaSyncKeys
{
    /** Top-level payload map: URL => list<ClaimDTO> (modeled @import source claims). */
    public const string SOURCE_CLAIMS = 'sourceClaims';

    /** Context key: dataset id (provider/code, e.g. "mus/fortepan") — the claim scope. */
    public const string DATASET = 'dataset';

    /** Context key: item grouping id — becomes the mediary MediaRecord the claims hang on. */
    public const string RECORD_KEY = 'media_record_key';

    /**
     * Context key: list<string> of AI task names to seed onto the mediary Asset's
     * aiQueue (e.g. ["enrich_from_thumbnail"]). A processing DIRECTIVE, not a
     * source claim — it tells mediary which AI to run; it is never compared
     * against AI output the way {@see SOURCE_CLAIMS} are.
     */
    public const string AI_QUEUE = 'ai_queue';

    /**
     * Context keys: OCR the producer already holds, handed over at ingest instead of being
     * regenerated. A harvester of NDNP newspapers has publisher ALTO for every page — word
     * coordinates included — so letting mediary re-OCR the same page with a general model is both
     * slower and worse. When {@see self::OCR_TEXT} arrives, mediary records it against
     * {@see OcrProvider} and skips its own local OCR pass.
     */
    public const string OCR_TEXT = 'ocr_text';
    /** Context key: one of {@see OcrProvider}'s constants. Required alongside OCR_TEXT. */
    public const string OCR_PROVIDER = 'ocr_provider';
    /** Context key: the specific model/tool version, when the provider has one. */
    public const string OCR_MODEL = 'ocr_model';
    /** Context key: float 0..1, when the source reports one. */
    public const string OCR_CONFIDENCE = 'ocr_confidence';
    /** Context key: where the OCR came from (e.g. the page's ALTO XML URL). */
    public const string OCR_SOURCE_URL = 'ocr_source_url';

    private function __construct()
    {
    }
}
