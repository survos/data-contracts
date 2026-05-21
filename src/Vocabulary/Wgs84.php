<?php
declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/**
 * WGS84 Geo Positioning vocabulary (http://www.w3.org/2003/01/geo/wgs84_pos#).
 *
 * @see https://www.w3.org/2003/01/geo/
 */
final class Wgs84
{
    public const NAMESPACE_URI = 'http://www.w3.org/2003/01/geo/wgs84_pos#';

    public const LAT  = self::NAMESPACE_URI . 'lat';
    public const LONG = self::NAMESPACE_URI . 'long';
    public const ALT  = self::NAMESPACE_URI . 'alt';
}
