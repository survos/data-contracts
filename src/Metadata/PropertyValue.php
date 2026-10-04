<?php

declare(strict_types=1);

namespace Survos\DataContracts\Metadata;

/** Ownership controls replacement; provenance records origin and survives materialization. */
final readonly class PropertyValue
{
    public function __construct(
        public mixed $value,
        public string $source,
        public string $owner,
        public string $updatedAt,
        public array $provenance = [],
    ) {
        if (!in_array($source, ['meta', 'build', 'human', 'import'], true) || $owner === '') {
            throw new \InvalidArgumentException('Metadata requires a valid source and nonempty owner.');
        }
        new \DateTimeImmutable($updatedAt);
        json_encode([$value, $provenance], JSON_THROW_ON_ERROR);
    }

    public static function create(mixed $value, string $source, string $owner, array $provenance = []): self
    {
        return new self($value, $source, $owner, gmdate('Y-m-d\TH:i:s\Z'), $provenance);
    }

    public static function fromArray(array $data): self
    {
        foreach (['value', 'source', 'owner', 'updatedAt'] as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \InvalidArgumentException('Missing property field: '.$key);
            }
        }
        return new self($data['value'], $data['source'], $data['owner'], $data['updatedAt'], $data['provenance'] ?? []);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
