<?php
declare(strict_types=1);

namespace Survos\DataContracts\Attribute;

/**
 * Property-level metadata for search, faceting, and AI field hints.
 *
 * Consumed by Meilisearch config, AI prompts, and import pipelines via reflection.
 * field-bundle's Field extends this for grid/filter/widget concerns.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class PropertyMeta
{
    public function __construct(
        /** Display label; defaults to TitleCase property name */
        public readonly ?string $label = null,

        /** Field-level hint sent to AI prompts describing what this property holds */
        public readonly ?string $description = null,

        /** Include in full-text search (Meilisearch searchableAttributes) */
        public readonly bool $searchable = false,

        /** Allow ordering by this field (Meilisearch sortableAttributes) */
        public readonly bool $sortable = false,

        /** Include in facet panel (Meilisearch filterableAttributes + UI refinements) */
        public readonly bool $facet = false,
    ) {}
}
