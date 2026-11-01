<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/** Authority schemes and relation semantics; endpoints belong to the consuming app. */
final class Authority
{
    public const GEONAMES = 'geonames';
    public const GEOBOUNDARIES = 'geoboundaries';
    public const WIKIDATA = 'wikidata';
    public const SAME_ENTITY = 'sameEntity';
    public const BOUNDARY = 'boundary';
}
