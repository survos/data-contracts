<?php

declare(strict_types=1);

namespace Survos\DataContracts\Util;

/**
 * Pure-PHP array/object utilities with no framework dependencies.
 */
final class Arrays
{
    /**
     * Recursively remove null values, empty strings, and empty arrays/objects
     * from a data structure, producing a sparse representation.
     *
     * Typical use: compacting XML→JSON documents (e.g. Europeana) before
     * indexing in Meilisearch or storing as JSONL.
     *
     * @param array<mixed>|object|null $data
     * @return array<mixed>|object|null
     */
    public static function sparse(array|object|null $data): array|object|null
    {
        if ($data === null) {
            return null;
        }

        $isObject = is_object($data);
        if ($isObject) {
            $data = (array) $data;
        }

        if (is_array($data)) {
            $clean = [];
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $value = self::sparse($value);
                }
                if ($value === null) continue;
                if ($value === '') continue;
                if ($value === []) continue;
                if (is_object($value) && (array) $value === []) continue;
                $clean[$key] = $value;
            }
            return $isObject ? (object) $clean : $clean;
        }

        return $data;
    }

    /**
     * Rename a key during normalization: move $row[$from] to $row[$to] and drop $from.
     *
     * PHP has no native key rename, so normalizers tend to do `$row[$to] = $row[$from]; unset(...)`
     * by hand (and often forget the unset, leaving the old key to spill into a DTO's extras). This
     * centralises that move. The optional $transform reshapes the value as it moves — e.g. wrap a
     * scalar, or array_map over a list:
     *
     *   Arrays::renameKey($row, 'subject_facet', ItemField::SUBJECTS);
     *   Arrays::renameKey($row, 'genre_basic', 'genreBasic', static fn (array $v) => array_map('trim', $v));
     *
     * No-op when $from is absent or $from === $to. Mutates $row by reference and also returns it so
     * calls can be chained or used as an expression.
     *
     * @param array<string,mixed> $row
     * @param (callable(mixed):mixed)|null $transform applied to the value as it moves to $to
     * @return array<string,mixed>
     */
    public static function renameKey(array &$row, string $from, string $to, ?callable $transform = null): array
    {
        if ($from === $to || !array_key_exists($from, $row)) {
            return $row;
        }

        $value = $row[$from];
        unset($row[$from]);
        $row[$to] = $transform === null ? $value : $transform($value);

        return $row;
    }
}
