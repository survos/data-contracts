<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/**
 * Who produced a piece of OCR text.
 *
 * Defined in the contracts library because it is written by one service and read by another: a
 * harvester that already holds publisher ALTO sends {@see self::ALTO}, and mediary must be able to
 * tell that apart from text its own pipeline generated — otherwise the only way to know whether an
 * asset still needs OCR is to re-run it.
 */
final class OcrProvider
{
    /**
     * OCR supplied by the source archive as ALTO XML, parsed by the harvester.
     *
     * Preferred over anything we generate: NDNP and its regional mirrors ship OCR produced from
     * the scanning masters with word-level coordinates, which is better than re-OCRing a derivative
     * JPEG and is already paid for.
     */
    public const string ALTO = 'alto';

    /** OCR produced by mediary's own ai-tools service. */
    public const string AI_TOOLS = 'ai-tools';

    /** OCR extracted from an embedded text layer in a supplied PDF. */
    public const string PDF_TEXT = 'pdf-text';

    private function __construct()
    {
    }
}
