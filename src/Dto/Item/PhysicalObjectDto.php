<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\DataContracts\Vocabulary\MuseumVocab;

/**
 * Base DTO for physical museum objects (objects with material substance).
 *
 * Covers anything you can hold, measure, or chemically analyse — artefacts,
 * artworks, specimens, documents on physical supports, etc.
 *
 * Born-digital content (digital-only photographs, audio, video) should
 * extend BaseItemDto directly rather than this class.
 */
abstract class PhysicalObjectDto extends BaseItemDto
{
    /** MuseumVocab::MATERIAL — physical materials, e.g. ['Ink', 'Paper'] */
    public ?array $mat = null;

    /** MuseumVocab::TECHNIQUE — fabrication techniques, e.g. ['Etching', 'Lithograph'] */
    public ?array $tec = null;

    /**
     * MuseumVocab::DIMENSIONS — structured dimension records.
     *
     * Each entry: {height?, width?, length?, depth?, radius?, units, name?}
     * where height/width/length/depth/radius are numeric values in `units`
     * (e.g. "cm", "in", "mm"). `name` is an optional label like "framed",
     * "sight", "with mount", etc. Producers should emit only the keys present
     * on the source record.
     *
     * @var list<array<string, mixed>>|null
     */
    public ?array $dimensions = null;

    /**
     * Original free-text dimensions string from the source, kept as a fallback
     * when the normalizer cannot fully parse it (e.g. fractional inches,
     * museum-specific shorthand). Templates may render this verbatim when
     * `$dimensions` is empty.
     */
    public ?string $dimensionsRaw = null;

    /**
     * Weight records. Each entry: {amount, units, name?}.
     *
     * @var list<array<string, mixed>>|null
     */
    public ?array $weight = null;

    /** MuseumVocab::CULTURE — cultural attribution, e.g. ['Mochica', 'Inca'] */
    public ?array $cul = null;

    /** MuseumVocab::PERIOD — historical period(s) */
    public ?array $period = null;

    /** MuseumVocab::ACCESSION — local accession / catalog number */
    public ?string $accession = null;

    /** Holding collection identifier */
    public ?int $collectionId = null;

    /** Number of associated digital images */
    public ?int $imageCount = null;

    public static function contentType(): string
    {
        return ContentType::OBJECT;
    }
}
