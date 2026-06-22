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

        /**
         * Whether this field is a low-cardinality controlled vocabulary that should be promoted to a
         * TermSet (distinct values → terms) during dataset processing and related to rows on folio
         * import. Flagging it here is the single source of truth — no per-dataset --fields.
         */
        public readonly bool $termSet = false,

        /**
         * Extra normalized item fields whose values feed this term set, beyond the vocabulary code
         * itself — like #[Map] source aliases, but for term sets. Lets one set draw from both
         * museum-coded rows (keyed by the code, e.g. 'obj') and ItemField-shaped rows ('subjects',
         * 'keywords'). The code is always an implicit source. This is what makes "is a termset"
         * (termSet) and "fed by these fields" a single declaration instead of two maps.
         *
         * @var list<string>
         */
        public readonly array $sourceFields = [],

        /**
         * Model this vocabulary as a RELATION to its own core (an entity worth its own page —
         * person, collection) rather than a flat term set. When true, the extractor materialises a
         * <code>.jsonl core (id=slug, label=value) from the sourceFields and writes typed edges
         * obj→<code> with $linkType (reverse $reverseCode), instead of a TermSet. Mutually exclusive
         * with $termSet. The deciding line: would you enrich it (Wikidata bio/photo)? → relation.
         */
        public readonly bool $relation = false,

        /** Forward edge code for a $relation vocab (e.g. 'created'); reverse is $reverseCode. */
        public readonly ?string $linkType = null,

        /** Reverse edge code for a $relation vocab (e.g. 'created_by', 'contains'). */
        public readonly ?string $reverseCode = null,

        /**
         * For a $relation vocab whose values are personal names: parse each value with PersonName
         * (classify person/org, extract birth/death) when materialising the entity. Off for
         * non-personal entity cores like collections, whose value is just a title.
         */
        public readonly bool $personName = false,
    ) {}
}
