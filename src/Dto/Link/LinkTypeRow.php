<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Link;

/**
 * One row of a normalize stage's linkType.jsonl — the declaration of a typed edge between two cores.
 *
 * A link type is identified by (subjectCore, code, objectCore), never by its code alone: two types
 * may share a code (`story:has_stop:stop` and `tour:has_stop:obj` during the tour-model migration),
 * so everything that indexes or owns link types keys by {@see key()}.
 */
final readonly class LinkTypeRow
{
    public function __construct(
        public string $code,
        public string $subjectCore,
        public string $objectCore,
        public ?string $forwardLabel = null,
        public ?string $reverseCode = null,
        public ?string $reverseLabel = null,
        public ?bool $multiple = null,
    ) {
    }

    public static function keyOf(string $subjectCore, string $code, string $objectCore): string
    {
        return $subjectCore.':'.$code.':'.$objectCore;
    }

    public function key(): string
    {
        return self::keyOf($this->subjectCore, $this->code, $this->objectCore);
    }

    /** @param array<string,mixed> $row a decoded linkType.jsonl line */
    public static function fromArray(array $row): self
    {
        return new self(
            code: self::required($row, 'code'),
            subjectCore: self::required($row, 'subjectCore'),
            objectCore: self::required($row, 'objectCore'),
            forwardLabel: self::optional($row, 'forwardLabel'),
            reverseCode: self::optional($row, 'reverseCode'),
            reverseLabel: self::optional($row, 'reverseLabel'),
            multiple: isset($row['multiple']) ? (bool) $row['multiple'] : null,
        );
    }

    /** @return array<string,string|bool> the linkType.jsonl line; unset (null) keys are omitted */
    public function toArray(): array
    {
        return array_filter([
            'code' => $this->code,
            'subjectCore' => $this->subjectCore,
            'objectCore' => $this->objectCore,
            'forwardLabel' => $this->forwardLabel,
            'reverseCode' => $this->reverseCode,
            'reverseLabel' => $this->reverseLabel,
            'multiple' => $this->multiple,
        ], static fn (mixed $v): bool => null !== $v);
    }

    /** @param array<string,mixed> $row */
    private static function required(array $row, string $key): string
    {
        $value = $row[$key] ?? null;
        if (!\is_scalar($value) || '' === (string) $value) {
            throw new \InvalidArgumentException(sprintf('linkType row is missing "%s": %s', $key, json_encode($row)));
        }

        return (string) $value;
    }

    /** @param array<string,mixed> $row */
    private static function optional(array $row, string $key): ?string
    {
        return \is_scalar($row[$key] ?? null) ? (string) $row[$key] : null;
    }
}
