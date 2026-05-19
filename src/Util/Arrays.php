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
}
