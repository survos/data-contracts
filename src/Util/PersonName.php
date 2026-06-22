<?php

declare(strict_types=1);

namespace Survos\DataContracts\Util;

/**
 * Parse a Dublin-Core creator string into a structured person/agent entity.
 *
 * Dublin Core conflates persons and corporate bodies in one `creator` field, so we classify and
 * extract what we can up front — the value-level groundwork for a real `per` core that the enrich
 * phase later reconciles to Wikidata (bio/photo for persons, logo/info for orgs):
 *
 *   "Fergusson, James, 1808-1886"  → {kind: person, name: "Fergusson, James", birth: 1808, death: 1886}
 *   "Cousens, Henry, 1854-1934"    → {kind: person, name: "Cousens, Henry",   birth: 1854, death: 1934}
 *   "Boston Public Library"        → {kind: org,    name: "Boston Public Library"}
 *
 * The classification is a heuristic (DC gives us no type), refined later by enrichment.
 */
final class PersonName
{
    /** Trailing life dates: ", 1808-1886", ", 1854-", ", b. 1854", "(1808-1886)". */
    private const LIFE_DATES = '/[,(]\s*(?:b\.\s*|c(?:a)?\.\s*)?(\d{3,4})\s*[-–—]\s*(\d{3,4})?\s*\)?\s*$/u';

    /**
     * @return array{kind: string, name: string, birth: ?int, death: ?int}
     */
    public static function parse(string $value): array
    {
        $raw = trim($value);
        $name = $raw;
        $birth = null;
        $death = null;

        if (preg_match(self::LIFE_DATES, $raw, $m) === 1) {
            $birth = (int) $m[1];
            $death = isset($m[2]) && $m[2] !== '' ? (int) $m[2] : null;
            $name = trim(preg_replace(self::LIFE_DATES, '', $raw) ?? $raw, " ,(\t");
        }

        return [
            'kind' => self::classify($name, $birth !== null),
            'name' => $name,
            'birth' => $birth,
            'death' => $death,
        ];
    }

    /** person when it has life dates or reads as an inverted "Last, First" personal name; else org. */
    private static function classify(string $name, bool $hasLifeDates): string
    {
        if ($hasLifeDates) {
            return 'person';
        }

        // Inverted personal name: exactly one comma, and the part after it looks like a forename
        // (not an institutional qualifier). Crude but catches "Smith, John" vs "Library, British".
        if (preg_match('/^[^,]+,\s+\p{Lu}[\p{L}.\-]*\s*$/u', $name) === 1) {
            return 'person';
        }

        return 'org';
    }
}
