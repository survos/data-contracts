<?php
declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

use Survos\DataContracts\Attribute\VocabTerm;

/**
 * Controlled-vocabulary fields describing a PERSON, EVENT, or PLACE entity itself — as opposed to
 * MuseumVocab, which describes an OBJECT (what it's made of / by / from / about). Where MuseumVocab
 * answers "what is this object?", EntityVocab answers "what is this person/event/place?": a
 * person's gender or status, an event's type, a place's type.
 *
 * Each constant's value doubles as the field key in normalized JSONL records (same convention as
 * MuseumVocab) and the TermSet code in folios — TermSetBinding reflects both interfaces, so a
 * dataset provider writing e.g. $row[EntityVocab::STATUS] = $qId and a term-set extraction pass
 * writing termSet.jsonl with code=EntityVocab::STATUS is all that's needed for the value to
 * resolve to a real label in the search sidebar.
 *
 * First real source: Enslaved.org's Wikibase dump (App\Aggregator\Provider\WikibaseProvider,
 * survos-sites/musdig#35) — a genuinely person/event/place-shaped dataset with no generic
 * "object" entity at all, which is what exposed MuseumVocab's object-only framing as a gap.
 */
interface EntityVocab
{
    #[VocabTerm(
        label: 'Gender',
        uri: 'http://xmlns.com/foaf/0.1/gender',
        aiHint: 'Gender of the person, e.g. "Male", "Female".',
        termSet: true,
    )]
    const GENDER = 'gender';

    #[VocabTerm(
        label: 'Status',
        aiHint: 'Legal, social, or life status of the person, e.g. "Enslaved Person", "Free Person", "Deceased".',
        termSet: true,
    )]
    const STATUS = 'status';

    #[VocabTerm(
        label: 'Occupation',
        uri: 'http://purl.org/dc/terms/subject',
        aiHint: 'Occupation, trade, or role the person is recorded as having, e.g. "Maritime", "Agriculture".',
        termSet: true,
    )]
    const OCCUPATION = 'occupations';

    #[VocabTerm(
        label: 'Ethnicity',
        aiHint: 'Ethnic or ethnolinguistic origin group as recorded by the source, e.g. "Igbo", "Afro-Creole".',
        termSet: true,
    )]
    const ETHNICITY = 'ethnicity';

    #[VocabTerm(
        label: 'Event Type',
        aiHint: 'Type or category of the event, e.g. "Birth", "Sale or Transfer", "Voyage".',
        termSet: true,
    )]
    const EVENT_TYPE = 'eventType';

    #[VocabTerm(
        label: 'Place Type',
        aiHint: 'Type of place, e.g. "Plantation, Estate, or Ranch", "Port", "County or Parish".',
        termSet: true,
    )]
    const PLACE_TYPE = 'placeType';
}
