<?php
declare(strict_types=1);

namespace Survos\DataContracts\Util;

/**
 * The asset id, and the ONLY way to derive one.
 *
 * Never compute an id another way — not a different hash, not a substring, not a slug, and not
 * "close enough" canonicalisation. This value is the primary key that a client's Media row and
 * mediary's Asset row use to mean the same image, computed independently on both sides and never
 * exchanged during registration. Two derivations that disagree do not raise an error anywhere:
 * mediary answers about ids the client never asked about, applyBatch() drops every response into
 * its "no local row" branch, and the media sit at status=new with no s3Url forever. That is a real
 * outage that cost a day to find (see DatasetMediaDispatcher), and it looked like a clean run.
 *
 * That is also why this lives in data-contracts rather than in media-bundle or mediary: neither
 * side depends on the other, so neither can quietly fork the algorithm.
 *
 * NOT the imgproxy key. {@see MediaKeyService::keyFromString()} is a REVERSIBLE URL-safe base64
 * encoding used to build resize URLs that carry their own source. This one is a one-way hash used
 * as a database key. They are different values for the same URL, on purpose; do not substitute one
 * for the other.
 */
final class MediaIdentity
{
    /**
     * Stable identifier derived from the canonical original URL: xxh3, 16 lowercase hex chars
     * (8 bytes), e.g. "6b57e9c2a91759a6". Not reversible — the URL cannot be recovered from it.
     */
    public static function idFromOriginalUrl(string $url): string
    {
        $canonical = self::canonicalizeUrl($url);

        return hash('xxh3', $canonical, false);
    }

    private static function canonicalizeUrl(string $url): string
    {
        return trim($url);
    }
}
