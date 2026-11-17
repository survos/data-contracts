<?php

declare(strict_types=1);

namespace Survos\DataContracts\Util;

use Survos\DataContracts\Metadata\ContentType;

/**
 * Parse a Dublin-Core creator string into a structured person/agent entity.
 *
 * Dublin Core conflates persons and corporate bodies in one `creator` field, so we classify and
 * extract what we can up front — the value-level groundwork for separate `per` and `org` cores that the enrich
 * phase later reconciles to Wikidata (bio/photo for persons, logo/info for orgs):
 *
 *   "Fergusson, James, 1808-1886"  → {kind: person, name: "Fergusson, James", birth: 1808, death: 1886}
 *   "Cousens, Henry, 1854-1934"    → {kind: person, name: "Cousens, Henry",   birth: 1854, death: 1934}
 *   "Boston Public Library"        → {kind: organization,    name: "Boston Public Library"}
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

        if (1 === preg_match(self::LIFE_DATES, $raw, $m)) {
            $birth = (int) $m[1];
            $death = isset($m[2]) && '' !== $m[2] ? (int) $m[2] : null;
            $name = trim(preg_replace(self::LIFE_DATES, '', $raw) ?? $raw, " ,(\t");
        }

        return [
            'kind' => self::classify($name, null !== $birth),
            'name' => $name,
            'birth' => $birth,
            'death' => $death,
        ];
    }

    /** Heuristic for untyped creator strings; explicit source entity types should take precedence. */
    private static function classify(string $name, bool $hasLifeDates): string
    {
        if ($hasLifeDates) {
            return ContentType::PERSON;
        }

        // Corporate names can contain commas or a person's name. Check their explicit
        // institutional vocabulary before interpreting a name-shaped string as a person.
        if (1 === preg_match('/\b(?:librar(?:y|ies)|universit(?:y|ies)|college|museum|archive[s]?|office|department|committee|association|society|foundation|institute|institution|company|corporation|inc|ltd|press|studio[s]?|research cent(?:er|re)|collections|admission[s]?)\b|&/iu', $name)) {
            return ContentType::ORGANIZATION;
        }

        // Inverted personal name: exactly one comma, and the part after it looks like a forename
        // (not an institutional qualifier). Crude but catches "Smith, John" vs "Library, British".
        if (1 === preg_match('/^[^,]+,\s+\p{Lu}[\p{L}.\-]*\s*$/u', $name)) {
            return ContentType::PERSON;
        }

        // Natural-order personal names (including initials and common name particles).
        // A single untyped word is ambiguous; keep the historical corporate fallback.
        if (1 === preg_match("/^(?:\p{Lu}[\p{L}’'.-]*|(?:de|del|van|von|der|da|di|la|le))(?:(?:\s+)(?:\p{Lu}[\p{L}’'.-]*|(?:de|del|van|von|der|da|di|la|le))){1,5}$/u", $name)) {
            return ContentType::PERSON;
        }

        return ContentType::ORGANIZATION;
    }
}
