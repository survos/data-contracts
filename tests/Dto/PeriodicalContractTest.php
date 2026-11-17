<?php

declare(strict_types=1);

namespace Survos\DataContracts\Tests\Dto;

use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Dto\Item\DocumentDto;
use Survos\DataContracts\Dto\Item\IssueGroupDto;
use Survos\DataContracts\Dto\Item\NewspaperDto;
use Survos\DataContracts\Dto\Periodical\SegmentDto;
use Survos\DataContracts\Metadata\ContentType;
use Survos\DataContracts\Vocabulary\ItemField;

/** A periodical field is a declared DTO property with one constant; none of it may fall into the unmapped bucket. */
final class PeriodicalContractTest extends TestCase
{
    public function testNoTwoFieldConstantsDifferOnlyByCase(): void
    {
        $seen = [];
        foreach ((new \ReflectionClass(ItemField::class))->getConstants() as $name => $value) {
            if (!is_string($value)) {
                continue;
            }
            $key = strtolower($value);
            self::assertTrue(!isset($seen[$key]) || $seen[$key][1] === $value,
                sprintf('ItemField::%s ("%s") and ItemField::%s ("%s") are case variants of one field.', $name, $value, $seen[$key][0] ?? '', $seen[$key][1] ?? ''));
            $seen[$key] ??= [$name, $value];
        }
    }

    public function testArticleFieldsAreMapped(): void
    {
        $segment = ['id' => 'a-s001', 'kind' => SegmentDto::HEADLINE, 'order' => 1, 'text' => 'Fire', 'anchors' => [['page' => 0, 'box' => [1, 2, 3, 4]]]];
        $dto = DocumentDto::fromNormalized([
            ItemField::SOURCE_KIND => 'digital', ItemField::SEGMENTS => [$segment], ItemField::SEGMENTATION_BASIS => 'publisher',
            ItemField::OCR_SOURCE => 'vendor-alto', ItemField::OCR_MODEL => 'tesseract', ItemField::PAGE_START => 2, ItemField::PAGE_END => 3,
            ItemField::ARTICLE_IMAGES => [['url' => 'https://example.org/a.jpg', 'caption' => 'A']],
        ]);
        self::assertSame([], $dto->unmapped);
        self::assertSame('digital', $dto->sourceKind);
        self::assertSame(2, $dto->pageStart);
        self::assertSame($segment, SegmentDto::fromArray($dto->segments[0])->toArray());
    }

    public function testIssueFieldsAreMapped(): void
    {
        $dto = NewspaperDto::fromNormalized([
            ItemField::ISSUE_TIER => 'enhanced', ItemField::IA_IDENTIFIER => 'ia-1', ItemField::SOURCE_PDF_URL => 'https://example.org/i.pdf',
            ItemField::PUBLICATION_TITLE => 'The Labor herald', ItemField::OCR_SOURCE => 'loc-alto', ItemField::SOURCE_LABEL => 'Virginia Chronicle', ItemField::PAGE_URL_TEMPLATE => 'https://example.org/p/%d',
            ItemField::GROUPING_MARKING => 'grouped', ItemField::GROUPING_REVISION => 'r2',
        ]);
        self::assertSame([], $dto->unmapped);
        self::assertSame('enhanced', $dto->issueTier);
        self::assertSame('grouped', $dto->groupingMarking);
    }

    public function testAGroupIsItsOwnTypedRecord(): void
    {
        self::assertSame(IssueGroupDto::class, ContentType::dtoClass(ContentType::ISSUE_GROUP));
        $dto = IssueGroupDto::fromNormalized([
            'groupType' => 'article', 'pageIndex' => 0, 'column' => 2, 'basis' => 'headline-span',
            'blockIds' => ['b1', 'b2'], 'roles' => [['role' => 'headline', 'id' => 'b1']], 'evidence' => 'Leading all-caps line inside body block',
        ]);
        self::assertSame([], $dto->unmapped);
        self::assertSame(['b1', 'b2'], $dto->blockIds);
    }

    public function testEveryPeriodicalConstantNamesADeclaredProperty(): void
    {
        $fields = ['ISSUE_ID', 'ISSUE_TIER', 'ISSUE_KIND', 'ISSUE_NUMBER', 'ARTICLE_TIER', 'RECORD_KIND', 'BLOCK_TYPE', 'SOURCE_KIND', 'SEGMENTS',
            'SEGMENTATION_BASIS', 'OCR_TEXT', 'OCR_SOURCE', 'OCR_MODEL', 'PAGE_START', 'PAGE_END', 'ARTICLE_IMAGES', 'IA_IDENTIFIER', 'SOURCE_PDF_URL',
            'PUBLICATION_TITLE', 'HOSTING_ARCHIVE', 'ISSUE_TITLE', 'SOURCE_LABEL', 'PAGE_URL_TEMPLATE', 'GROUPING_MARKING', 'GROUPING_REVISION', 'GROUPING_SCHEMA_VERSION',
            'GROUPING_SOURCE_HASH', 'GROUPING_ALGORITHM_HASH', 'GROUPING_INPUT_HASH', 'GROUPING_UNAVAILABLE', 'EXTRACTION_EVIDENCE', 'PEOPLE', 'PLACES', 'ORGANIZATIONS', 'SOURCE_ARCHIVE', 'MODIFIED_AT'];
        foreach ($fields as $name) {
            $property = constant(ItemField::class.'::'.$name);
            self::assertTrue(property_exists(DocumentDto::class, $property) || property_exists(NewspaperDto::class, $property),
                sprintf('ItemField::%s ("%s") is not a property of DocumentDto or NewspaperDto.', $name, $property));
        }
    }
}
