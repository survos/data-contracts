<?php

declare(strict_types=1);

namespace Survos\DataContracts\Tests\Link;

use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Vocabulary\Core;
use Survos\DataContracts\Vocabulary\Narrative;
use Survos\DataContracts\Vocabulary\Panorama;

final class PanoramaTest extends TestCase
{
    public function testPanoramasAndPhotographsShareTheTourObjectContract(): void
    {
        self::assertSame(Core::OBJECT, Narrative::hasStop()->objectCore);
        foreach ([Panorama::depicts(), Panorama::connectsTo()] as $type) {
            self::assertSame(Core::OBJECT, $type->subjectCore);
            self::assertSame(Core::OBJECT, $type->objectCore);
        }
    }
}
