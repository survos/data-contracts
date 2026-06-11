<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\PropertyMeta;

/**
 * Base DTO for currency objects (coins, banknotes, tokens, medals).
 *
 * A currency object is a physical object, so it inherits the structured `mat` (material),
 * `dimensions` and `weight` records from PhysicalObjectDto; this layer adds the monetary
 * attributes shared across currency forms. Concrete subclasses (CoinDto, …) add form-specific
 * fields and declare their ContentType.
 */
abstract class CurrencyDto extends PhysicalObjectDto
{
    /** The unit of value (e.g. Denarius, Antoninianus, Drachm). */
    #[PropertyMeta(label: 'Denomination', description: 'The denomination / unit of value.', facet: true)]
    public ?string $denomination = null;

    /** Issuing authority — the ruler, state or institution that struck/issued it. */
    #[PropertyMeta(label: 'Authority', description: 'Issuing authority (ruler, state or institution).', facet: true)]
    public ?string $authority = null;
}
