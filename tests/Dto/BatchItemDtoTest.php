<?php

declare(strict_types=1);

namespace Survos\DataContracts\Tests\Dto;

use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Dto\BatchItemDto;
use Survos\DataContracts\Vocabulary\MediaSyncKeys;

final class BatchItemDtoTest extends TestCase
{
    public function testArchiveIsTheDefaultAndDoesNotTravel(): void
    {
        $item = new BatchItemDto(dataset: 'mus/x');

        self::assertTrue($item->archive);
        self::assertArrayNotHasKey(MediaSyncKeys::ARCHIVE, $item->toArray());
        self::assertTrue(BatchItemDto::fromArray($item->toArray())->archive);
    }

    public function testReferenceOnlyRoundTripsAndLeavesTheHints(): void
    {
        $wire = (new BatchItemDto(dataset: 'nara/coll_x', sourceMeta: ['content_type' => 'photograph'], archive: false))->toArray();

        self::assertFalse($wire[MediaSyncKeys::ARCHIVE]);
        $parsed = BatchItemDto::fromArray($wire);
        self::assertFalse($parsed->archive);
        self::assertSame(['content_type' => 'photograph'], $parsed->sourceMeta);
    }

    /** Only a real false opts out: a stray value must not silently stop archiving. */
    public function testOnlyFalseOptsOut(): void
    {
        foreach (['false', 0, '0', null, 'no'] as $value) {
            self::assertTrue(BatchItemDto::fromArray([MediaSyncKeys::ARCHIVE => $value])->archive, var_export($value, true));
        }
    }
}
