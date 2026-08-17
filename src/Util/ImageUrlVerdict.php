<?php

declare(strict_types=1);

namespace Survos\DataContracts\Util;

/**
 * Outcome of {@see ImageUrl::classify()}. Distinguishes the two failure modes that used to
 * look identical (a broken thumbnail): a *wrong field picked at harvest* versus *the asset
 * really is a document*.
 */
enum ImageUrlVerdict: string
{
    /** Known raster image extension. */
    case Image = 'image';

    /** No extension to judge by (delivery endpoint, IIIF base) — treated as usable. */
    case Unverifiable = 'unverifiable';

    /** A document (PDF etc.). Real asset, but imgproxy cannot rasterise it. */
    case Document = 'document';

    /** A viewer config, landing page, or data file mistakenly stored as the image. */
    case NotAnImage = 'not_an_image';

    /** No URL at all. */
    case Empty = 'empty';

    /** Should this URL ever be handed to imgproxy? */
    public function isRenderable(): bool
    {
        return $this === self::Image || $this === self::Unverifiable;
    }

    /** Does this indicate a harvest bug worth logging, as opposed to expected data? */
    public function isHarvestDefect(): bool
    {
        return $this === self::NotAnImage;
    }

    public function label(): string
    {
        return match ($this) {
            self::Image => 'Image',
            self::Unverifiable => 'Unverified (no extension)',
            self::Document => 'Document (not rasterisable)',
            self::NotAnImage => 'Not an image',
            self::Empty => 'No image URL',
        };
    }
}
