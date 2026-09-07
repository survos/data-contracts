<?php
declare(strict_types=1);

namespace Survos\DataContracts\Util;

use InvalidArgumentException;

use function base64_encode;
use function rtrim;
use function strtr;
use function trim;

/**
 * The imgproxy-style media key, and the ONLY way to derive one.
 *
 * Never hand-roll this encoding. It is URL-safe base64 (`+/` → `-_`, padding stripped) and it is
 * REVERSIBLE via {@see stringFromEncoded()} — deliberately, because it is what lets a resize URL
 * carry its own source so rendering a thumbnail needs no database lookup. A second, subtly
 * different encoding somewhere would produce URLs that resolve to nothing, or worse, to the wrong
 * image.
 *
 * NOT the asset id. {@see MediaIdentity::idFromOriginalUrl()} is a one-way xxh3 hash used as the
 * primary key both sides agree on. Same URL, two different values, each with its own job:
 *
 *   keyFromString()          → reversible, for URLs
 *   idFromOriginalUrl()      → one-way, for keys
 *   archivePathFromKey()     → uses BOTH: hashes the reversible key for the orig/aa/bb prefix
 *
 * Lives in data-contracts so mediary and its clients derive identical values without either
 * depending on the other's code.
 */
final class MediaKeyService
{
    static public function keyFromString(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            throw new InvalidArgumentException('Value must not be empty.');
        }

        return rtrim(
            strtr(base64_encode($value), '+/', '-_'),
            '='
        );
    }

    static public function stringFromEncoded(string $encoded): string
    {
        $encoded = strtr($encoded, '-_', '+/');
        $encoded = str_pad($encoded, (int) ceil(strlen($encoded) / 4) * 4, '=');

        $decoded = base64_decode($encoded, true);

        if ($decoded === false) {
            throw new InvalidArgumentException('Invalid base64 encoding.');
        }

        return $decoded;
    }

    public static function archivePathFromKey(
        string $key,
        string $extension,
        string $prefix = 'o',
    ): string {
        if ($key === '') {
            throw new InvalidArgumentException('Media key must not be empty.');
        }

        $hash = hash('xxh3', $key);
        $aa = $hash[0];
        $bb = substr($hash, 1, 2);

        return sprintf(
            '%s/%s/%s/%s.%s',
            trim($prefix, '/'),
            $aa,
            $bb,
            $key,
            $extension
        );
    }
    public static function archivePathFromUrl(
        string $url,
        string $extension,
        string $prefix = 'o',
    ): string {
        return self::archivePathFromKey(self::keyFromString($url), $extension, $prefix);
    }

    public static function extensionFromMime(string $mime): string
    {
        $extensions = \Symfony\Component\Mime\MimeTypes::getDefault()->getExtensions($mime);
        $extension = $extensions[0] ?? null;

        if ($extension === null) {
            throw new InvalidArgumentException(sprintf(
                'Cannot determine file extension for MIME type "%s".',
                $mime
            ));
        }

        return $extension;
    }

}
