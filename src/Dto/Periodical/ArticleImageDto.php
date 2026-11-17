<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Periodical;

/** An image published with a digital article. The shape of each entry of DocumentDto::$articleImages. */
final readonly class ArticleImageDto
{
    public function __construct(
        public string $url,
        public ?string $caption = null,
        public ?string $credit = null,
        public ?int $width = null,
        public ?int $height = null,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self((string) $data['url'], $data['caption'] ?? null, $data['credit'] ?? null,
            isset($data['width']) ? (int) $data['width'] : null, isset($data['height']) ? (int) $data['height'] : null);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter(get_object_vars($this), static fn (mixed $v): bool => null !== $v);
    }
}
