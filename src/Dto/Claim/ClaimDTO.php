<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Claim;

/**
 * A flat assertion for transport: predicate (compact IRI) + value, with optional
 * provenance. The wire shape for source and AI claims — `predicate` names the
 * assertion type ('dcterms:title', 'dcterms:subject', …), `value` is the asserted
 * value. Multi-valued fields are expressed as multiple ClaimDTOs.
 *
 * Maps onto the claim store's (predicate, value, confidence, basis); the consumer
 * stamps the persistence context (scope, subjectType, subjectId, source) at ingest.
 *
 * Distinct from {@see TermClaim}, which is value-only (the predicate is implied by
 * the map key it sits under). Use ClaimDTO when each entry carries its own predicate.
 *
 * jsonSerialize() is kept deliberately: these travel via raw json_encode() — a
 * Doctrine JSON column (BaseMedia.rawData) and HttpClient's `json` option — not the
 * Symfony Serializer, so JsonSerializable is what shapes the output (and drops nulls).
 */
readonly class ClaimDTO implements \JsonSerializable
{
    public function __construct(
        public string  $predicate,
        public mixed   $value,
        public ?int    $confidence = null,
        public ?string $basis      = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'predicate'  => $this->predicate,
            'value'      => $this->value,
            'confidence' => $this->confidence,
            'basis'      => $this->basis,
        ], static fn ($v): bool => $v !== null);
    }
}
