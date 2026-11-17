<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Periodical;

/**
 * Where a segment came from: a box on a scanned page, or, for a born-digital article, a byte range in a text field.
 */
final readonly class SegmentAnchorDto
{
    /** @param array{0:int,1:int,2:int,3:int}|null $box x, y, width, height in page-image pixels */
    public function __construct(
        /** 0-based page index within the issue. */
        public ?int $page = null,
        public ?array $box = null,
        /** The page number as printed, when known. */
        public ?string $printed = null,
        public ?string $sourceBlockId = null,
        /** Name of the text field a digital segment cites, with its byte range. */
        public ?string $field = null,
        public ?int $from = null,
        public ?int $to = null,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['page']) ? (int) $data['page'] : null, $data['box'] ?? null,
            isset($data['printed']) ? (string) $data['printed'] : null, $data['sourceBlockId'] ?? null,
            $data['field'] ?? null, isset($data['from']) ? (int) $data['from'] : null, isset($data['to']) ? (int) $data['to'] : null,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter(get_object_vars($this), static fn (mixed $v): bool => null !== $v);
    }
}
