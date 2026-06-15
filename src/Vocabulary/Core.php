<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/**
 * Canonical folio CORE codes — the short keys that name a core (table) inside a folio
 * and appear in URLs (`/folio/{provider}/{dataset}/{coreCode}/…`).
 *
 * These were previously magic strings scattered across provider singletons
 * (`'obj'`, `'per'`). Reference these constants instead.
 *
 * A core is the *kind of thing* a row is, distinct from its ContentType (the DTO type).
 * Cores relate to one another via folio links — e.g. an OBJECT (an artifact) can relate
 * to the DOCUMENT that describes it (a purchase contract), and a DOCUMENT to the IMAGE
 * rows that are its pages.
 *
 * @author Tac Tacelosky <tacman@gmail.com>
 */
final class Core
{
    /** Physical / artifact objects (artworks, specimens, objects of material culture). */
    public const OBJECT = 'obj';

    /** Text-primary records: pension files, letters, contracts, forms, transcripts. */
    public const DOCUMENT = 'doc';

    /** A single page/image belonging to a document or object (per-page OCR lives here). */
    public const IMAGE = 'image';

    /**
     * A top-level archival grouping — a NARA record group, a fonds, a museum
     * collection. Code is `coll` (not `collection`, to avoid clashing with
     * Doctrine's Collection); matches MuseumVocab::COLLECTION. Documents link to
     * it (DOCUMENT --inCollection--> COLLECTION).
     */
    public const COLLECTION = 'coll';

    /**
     * A mid-level archival grouping below a collection — a NARA series. Has a
     * title and a stable code (the series naId). Documents link to it
     * (DOCUMENT --inSeries--> SERIES); its description often reveals the records'
     * content type (photographs, maps, …).
     */
    public const SERIES = 'series';

    /** People (creators, subjects, authors). */
    public const PERSON = 'per';

    /** Places. */
    public const PLACE = 'place';

    /** Organisations. */
    public const ORGANISATION = 'org';

    /** Events. */
    public const EVENT = 'event';
}
