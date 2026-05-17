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

    /** MuseumVocab::DIMENSIONS — human-readable dimensions string */
    public ?string $dimensions = null;

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
