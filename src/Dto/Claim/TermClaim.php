<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Claim;

/**
 * A controlled-vocabulary term asserted by AI analysis.
 *
 * Used for subjects, people, places, organisations — any predicate whose
 * value is a named entity with provenance.
 *
 * term → Claim::value, confidence → Claim::confidence (0–100), basis → Claim::basis.
 */
readonly class TermClaim implements \JsonSerializable
{
    public function __construct(
        public string  $term,
        public ?int    $confidence = null,
        public ?string $basis      = null,
    ) {}

    public static function from(mixed $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        if ($value instanceof \stdClass) {
            $value = (array) $value;
        }

        if (is_array($value)) {
            return new self(
                term:       (string) ($value['term'] ?? $value['name'] ?? $value['value'] ?? ''),
                confidence: isset($value['confidence']) ? (int) $value['confidence'] : null,
                basis:      isset($value['basis'])      ? (string) $value['basis']    : null,
            );
        }

        return new self(term: (string) $value);
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'term'       => $this->term,
            'confidence' => $this->confidence,
            'basis'      => $this->basis,
        ], static fn($v) => $v !== null);
    }
}
