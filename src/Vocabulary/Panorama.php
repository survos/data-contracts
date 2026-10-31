<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

use Survos\DataContracts\Dto\Link\LinkTypeRow;

/**
 * Panorama objects and related-image placements, independent of the source database.
 *
 * A panorama is an obj, not a separate stop or media core. Its image URL identifies
 * the media asset; tours use Narrative::hasStop(). Heading/azimuth/inclination are
 * degrees. Distance is source renderer units (not metres). The source angle mode
 * must be retained until a consumer explicitly converts that renderer's convention.
 */
final class Panorama
{
    public const string MEDIA_KIND = 'mediaKind';
    public const string EQUIRECTANGULAR = 'equirectangular';
    public const string HEADING = 'heading';
    public const string SOURCE_ANGLE_MODE = 'useNewAngles';
    public const string AZIMUTH = 'azimuth';
    public const string INCLINATION = 'inclination';
    public const string DISTANCE = 'distance';
    public const string DEPICTS = 'depicts';
    public const string CONNECTS_TO = 'connects_to';

    public static function depicts(): LinkTypeRow
    {
        return new LinkTypeRow(self::DEPICTS, Core::OBJECT, Core::OBJECT, multiple: true);
    }

    public static function connectsTo(): LinkTypeRow
    {
        return new LinkTypeRow(self::CONNECTS_TO, Core::OBJECT, Core::OBJECT, multiple: true);
    }
}
