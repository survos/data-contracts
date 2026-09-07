<?php

declare(strict_types=1);

namespace Survos\DataContracts\Tests\Util;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Util\MediaIdentity;
use Survos\DataContracts\Util\MediaKeyService;

/**
 * Pins BOTH derivations from a URL, and pins that they stay different.
 *
 * They were conflated in the README (the asset id documented as reversible base64), and the class
 * that uses the reversible one shipped a fatal reference to a moved class. Both encodings are
 * cross-process contracts — mediary and its clients each compute them independently and never
 * exchange them — so a change here that looks harmless desynchronises two databases with no error
 * anywhere. These assertions are the alarm.
 */
#[CoversClass(MediaKeyService::class)]
#[CoversClass(MediaIdentity::class)]
final class MediaKeyAndIdentityTest extends TestCase
{
    private const URL = 'https://openaccess-cdn.clevelandart.org/2020.113/2020.113_web.jpg';

    #[Test]
    public function assetIdIsSixteenLowercaseHexChars(): void
    {
        $id = MediaIdentity::idFromOriginalUrl(self::URL);

        // 16, not 32 and not "16 bytes" -- both were claimed in stacked docblocks on this method.
        self::assertMatchesRegularExpression('/^[0-9a-f]{16}$/', $id);
    }

    #[Test]
    public function assetIdIsStableAcrossProcesses(): void
    {
        // Hard-coded on purpose. Computing the expectation with the same call would pass even if
        // the algorithm changed, which is the only failure this test exists to catch: mediary
        // derives this id from the same URL in a different process and must agree.
        self::assertSame('6b57e9c2a91759a6', MediaIdentity::idFromOriginalUrl('https://example.com/image.jpg'));
    }

    #[Test]
    public function assetIdIgnoresSurroundingWhitespaceOnly(): void
    {
        self::assertSame(
            MediaIdentity::idFromOriginalUrl(self::URL),
            MediaIdentity::idFromOriginalUrl('  ' . self::URL . "\n"),
        );
    }

    #[Test]
    public function imgproxyKeyRoundTrips(): void
    {
        $key = MediaKeyService::keyFromString(self::URL);

        // The whole point of this encoding: a resize URL carries its own source, so rendering a
        // thumbnail needs no database lookup.
        self::assertSame(self::URL, MediaKeyService::stringFromEncoded($key));
    }

    #[Test]
    public function imgproxyKeyIsUrlSafeAndUnpadded(): void
    {
        $key = MediaKeyService::keyFromString(self::URL);

        self::assertStringNotContainsString('+', $key);
        self::assertStringNotContainsString('/', $key);
        self::assertStringNotContainsString('=', $key);
    }

    #[Test]
    public function theTwoDerivationsAreNotInterchangeable(): void
    {
        // Guards the mistake this test file was written after: treating the reversible key and the
        // one-way id as the same value.
        self::assertNotSame(
            MediaKeyService::keyFromString(self::URL),
            MediaIdentity::idFromOriginalUrl(self::URL),
        );
    }

    #[Test]
    public function archivePathBucketsOnTheHashedKeyAndKeepsTheKeyAsTheFilename(): void
    {
        $key  = MediaKeyService::keyFromString(self::URL);
        $path = MediaKeyService::archivePathFromKey($key, 'jpg');

        // Two things worth pinning, because neither is what the name suggests:
        //
        // 1. The buckets are ONE hex char then TWO ($hash[0], then substr($hash, 1, 2)), so the
        //    fan-out is 16 x 256, not the symmetric 256 x 256 an `aa/bb` reading implies.
        // 2. The filename is the base64 KEY itself, not a hash -- so the stored path is still
        //    reversible back to the source URL.
        //
        // This is ALSO not the scheme mediary uses for archived originals, which are
        // orig/<2>/<2>/<long hex>.<ext>. Two different layouts; do not assume this function
        // produces the other one.
        self::assertMatchesRegularExpression('#^o/[0-9a-f]/[0-9a-f]{2}/#', $path);
        self::assertStringEndsWith('/' . $key . '.jpg', $path);
        self::assertSame(self::URL, MediaKeyService::stringFromEncoded(basename($path, '.jpg')));
    }

    #[Test]
    public function emptyKeyIsRejectedRatherThanEncoded(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MediaKeyService::keyFromString('   ');
    }
}
