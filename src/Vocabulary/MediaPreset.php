<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/**
 * The named image sizes mediary and its clients must agree on.
 *
 * Shared vocabulary, not client behaviour. A client asks for `small`; mediary resolves that name
 * to concrete imgproxy parameters. If the two sides hold separate copies they can disagree about
 * what a name means — a client requesting a size the server does not know, or worse, both knowing
 * `small` and rendering it differently. That is the same failure the media identity/key classes
 * were moved here to prevent.
 *
 * Values only. URL construction and signing stay with whoever does the rendering
 * ({@see \Survos\MediaBundle\Service\MediaUrlGenerator} on the client side); this class holds no
 * behaviour so both sides can depend on it without either depending on the other.
 */
final class MediaPreset
{
    public const SMALL = 'small';
    public const MEDIUM = 'medium';
    public const LARGE = 'large';
    public const AI = 'ai';
    public const THUMB = 'thumb';

    /**
     * @var array<string, array{size: array{int, int}, resize: string, quality: int, format: string, dpr: list<int>}>
     */
    public const PRESETS = [
        self::SMALL => [
            'size' => [192, 192],
            'resize' => 'fit',
            'quality' => 80,
            'format' => 'jpg',
            'dpr' => [1],
        ],
        self::MEDIUM => [
            'size' => [600, 400],
            'resize' => 'fit',
            'quality' => 85,
            'format' => 'jpg',
            'dpr' => [1, 2],
        ],
        self::LARGE => [
            'size' => [1200, 800],
            'resize' => 'fit',
            'quality' => 85,
            'format' => 'jpg',
            'dpr' => [1, 2],
        ],
        self::AI => [
            'size' => [512, 512],
            'resize' => 'fit',
            'quality' => 85,
            'format' => 'jpg',
            'dpr' => [1],
        ],
        self::THUMB => [
            'size' => [300, 300],
            'resize' => 'fit',
            'quality' => 80,
            'format' => 'jpg',
            'dpr' => [1],
        ],
    ];

    private function __construct()
    {
    }
}
