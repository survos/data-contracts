<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\PropertyMeta;
use Survos\DataContracts\Metadata\ContentType;

/**
 * A coin: a physical numismatic specimen plus the descriptive type metadata
 * (denomination, mint, authority, date, obverse/reverse) merged from its Nomisma type series.
 *
 * Physical measurements use the inherited PhysicalObjectDto records: `weight` ([{amount,units}]),
 * `dimensions` ([{diameter,units}]) and `dimensionsRaw`; metal is the inherited `mat`.
 */
class CoinDto extends CurrencyDto
{
    public static function contentType(): string { return ContentType::COIN; }

    /** Mint / place of striking. */
    #[PropertyMeta(label: 'Mint', description: 'Mint or place of striking.', facet: true)]
    public ?string $mint = null;

    /** Region the coin belongs to. */
    #[PropertyMeta(label: 'Region', description: 'Geographic region.', facet: true)]
    public ?string $region = null;

    /** Earliest year of the type's date range (negative = BCE), sortable. */
    #[PropertyMeta(label: 'Date from', description: 'Earliest year of the issue (negative = BCE).', sortable: true)]
    public ?int $dateEarly = null;

    /** Latest year of the type's date range. */
    #[PropertyMeta(label: 'Date to', description: 'Latest year of the issue.', sortable: true)]
    public ?int $dateLate = null;

    /** Die axis in clock hours (1–12). */
    public ?int $axis = null;

    /** Obverse (front) legend text. */
    public ?string $obverseLegend = null;
    /** Obverse iconography description. */
    public ?string $obverseDescription = null;
    /** Reverse (back) legend text. */
    public ?string $reverseLegend = null;
    /** Reverse iconography description. */
    public ?string $reverseDescription = null;

    /** The Nomisma type-series URI this specimen instantiates (e.g. OCRE/PELLA). */
    public ?string $typeSeries = null;
    /** Hoard the coin is part of, where recorded. */
    public ?string $hoard = null;
}
