<?php
declare(strict_types=1);

namespace Survos\DataContracts\Attribute;

/**
 * Decorates a vocabulary constant with machine-readable metadata.
 *
 * Consumers:
 *   - AI extraction tasks: use $label + $aiHint to build field prompts
 *   - RDF / Omeka-S export: map via $uri
 *   - Documentation generators: render $label + $description
 *
 * PHP 8.3+ supports attributes on class/interface constants.
 */
#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
final class VocabTerm
{
    public function __construct(
        /** Human-readable display label. */
        public readonly string $label,

        /**
         * Linked-data URI for this term.
         * e.g. 'http://purl.org/dc/terms/medium', 'https://schema.org/latitude'
         */
        public readonly ?string $uri = null,

        /**
         * Hint for AI extraction — injected into the field prompt.
         * e.g. 'Named cultural group or civilisation, e.g. "Roman", "Edo period Japan"'
         */
        public readonly ?string $aiHint = null,

        /**
         * Whether AI should attempt to extract this field from observation prose.
         * Defaults to true; set false for fields that come from structured source data only.
         */
        public readonly bool $aiExtractable = true,
    ) {}
}
