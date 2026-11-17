<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Periodical;

/**
 * One ordered piece of a record's text (headline, byline, paragraph, ...) with the places it came from.
 * This is the shape of each entry of DocumentDto::$segments.
 */
final readonly class SegmentDto
{
    public const string HEADLINE = 'headline';
    public const string SUBTITLE = 'subtitle';
    public const string DECK = 'deck';
    public const string BYLINE = 'byline';
    public const string SUBHEAD = 'subhead';
    public const string PARAGRAPH = 'paragraph';
    /** A raw OCR region published before anything grouped it into an article. */
    public const string OCR_BLOCK = 'ocr-block';
    public const string CAPTION = 'caption';
    public const string FIGURE = 'figure';
    public const array KINDS = [self::HEADLINE, self::SUBTITLE, self::DECK, self::BYLINE, self::SUBHEAD, self::PARAGRAPH, self::OCR_BLOCK, self::CAPTION, self::FIGURE];

    /**
     * @param list<SegmentAnchorDto>     $anchors
     * @param list<array<string, mixed>> $captions caption id, text, anchors, confidence, association, reviewed
     */
    public function __construct(
        public string $id,
        public string $kind,
        public int $order,
        public array $anchors,
        /** Null when the record's only segment is the record's whole text: read DocumentDto::$ocrText. */
        public ?string $text = null,
        public ?float $confidence = null,
        public array $captions = [],
    ) {
        if ('' === $id || $order < 0 || !in_array($kind, self::KINDS, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid segment "%s" (kind "%s", order %d).', $id, $kind, $order));
        }
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) $data['id'], (string) $data['kind'], (int) $data['order'],
            array_map(SegmentAnchorDto::fromArray(...), array_values($data['anchors'] ?? [])),
            $data['text'] ?? null, isset($data['confidence']) ? (float) $data['confidence'] : null,
            array_values($data['captions'] ?? []),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id, 'kind' => $this->kind, 'order' => $this->order, 'text' => $this->text,
            'anchors' => array_map(static fn (SegmentAnchorDto $a): array => $a->toArray(), $this->anchors),
            'captions' => $this->captions, 'confidence' => $this->confidence,
        ], static fn (mixed $v): bool => null !== $v && [] !== $v) + ['anchors' => []];
    }
}
