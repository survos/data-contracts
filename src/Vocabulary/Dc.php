<?php
declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/**
 * Dublin Core Elements 1.1 (http://purl.org/dc/elements/1.1/).
 *
 * The original 15-term DC vocabulary, still used by Europeana EDM proxies,
 * OAI-PMH oai_dc, and many older RDF datasets. Use DcTerms for the refined
 * namespace (http://purl.org/dc/terms/).
 *
 * Usage:
 *   Dc::TITLE->uri()        // → 'http://purl.org/dc/elements/1.1/title'
 *   Dc::TITLE->value        // → 'dc:title'
 *   Dc::TITLE->localName()  // → 'title'
 *   Dc::TITLE->asDcTerms()  // → DcTerms::TITLE
 *
 * @see https://www.dublincore.org/specifications/dublin-core/dces/
 */
enum Dc: string
{
    case TITLE       = 'dc:title';
    case CREATOR     = 'dc:creator';
    case SUBJECT     = 'dc:subject';
    case DESCRIPTION = 'dc:description';
    case PUBLISHER   = 'dc:publisher';
    case CONTRIBUTOR = 'dc:contributor';
    case DATE        = 'dc:date';
    case TYPE        = 'dc:type';
    case FORMAT      = 'dc:format';
    case IDENTIFIER  = 'dc:identifier';
    case SOURCE      = 'dc:source';
    case LANGUAGE    = 'dc:language';
    case RELATION    = 'dc:relation';
    case COVERAGE    = 'dc:coverage';
    case RIGHTS      = 'dc:rights';

    public const NAMESPACE_URI = 'http://purl.org/dc/elements/1.1/';
    public const PREFIX = 'dc';

    public function localName(): string
    {
        return substr($this->value, strlen(self::PREFIX) + 1);
    }

    public function uri(): string
    {
        return self::NAMESPACE_URI . $this->localName();
    }

    /** Map to the equivalent DcTerms case (same local name, refined namespace). */
    public function asDcTerms(): DcTerms
    {
        return DcTerms::from('dcterms:' . $this->localName());
    }
}
