<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

use ReflectionClass;
use Survos\DataContracts\Attribute\VocabTerm;

/**
 * The "this term set is fed by these item fields" map — DERIVED, not hand-authored, by reflecting the
 * #[VocabTerm(termSet: true, sourceFields: …)] declarations on MuseumVocab. Those attributes are the
 * single source of truth; this just reads them so both the term extractor (writes term.jsonl) and
 * folio rendering (resolves an item's Terms) agree on what a term set is and which fields feed it.
 *
 * @see \Survos\DataContracts\Attribute\VocabTerm
 */
final class TermSetBinding
{
    /** @var array<string, list<string>>|null setCode => source fields (the code itself included) */
    private static ?array $cache = null;

    /** @return array<string, list<string>> termSet code => normalized fields whose values feed it */
    public static function fields(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $map = [];
        foreach ((new ReflectionClass(MuseumVocab::class))->getReflectionConstants() as $const) {
            foreach ($const->getAttributes(VocabTerm::class) as $attr) {
                $meta = $attr->newInstance();
                if (!$meta->termSet) {
                    continue;
                }
                $code = (string) $const->getValue();
                // The code is always an implicit source (museum-coded rows are keyed by it),
                // plus any declared ItemField aliases (subjects, creator, …).
                $map[$code] = array_values(array_unique([$code, ...$meta->sourceFields]));
            }
        }

        return self::$cache = $map;
    }
}
