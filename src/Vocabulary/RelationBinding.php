<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

use ReflectionClass;
use Survos\DataContracts\Attribute\VocabTerm;

/**
 * Sibling of {@see TermSetBinding} for the *relation* half of the model: the vocabularies flagged
 * #[VocabTerm(relation: true)] on MuseumVocab, each becoming its own core (an entity, e.g. `per`)
 * with typed edges obj→<code>. Derived by reflection so the relation producer and any consumer read
 * one declaration. A vocab is either a term set or a relation, never both.
 *
 * @see \Survos\DataContracts\Attribute\VocabTerm
 */
final class RelationBinding
{
    /** @var array<string, array{sourceFields: list<string>, linkType: string, reverseCode: ?string, personName: bool, contentType: string}>|null */
    private static ?array $cache = null;

    /**
     * @return array<string, array{sourceFields: list<string>, linkType: string, reverseCode: ?string, personName: bool, contentType: string}>
     *         entity core code (e.g. 'per') => relation spec
     */
    public static function relations(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $map = [];
        foreach ((new ReflectionClass(MuseumVocab::class))->getReflectionConstants() as $const) {
            foreach ($const->getAttributes(VocabTerm::class) as $attr) {
                $meta = $attr->newInstance();
                if (!$meta->relation || $meta->linkType === null) {
                    continue;
                }
                $code = (string) $const->getValue();
                $map[$code] = [
                    'sourceFields' => array_values(array_unique([$code, ...$meta->sourceFields])),
                    'linkType' => $meta->linkType,
                    'reverseCode' => $meta->reverseCode,
                    'personName' => $meta->personName,
                    // Fallback contentType for a non-personal entity (folio requires one) — the
                    // vocab label, e.g. 'collection'. Personal entities get person/org from PersonName.
                    'contentType' => strtolower($meta->label),
                ];
            }
        }

        return self::$cache = $map;
    }
}
