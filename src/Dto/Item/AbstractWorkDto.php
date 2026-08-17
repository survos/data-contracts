<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

/**
 * Base for DTOs that are CreativeWorks — the work itself, as opposed to the places,
 * events and people a work is about.
 *
 * It exists to give the CreativeWork-only fields somewhere to live. They are still
 * declared on BaseItemDto today ($citation, $credit, $rightsUri, $language, $ocrText,
 * $creators, $institution, $date), which PlaceDto, PoiDto and EventDto also extend —
 * so schema.org mappings for them cannot be declared without emitting CreativeWork
 * properties on Place and Event nodes, which is invalid.
 *
 * This class is deliberately EMPTY for now: introducing it is a no-op reparenting,
 * safe to release on its own. Moving the fields down is the risky half, because
 * BaseItemDto::fromMeta() populates them for every subclass — see survos/mono#TBD.
 *
 * StopDto and StoryDto are deliberately NOT reparented here: whether a tour stop is a
 * place and whether a story is an Article are domain calls, not mechanical ones.
 */
abstract class AbstractWorkDto extends BaseItemDto
{
}
