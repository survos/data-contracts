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

    /**
     * Role a person held in a specific recorded context, e.g. "Registered Person", "Buyer",
     * "Vessel Captain" -- distinct from $status (a person's overall recorded legal/social
     * condition): role is context-specific (a person can hold different roles in different
     * records), status is closer to a standing attribute. Housekeeping-flavored (an internal
     * cataloging role vocabulary more than a reader-facing fact) -- real, useful for project
     * admins now; may warrant an admin-only visibility tier later (no such mechanism exists
     * yet in this app as of 2026-08-12).
     */
    #[VocabTerm(
        label: 'Role',
        aiHint: 'Role the person held in this record, e.g. "Registered Person", "Buyer", "Vessel Captain".',
        termSet: true,
    )]
    const ROLE = 'role';

    /**
     * Contributing project/dataset that sourced this record, e.g. "SlaveVoyages", "Maranhao
     * Inventories Slave Database" -- provenance/cataloging metadata, not a fact about the
     * subject itself. Same admin-visibility caveat as $role.
     */
    #[VocabTerm(
        label: 'Project',
        aiHint: 'Contributing project or dataset that sourced this record, e.g. "SlaveVoyages".',
        termSet: true,
    )]
    const PROJECT = 'project';

    /**
     * Type/format of a source/document record, e.g. "Bill of Sale, Invoice, or Receipt",
     * "Census or Register", "Newspaper" -- distinct from ItemField::GENRE_* (broad object-genre
     * vocabulary): this is specifically what KIND of primary-source record this is.
     */
    #[VocabTerm(
        label: 'Source Type',
        aiHint: 'Type or format of the source record, e.g. "Bill of Sale, Invoice, or Receipt", "Census or Register".',
        termSet: true,
    )]
    const SOURCE_TYPE = 'sourceType';
}
