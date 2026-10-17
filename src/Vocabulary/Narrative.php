<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

use Survos\DataContracts\Dto\Link\LinkTypeRow;

/**
 * The narrative vocabulary — story, tour, tour stop, chapter — defined once so producers and
 * readers stop inventing their own strings. "Tour" here is a museum/walking tour of real things;
 * it is NOT the UI walkthrough of survos/tour-bundle (TourStep etc.), which shares only the word.
 *
 * Terms:
 *
 *  - STORY    A story.v1 document: the narrative view of a tour, served by exhibit-bundle
 *             (blocks, bookmarks, resources). Not a folio core. Curatescape also calls a single
 *             place a "story" — that is an obj row, not this.
 *  - TOUR     An ordered walk through things: one row in the `tour` core (Core::TOUR,
 *             ContentType::TOUR, TourDto).
 *  - TOUR STOP One place's position in a tour. Not a row: it is the `tour —has_stop {ordinal}→ obj`
 *             link (reverse `stop_of`); per-stop data (audio, framing) goes on the link's extras.
 *             Wire name {@see TOUR_STOP} if it ever needs one.
 *  - CHAPTER  A titled group of story.v1 blocks, as zm's StoryNarrator drafts `chapters[]` for
 *             movies.
 */
final class Narrative
{
    public const string STORY = 'story';
    public const string TOUR = Core::TOUR;
    public const string TOUR_STOP = 'tourStop';
    public const string CHAPTER = 'chapter';

    /** tour → obj, ordered by the link's `ordinal`. */
    public const string HAS_STOP = 'has_stop';

    /** Reverse of HAS_STOP: obj → the tours it is on. */
    public const string STOP_OF = 'stop_of';

    /** The canonical `tour:has_stop:obj` link type; producers use this rather than writing their own. */
    public static function hasStop(): LinkTypeRow
    {
        return new LinkTypeRow(
            code: self::HAS_STOP,
            subjectCore: Core::TOUR,
            objectCore: Core::OBJECT,
            forwardLabel: 'Stops',
            reverseCode: self::STOP_OF,
            reverseLabel: 'On tour',
            multiple: true,
        );
    }
}
