<?php
declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/**
 * Core RDF vocabulary constants (http://www.w3.org/1999/02/22-rdf-syntax-ns#).
 *
 * @see https://www.w3.org/TR/rdf-schema/
 */
final class Rdf
{
    public const NAMESPACE_URI = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';

    public const TYPE    = self::NAMESPACE_URI . 'type';
    public const LANG_STRING = self::NAMESPACE_URI . 'langString';
}
