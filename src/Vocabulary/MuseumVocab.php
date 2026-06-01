<?php
declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

use Survos\DataContracts\Attribute\VocabTerm;

/**
 * Museum authority vocabulary — 3-letter codes for controlled vocabulary fields.
 *
 * Use MuseumVocab for fields describing WHO made it, WHAT it's made of, WHERE it's
 * from, and WHAT it depicts: cul (culture), med (medium), mat (material), tec
 * (technique), pla (place), per (person), obj (subject), org (organisation).
 * These codes are the FIELD KEYS in normalized JSONL records and TermSet codes in folios.
 *
 * NOT for core record fields. Those live in ItemField (id, title, language, …).
 *
 * Rule of thumb:
 *   "What is this object made of / by / from / about?" → MuseumVocab (med, per, cul, obj)
 *   "What TYPE is this object?" → ItemField::GENRE_SPECIFIC or ItemField::TYPE
 *
 * In normalize listeners, always write:
 *   $row[MuseumVocab::MEDIUM]  = ...   ✓   NOT $row['medium']   ✗
 *   $row[MuseumVocab::CULTURE] = ...   ✓   NOT $row['culture']  ✗
 *
 * Each constant carries a #[VocabTerm] attribute with label, linked-data URI,
 * and AI extraction hint — consumed by TypeEnrichmentTask to auto-generate prompts.
 */
interface MuseumVocab
{
    #[VocabTerm(
        label: 'Culture',
        uri: 'http://id.loc.gov/vocabulary/graphicMaterials/tgm002187',
        aiHint: 'Named cultural group, civilisation, or ethnic origin, e.g. "Roman", "Aztec", "Edo period Japan".',
        termSet: true,
    )]
    const CULTURE = 'cul';

    #[VocabTerm(
        label: 'Technique',
        uri: 'http://purl.org/dc/terms/medium',
        aiHint: 'Method or process of manufacture, e.g. "Lost-wax casting", "Oil on canvas", "Woodblock print".',
        termSet: true,
    )]
    const TECHNIQUE = 'tec';

    #[VocabTerm(
        label: 'Material',
        uri: 'http://purl.org/dc/terms/medium',
        aiHint: 'Physical substance(s) the object is made from, e.g. "Terracotta", "Gold and turquoise", "Silk".',
        termSet: true,
    )]
    const MATERIAL = 'mat';

    #[VocabTerm(
        label: 'Medium',
        uri: 'http://purl.org/dc/terms/medium',
        aiHint: 'Combined material and technique shorthand as used by the holding institution.',
        termSet: true,
    )]
    const MEDIUM = 'med';

    #[VocabTerm(
        label: 'Place',
        uri: 'http://purl.org/dc/terms/spatial',
        aiHint: 'Geographic findspot, place of origin, or provenance location.',
        termSet: true,
    )]
    const PLACE = 'pla';

    #[VocabTerm(
        label: 'Person',
        uri: 'http://xmlns.com/foaf/0.1/Person',
        aiHint: 'Named person depicted in, associated with, or mentioned in the object.',
    )]
    const PERSON = 'per';

    #[VocabTerm(
        label: 'Subject',
        uri: 'http://purl.org/dc/terms/subject',
        aiHint: 'Subject heading, keyword, or topical term describing the object.',
    )]
    const SUBJECT = 'obj';

    #[VocabTerm(
        label: 'Organisation',
        uri: 'http://www.w3.org/ns/org#Organization',
        aiHint: 'Named organisation, institution, or corporate body associated with the object.',
    )]
    const ORGANISATION = 'org';

    #[VocabTerm(
        label: 'Period',
        uri: 'http://purl.org/dc/terms/temporal',
        aiHint: 'Named historical period, dynasty, or style era, e.g. "Ming Dynasty", "Victorian", "Art Deco".',
        termSet: true,
    )]
    const PERIOD = 'period';

    #[VocabTerm(
        label: 'Epoch',
        uri: 'http://purl.org/dc/terms/temporal',
        aiHint: 'Broad archaeological or geological era, broader than period, e.g. "Bronze Age", "Neolithic", "Classical Antiquity".',
        termSet: true,
    )]
    const EPOCH = 'epoch';

    #[VocabTerm(
        label: 'Collection',
        uri: 'http://purl.org/dc/terms/isPartOf',
        aiHint: 'Institutional or thematic collection within the holding museum.',
        aiExtractable: false,
        termSet: true,
    )]
    const COLLECTION = 'coll';

    #[VocabTerm(
        label: 'Department',
        aiHint: 'Curatorial department within the museum, e.g. "Ancient Near East", "Prints and Drawings".',
        aiExtractable: false,
        termSet: true,
    )]
    const DEPARTMENT = 'dept';

    #[VocabTerm(
        label: 'Accession number',
        aiHint: 'Institutional catalog or accession number assigned by the holding museum.',
        aiExtractable: false,
    )]
    const ACCESSION = 'accession';

    #[VocabTerm(
        label: 'Dimensions',
        uri: 'http://purl.org/dc/terms/extent',
        aiHint: 'Physical dimensions as a free-text string, e.g. "34.2 × 22.1 cm", "H. 45 cm, W. 30 cm".',
    )]
    const DIMENSIONS = 'dimensions';

    #[VocabTerm(
        label: 'Provenance',
        uri: 'http://purl.org/dc/terms/provenance',
        aiHint: 'Ownership and acquisition history of the object.',
    )]
    const PROVENANCE = 'provenance';

    #[VocabTerm(
        label: 'Credit line',
        aiHint: 'Donor attribution text for display, e.g. "Gift of John Smith, 1987".',
        aiExtractable: false,
    )]
    const CREDIT = 'credit';

    #[VocabTerm(
        label: 'Inscription',
        aiHint: 'Text inscribed, painted, stamped, or otherwise applied to the object — transcribe verbatim.',
    )]
    const INSCRIPTION = 'inscription';
}
