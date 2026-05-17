<?php
declare(strict_types=1);

namespace Survos\DataContracts\Attribute;

/**
 * Class-level metadata for domain objects, DTOs, and value types.
 *
 * Consumed by AI prompts, admin UIs, and search configuration via reflection.
 * field-bundle's EntityMeta extends this for admin/dashboard concerns.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ClassMeta
{
    public function __construct(
        /** Human-readable name for this type, e.g. "Photograph" */
        public readonly ?string $label = null,

        /** What this type covers — used verbatim in AI classification prompts */
        public readonly ?string $description = null,
    ) {}
}
