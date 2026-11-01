<?php

declare(strict_types=1);

namespace Survos\DataContracts\Tests\Authority;

use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Dto\Authority\AuthorityReference;
use Survos\DataContracts\Vocabulary\Authority;

final class AuthorityReferenceTest extends TestCase
{
    public function testReferencesRoundTripWithoutCopyingAuthorityData(): void
    {
        foreach ([new AuthorityReference(Authority::GEONAMES, '5243559'),
            new AuthorityReference(Authority::GEOBOUNDARIES, 'shape123', Authority::BOUNDARY, 'USA', 'release-1'),
            new AuthorityReference(Authority::WIKIDATA, 'Q123')] as $reference) {
            $data = json_decode(json_encode($reference, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);
            self::assertEquals($reference, AuthorityReference::fromArray($data));
            self::assertArrayNotHasKey('geometry', $data);
            self::assertArrayNotHasKey('url', $data);
        }
    }

    public function testBoundaryCannotPretendToBePlaceIdentity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new AuthorityReference(Authority::GEOBOUNDARIES, 'shape123');
    }

    public function testRejectsMalformedWikidataIdentifier(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new AuthorityReference(Authority::WIKIDATA, 'https://untrusted.test');
    }

    public function testRequiresIdentifier(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        AuthorityReference::fromArray(['authorityType' => Authority::GEONAMES]);
    }
}
