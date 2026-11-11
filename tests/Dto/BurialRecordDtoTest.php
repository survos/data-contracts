<?php

declare(strict_types=1);

namespace Survos\DataContracts\Tests\Dto;

use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Dto\Item\BurialRecordDto;
use Survos\DataContracts\Metadata\ContentType;
use Survos\DataContracts\Vocabulary\ItemField;

final class BurialRecordDtoTest extends TestCase
{
    public function testStructuredFieldsHydrateAndKeepGraveIdentifiers(): void
    {
        $dto = BurialRecordDto::fromNormalized([
            ItemField::ID => '10000', ItemField::CONTENT_TYPE => ContentType::BURIAL_RECORD,
            ItemField::CEMETERY_NAME => 'Arlington National Cemetery',
            ItemField::INTERMENT_DATE => '2004-09-03', ItemField::INTERMENT_TIME => '13:00',
            ItemField::SECTION => '54', ItemField::GRAVE => '003811-A',
            ItemField::LOCATION_ID => '54:_:003811-A:_:_:_',
            ItemField::SERVICE_BRANCH => 'USA', ItemField::MILITARY_RANK => 'SGM',
            ItemField::RELATIONSHIP => 'Veteran (Self)',
        ]);
        self::assertSame('003811-A', $dto->grave);
        self::assertSame('2004-09-03', $dto->intermentDate);
        self::assertSame('13:00', $dto->intermentTime);
        self::assertSame('SGM', $dto->militaryRank);
        self::assertSame('Veteran (Self)', $dto->relationship);
        self::assertNull($dto->description);
        self::assertSame(BurialRecordDto::class, ContentType::dtoClass(ContentType::BURIAL_RECORD));
    }
}
