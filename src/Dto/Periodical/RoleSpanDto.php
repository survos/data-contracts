<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Periodical;

/** The part a block (or a line range of it) plays in a group: headline, byline, body, ... */
final readonly class RoleSpanDto
{
    public function __construct(
        public string $role,
        /** Id of the page-layout block. */
        public string $id,
        public ?int $start = null,
        public ?int $end = null,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self((string) $data['role'], (string) $data['id'],
            isset($data['start']) ? (int) $data['start'] : null, isset($data['end']) ? (int) $data['end'] : null);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter(get_object_vars($this), static fn (mixed $v): bool => null !== $v);
    }
}
