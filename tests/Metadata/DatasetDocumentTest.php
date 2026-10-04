<?php

declare(strict_types=1);
namespace Survos\DataContracts\Tests\Metadata;

use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Metadata\DatasetDocument;
use Survos\DataContracts\Metadata\DatasetMetadata;

final class DatasetDocumentTest extends TestCase
{
    public function testWritersOverridesAndOriginSurviveRefresh(): void
    {
        $identity = ['datasetKey' => 'loc/1', 'aggregator' => 'loc'];
        $title = $identity + ['description' => 'LOC description', 'extras' => ['titleRecord' => ['source' => 'https://loc.gov/item/1', 'essay' => 'Essay']]];
        $doc = DatasetDocument::update([], $title, 'loc.title');
        $coverage = $identity + ['description' => null, 'extras' => ['coverage' => ['pages' => 200]]];
        $doc = DatasetDocument::update($doc, $coverage, 'loc.coverage', ['description' => 'My correction']);
        self::assertSame('Essay', $doc['dataset']['extras']['titleRecord']['essay']);
        self::assertSame(200, $doc['dataset']['extras']['coverage']['pages']);
        self::assertSame('My correction', $doc['dataset']['description']);
        self::assertSame($doc, DatasetDocument::update($doc, $coverage, 'loc.coverage', ['description' => 'My correction']));
        $doc = DatasetDocument::update($doc, $title, 'loc.title');
        self::assertSame('LOC description', $doc['dataset']['description'], 'removing override reveals generated value');
        self::assertSame('https://loc.gov/item/1', DatasetMetadata::properties($doc['dataset'])['title.essay']->provenance['sourceRef']);
        $doc = DatasetDocument::update($doc, $identity, 'loc.title');
        self::assertArrayNotHasKey('titleRecord', $doc['dataset']['extras']);
        self::assertSame(200, $doc['dataset']['extras']['coverage']['pages']);
    }
    public function testParentReplacementCannotEraseAnotherWritersFields(): void
    {
        $identity = ['datasetKey' => 'loc/1', 'aggregator' => 'loc'];
        $doc = DatasetDocument::update([], $identity + ['extras' => ['coverage' => ['pages' => 200]]], 'coverage');
        $doc = DatasetDocument::update($doc, $identity + ['extras' => []], 'title');
        self::assertSame(200, $doc['dataset']['extras']['coverage']['pages']);
        $doc = DatasetDocument::update($doc, $identity + ['extras' => ['coverage' => []]], 'title');
        self::assertSame(200, $doc['dataset']['extras']['coverage']['pages']);
    }

}
