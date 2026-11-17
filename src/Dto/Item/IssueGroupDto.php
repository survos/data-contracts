<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;

/**
 * One group of a newspaper issue's text-sheet grouping: page blocks that belong together.
 *
 * A group is a row in the "group" core. Its issue, its parent group and the article it became are links
 * (PeriodicalVocabulary::GROUP_IN_ISSUE, GROUP_PARENT, GROUP_ARTICLE), never fields; order is the link's ordinal.
 */
#[ClassMeta(label: 'Issue group', description: 'Page blocks of a newspaper issue that belong together.')]
class IssueGroupDto extends AbstractWorkDto
{
    /** What the group is, e.g. article, column, advertisement. */
    #[Field(filterable: true, facet: true, group: 'Grouping')]
    public ?string $groupType = null;

    /** 0-based page index within the issue. */
    #[Field(filterable: true, group: 'Grouping')]
    public ?int $pageIndex = null;

    /** Column the group starts in, when the page analysis found columns. */
    public ?int $column = null;

    /** What decided the group, e.g. the rule that joined its blocks. */
    public ?string $basis = null;

    /** @var list<string>|null ids of the page-layout blocks in the group, in reading order */
    public ?array $blockIds = null;

    /** @var list<array<string, mixed>>|null each entry is a {@see \Survos\DataContracts\Dto\Periodical\RoleSpanDto} array */
    public ?array $roles = null;

    /** Why the grouper made this group, in its own words. */
    public ?string $evidence = null;

    /**
     * Only on a page's own group (groupType "page"): what the grouper measured on the page (columns, body
     * font size, masthead status, slots).
     *
     * @var array<string, mixed>|null
     */
    public ?array $analysis = null;

    /** @var list<string>|null only on a page's own group: blocks no group claimed */
    public ?array $unassignedBlockIds = null;

    /** @var list<string>|null only on a page's own group: blocks of a programme or listing */
    public ?array $programBlockIds = null;

    /** @var list<string>|null only on a page's own group: blocks of the masthead */
    public ?array $mastheadBlockIds = null;

    public static function contentType(): string
    {
        return ContentType::ISSUE_GROUP;
    }
}
