<?php

declare(strict_types=1);

namespace Survos\DataContracts\Tests\Util;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Util\ImageUrl;
use Survos\DataContracts\Util\ImageUrlVerdict;

final class ImageUrlTest extends TestCase
{
    public static function urls(): iterable
    {
        yield 'jpg' => ['https://example.org/a/b.jpg', ImageUrlVerdict::Image];
        yield 'iiif base' => ['https://iiif.example.org/iiif/2/abc', ImageUrlVerdict::Unverifiable];
        yield 'pdf' => ['https://example.org/doc.pdf', ImageUrlVerdict::Document];
        yield 'loc mp3' => ['https://tile.loc.gov/storage-services/service/afc/afc1941018/afc1941018_afs05091/afc1941018_afs05091b.mp3', ImageUrlVerdict::Audio];
        yield 'wav with query' => ['https://example.org/rec.WAV?dl=1', ImageUrlVerdict::Audio];
        yield 'viewer xml' => ['https://example.org/viewer.xml', ImageUrlVerdict::NotAnImage];
        yield 'empty' => ['  ', ImageUrlVerdict::Empty];
    }

    #[DataProvider('urls')]
    public function testClassify(string $url, ImageUrlVerdict $expected): void
    {
        self::assertSame($expected, ImageUrl::classify($url));
    }

    public function testAudioIsNeverSentToImgproxy(): void
    {
        $mp3 = 'https://tile.loc.gov/x/interview.mp3';

        self::assertFalse(ImageUrl::classify($mp3)->isRenderable());
        self::assertFalse(ImageUrl::classify($mp3)->isHarvestDefect());
        self::assertFalse(ImageUrl::looksLikeImage($mp3));
    }
}
