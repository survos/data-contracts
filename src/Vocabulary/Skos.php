<?php
declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/**
 * SKOS Simple Knowledge Organization System (http://www.w3.org/2004/02/skos/core#).
 *
 * Usage:
 *   Skos::PREF_LABEL->uri()   // → 'http://www.w3.org/2004/02/skos/core#prefLabel'
 *   Skos::PREF_LABEL->value   // → 'skos:prefLabel'
 *   Skos::CONCEPT_TYPE        // → 'http://www.w3.org/2004/02/skos/core#Concept'
 *
 * @see https://www.w3.org/TR/skos-reference/
 */
enum Skos: string
{
    case PREF_LABEL  = 'skos:prefLabel';
    case ALT_LABEL   = 'skos:altLabel';
    case HIDDEN_LABEL= 'skos:hiddenLabel';
    case NOTE        = 'skos:note';
    case SCOPE_NOTE  = 'skos:scopeNote';
    case DEFINITION  = 'skos:definition';
    case BROADER     = 'skos:broader';
    case NARROWER    = 'skos:narrower';
    case RELATED     = 'skos:related';
    case EXACT_MATCH = 'skos:exactMatch';
    case CLOSE_MATCH = 'skos:closeMatch';
    case IN_SCHEME   = 'skos:inScheme';

    public const NAMESPACE_URI = 'http://www.w3.org/2004/02/skos/core#';
    public const PREFIX = 'skos';

    /** rdf:type URI for a skos:Concept node. */
    public const CONCEPT_TYPE = self::NAMESPACE_URI . 'Concept';

    public function localName(): string
    {
        return substr($this->value, strlen(self::PREFIX) + 1);
    }

    public function uri(): string
    {
        return self::NAMESPACE_URI . $this->localName();
    }
}
