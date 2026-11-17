<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/**
 * Structure of a periodical folio: its cores, its link types and the closed value sets of its typed fields.
 * Field names are in {@see ItemField}; nothing about a periodical is read from or written to extras.
 */
final class PeriodicalVocabulary
{
    public const string ISSUE_CORE = 'doc';
    public const string ARTICLE_CORE = 'article';
    public const string GROUP_CORE = 'group';

    /** article -> issue; ordinal is the record's order in the issue. */
    public const string IN_ISSUE = 'in_issue';
    public const string HAS_ARTICLE = 'has_article';
    /** group -> issue; ordinal is the group's order in the issue (page, then position on the page). */
    public const string GROUP_IN_ISSUE = 'group_in_issue';
    public const string HAS_GROUP = 'has_group';
    /** group -> its parent group; ordinal is the order among siblings. */
    public const string GROUP_PARENT = 'group_parent';
    public const string GROUP_CHILD = 'group_child';
    /** group -> the article it was published as. */
    public const string GROUP_ARTICLE = 'group_article';
    public const string ARTICLE_GROUP = 'article_group';

    /** groupType of the one group per page that holds the page's analysis and its unclaimed blocks. */
    public const string GROUP_TYPE_PAGE = 'page';

    public const string SOURCE_SCAN = 'scan';
    public const string SOURCE_DIGITAL = 'digital';
    public const array SOURCE_KINDS = [self::SOURCE_SCAN, self::SOURCE_DIGITAL];

    public const string TIER_BASIC = 'basic';
    public const string TIER_ENHANCED = 'enhanced';
    public const array ISSUE_TIERS = [self::TIER_BASIC, self::TIER_ENHANCED];

    /** A group's local id is scoped by its issue, so the same group id may repeat across issues. */
    public static function groupId(string $issueId, string $groupId): string
    {
        return $issueId.'#'.$groupId;
    }

    /** The issue a group's local id names. Group ids never contain "#"; an issue id may. */
    public static function issueOfGroup(string $groupLocalId): string
    {
        $at = strrpos($groupLocalId, '#');

        return false === $at ? throw new \InvalidArgumentException('Not a group id: '.$groupLocalId) : substr($groupLocalId, 0, $at);
    }
}
