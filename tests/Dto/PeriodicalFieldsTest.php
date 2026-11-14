<?php

declare(strict_types=1);

namespace Survos\DataContracts\Tests\Dto;

use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Dto\Item\DocumentDto;
use Survos\DataContracts\Dto\Item\NewspaperDto;
use Survos\FieldBundle\Attribute\Field;

final class PeriodicalFieldsTest extends TestCase
{
    public function testArticleMetadataIsMappedAndUnknownSourceValuesArePreserved(): void
    {
        $dto = DocumentDto::fromNormalized([
            'issueId' => 'issue-1', 'articleTier' => 'block',
            'recordKind' => 'advertisement', 'unknownSourceValue' => ['raw' => 42],
        ]);
        self::assertSame('issue-1', $dto->issueId);
        self::assertSame('block', $dto->articleTier);
        self::assertSame('advertisement', $dto->recordKind);
        self::assertSame(['unknownSourceValue' => ['raw' => 42]], $dto->unmapped);
    }

    public function testIssueProcessingReceiptIsMappedWithoutLosingNestedValues(): void
    {
        $receipt = ['stage' => 'segmented', 'reviewed' => false, 'pages' => 12];
        $dto = NewspaperDto::fromNormalized([
            'issueKind' => 'annual index', 'issueProcessing' => $receipt,
            'unknownSourceValue' => 'retain for mapping review',
        ]);
        self::assertSame('annual index', $dto->issueKind);
        self::assertSame($receipt, $dto->issueProcessing);
        self::assertSame(['unknownSourceValue' => 'retain for mapping review'], $dto->unmapped);
    }

    public function testIssueLookupDeclaresAnOptionalStorageIndex(): void
    {
        $property = new \ReflectionProperty(DocumentDto::class, 'issueId');
        $field = $property->getAttributes(Field::class)[0]->newInstance();
        self::assertTrue($field->index);
        self::assertFalse((new Field())->index);
    }
}
