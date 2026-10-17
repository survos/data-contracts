<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Link;

/**
 * One row of a normalize stage's link.jsonl — a typed edge subject→object.
 *
 * On the wire the type is spelled `predicate` (the link type's code) beside subjectCore/objectCore;
 * {@see typeKey()} recombines them into the {@see LinkTypeRow::key()} the edge belongs to. Anything
 * else on the line is an extra and is written FLAT at the top level (`ordinal`, `soundId`, `role`,
 * …), which is how folio ingest reads it; `ordinal` is promoted to a typed property because ordered
 * edges (tour stops, issue articles) are common enough to sort on.
 */
final readonly class LinkRow
{
    /** The wire keys that are not extras. */
    public const array KEYS = ['predicate', 'subjectCore', 'subjectId', 'objectCore', 'objectId'];

    /** @param array<string,mixed> $extras flat, scalar-or-array values; nulls are dropped on write */
    public function __construct(
        public string $predicate,
        public string $subjectCore,
        public string $subjectId,
        public string $objectCore,
        public string $objectId,
        public ?int $ordinal = null,
        public array $extras = [],
    ) {
    }

    /** @param array<string,mixed> $extras */
    public static function of(LinkTypeRow $type, string $subjectId, string $objectId, ?int $ordinal = null, array $extras = []): self
    {
        return new self($type->code, $type->subjectCore, $subjectId, $type->objectCore, $objectId, $ordinal, $extras);
    }

    public function typeKey(): string
    {
        return LinkTypeRow::keyOf($this->subjectCore, $this->predicate, $this->objectCore);
    }

    /** @param array<string,mixed> $row a decoded link.jsonl line */
    public static function fromArray(array $row): self
    {
        foreach (self::KEYS as $key) {
            if (!\is_scalar($row[$key] ?? null) || '' === (string) $row[$key]) {
                throw new \InvalidArgumentException(sprintf('link row is missing "%s": %s', $key, json_encode($row)));
            }
        }
        $extras = array_diff_key($row, array_flip([...self::KEYS, 'ordinal']));

        return new self(
            predicate: (string) $row['predicate'],
            subjectCore: (string) $row['subjectCore'],
            subjectId: (string) $row['subjectId'],
            objectCore: (string) $row['objectCore'],
            objectId: (string) $row['objectId'],
            ordinal: is_numeric($row['ordinal'] ?? null) ? (int) $row['ordinal'] : null,
            extras: $extras,
        );
    }

    /** @return array<string,mixed> the link.jsonl line: the five keys, then ordinal, then the extras */
    public function toArray(): array
    {
        $extras = array_diff_key($this->extras, array_flip([...self::KEYS, 'ordinal']));

        return array_filter([
            'predicate' => $this->predicate,
            'subjectCore' => $this->subjectCore,
            'subjectId' => $this->subjectId,
            'objectCore' => $this->objectCore,
            'objectId' => $this->objectId,
            'ordinal' => $this->ordinal,
            ...$extras,
        ], static fn (mixed $v): bool => null !== $v);
    }
}
