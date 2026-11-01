<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Authority;

use Survos\DataContracts\Vocabulary\Authority;

/** Portable identity/reference only. Hosts own resolution, caching and presentation. */
final readonly class AuthorityReference implements \JsonSerializable
{
    public function __construct(
        public string $authorityType,
        public string $id,
        public string $relation = Authority::SAME_ENTITY,
        public ?string $iso3 = null,
        public ?string $revision = null,
    ) {
        if (!preg_match('/^[a-z][a-z0-9_-]*$/D', $authorityType) || '' === trim($id)) {
            throw new \InvalidArgumentException('Authority type and nonempty identifier are required.');
        }
        if (!in_array($relation, [Authority::SAME_ENTITY, Authority::BOUNDARY], true)) {
            throw new \InvalidArgumentException('Unknown authority relation.');
        }
        if (null !== $iso3 && !preg_match('/^[A-Z]{3}$/D', $iso3)) {
            throw new \InvalidArgumentException('Use an ISO3 country code.');
        }
        if (Authority::GEONAMES === $authorityType && !preg_match('/^[1-9][0-9]*$/D', $id)) {
            throw new \InvalidArgumentException('GeoNames identifiers are positive integers.');
        }
        if (Authority::WIKIDATA === $authorityType && !preg_match('/^Q[1-9][0-9]*$/D', $id)) {
            throw new \InvalidArgumentException('Wikidata entity identifiers use Q numbers.');
        }
        if (Authority::GEOBOUNDARIES === $authorityType && (null === $iso3 || Authority::BOUNDARY !== $relation)) {
            throw new \InvalidArgumentException('A boundary reference requires ISO3 and the boundary relation.');
        }
        if (null !== $revision && '' === trim($revision)) {
            throw new \InvalidArgumentException('An optional revision cannot be empty.');
        }
    }

    public static function fromArray(array $data): self
    {
        foreach (['authorityType', 'id'] as $required) {
            if (!isset($data[$required]) || !is_string($data[$required])) {
                throw new \InvalidArgumentException('Authority reference requires string '.$required);
            }
        }

        return new self($data['authorityType'], $data['id'], $data['relation'] ?? Authority::SAME_ENTITY,
            $data['iso3'] ?? null, $data['revision'] ?? null);
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), static fn (mixed $value): bool => null !== $value);
    }
}
