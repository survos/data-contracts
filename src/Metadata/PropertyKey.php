<?php

declare(strict_types=1);

namespace Survos\DataContracts\Metadata;

/** Portable dataset metadata keys. Pipeline configuration is deliberately excluded. */
final class PropertyKey
{
    public const string LABEL = 'label';
    public const string DESCRIPTION = 'description';
    public const string CONTENT_TYPE = 'contentType';
    public const string TAGS = 'tags';
    public const string ROW_COUNT = 'rowCount';
    public const string SCHEMA_VERSION = 'schemaVersion';
    public const string TITLE_RECORD = 'titleRecord';
    public const string TITLE_PREFIX = 'title.';
    public const string PROPERTIES = 'metadataProperties';

    public static function validate(string $key, mixed $value): void
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*(?:[._-][a-zA-Z0-9]+)*$/D', $key)) {
            throw new \InvalidArgumentException('Invalid metadata key: '.$key);
        }
        $valid = match ($key) {
            self::LABEL, self::DESCRIPTION, self::CONTENT_TYPE => $value === null || is_string($value),
            self::ROW_COUNT, self::SCHEMA_VERSION, 'title.issueCount' => is_int($value) && $value >= 0,
            self::TAGS => is_array($value) && array_is_list($value) && array_all($value, static fn (mixed $item): bool => is_string($item)),
            'title.source', 'title.essay' => $value === null || is_string($value),
            'title.firstIssue', 'title.lastIssue' => $value === null || (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value) === 1),
            'title.title', 'title.essayContributor', 'title.datesOfPublication', 'title.frequency',
            'title.createdPublished', 'title.notes', 'title.subjectHeadings', 'title.precedingTitles', 'title.oclc' =>
                $value === null || is_string($value) || (is_array($value) && array_is_list($value) && array_all($value, static fn (mixed $item): bool => is_string($item))),
            default => true,
        };
        if (!$valid) {
            throw new \InvalidArgumentException('Invalid value for metadata key: '.$key);
        }
        json_encode($value, JSON_THROW_ON_ERROR);
    }
}
