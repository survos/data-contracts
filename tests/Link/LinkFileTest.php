<?php

declare(strict_types=1);

namespace Survos\DataContracts\Tests\Link;

use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Dto\Item\TourDto;
use Survos\DataContracts\Dto\Link\LinkRow;
use Survos\DataContracts\Dto\Link\LinkTypeRow;
use Survos\DataContracts\Link\LinkFile;
use Survos\DataContracts\Metadata\ContentType;
use Survos\DataContracts\Vocabulary\Narrative;

final class LinkFileTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/linkfile-'.bin2hex(random_bytes(4));
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir.'/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->dir);
    }

    public function testRowsRoundTripTodaysWireKeys(): void
    {
        $type = ['code' => 'has_stop', 'subjectCore' => 'tour', 'objectCore' => 'obj', 'forwardLabel' => 'Has stop', 'reverseCode' => 'stop_of', 'reverseLabel' => 'Stop of', 'multiple' => true];
        self::assertSame($type, LinkTypeRow::fromArray($type)->toArray());
        self::assertSame('tour:has_stop:obj', LinkTypeRow::fromArray($type)->key());

        $link = ['predicate' => 'has_stop', 'subjectCore' => 'tour', 'subjectId' => 't1', 'objectCore' => 'obj', 'objectId' => 'o9', 'ordinal' => 3, 'soundId' => 's4'];
        $row = LinkRow::fromArray($link);
        self::assertSame(3, $row->ordinal);
        self::assertSame(['soundId' => 's4'], $row->extras);
        self::assertSame($link, $row->toArray());
        self::assertSame('tour:has_stop:obj', $row->typeKey());

        $sparse = LinkRow::of(LinkTypeRow::fromArray($type), 't1', 'o9', null, ['soundId' => null]);
        self::assertSame(['predicate' => 'has_stop', 'subjectCore' => 'tour', 'subjectId' => 't1', 'objectCore' => 'obj', 'objectId' => 'o9'], $sparse->toArray());
    }

    public function testTourContentType(): void
    {
        self::assertSame(TourDto::class, ContentType::dtoClass(ContentType::TOUR));
    }

    public function testCanonicalHasStop(): void
    {
        self::assertSame('tour:has_stop:obj', Narrative::hasStop()->key());
    }

    public function testMergeKeepsOthersAndKeysByCoresNotCode(): void
    {
        $this->write('linkType.jsonl', [
            ['code' => 'has_stop', 'subjectCore' => 'story', 'objectCore' => 'stop'],
            ['code' => 'created', 'subjectCore' => 'per', 'objectCore' => 'obj'],
        ]);
        $this->write('link.jsonl', [
            ['predicate' => 'has_stop', 'subjectCore' => 'story', 'subjectId' => 's1', 'objectCore' => 'stop', 'objectId' => 'x1', 'ordinal' => 1],
            ['predicate' => 'created', 'subjectCore' => 'per', 'subjectId' => 'p1', 'objectCore' => 'obj', 'objectId' => 'o1'],
            ['predicate' => 'has_stop', 'subjectCore' => 'tour', 'subjectId' => 'stale', 'objectCore' => 'obj', 'objectId' => 'o0'],
        ]);

        $hasStop = new LinkTypeRow('has_stop', 'tour', 'obj', 'Has stop', 'stop_of', 'Stop of', true);
        $links = [LinkRow::of($hasStop, 't1', 'o1', 1), LinkRow::of($hasStop, 't1', 'o2', 2, ['soundId' => 's2'])];

        for ($run = 0; $run < 2; ++$run) { // idempotent
            self::assertSame(['types' => 1, 'links' => 2], LinkFile::merge($this->dir, [$hasStop], $links));
        }

        self::assertSame(['story:has_stop:stop', 'per:created:obj', 'tour:has_stop:obj'], array_keys(LinkFile::readTypes($this->dir)));
        self::assertSame(
            ['story:has_stop:stop|s1', 'per:created:obj|p1', 'tour:has_stop:obj|t1', 'tour:has_stop:obj|t1'],
            array_map(static fn (LinkRow $l): string => $l->typeKey().'|'.$l->subjectId, [...LinkFile::readLinks($this->dir)]),
        );
    }

    public function testRetireDropsTypesWithoutReplacement(): void
    {
        $this->write('linkType.jsonl', [
            ['code' => 'has_stop', 'subjectCore' => 'story', 'objectCore' => 'stop'],
            ['code' => 'presents', 'subjectCore' => 'stop', 'objectCore' => 'obj'],
        ]);
        $this->write('link.jsonl', [
            ['predicate' => 'has_stop', 'subjectCore' => 'story', 'subjectId' => 's1', 'objectCore' => 'stop', 'objectId' => 'x1'],
            ['predicate' => 'presents', 'subjectCore' => 'stop', 'subjectId' => 'x1', 'objectCore' => 'obj', 'objectId' => 'o1'],
        ]);
        $hasStop = new LinkTypeRow('has_stop', 'tour', 'obj');

        LinkFile::merge($this->dir, [$hasStop], [LinkRow::of($hasStop, 't1', 'o1', 1)], retire: ['story:has_stop:stop', new LinkTypeRow('presents', 'stop', 'obj')]);

        self::assertSame(['tour:has_stop:obj'], array_keys(LinkFile::readTypes($this->dir)));
        self::assertCount(1, [...LinkFile::readLinks($this->dir)]);
    }

    public function testFollowsTheNewestSpellingAndRemovesTheOther(): void
    {
        $this->write('link.jsonl', [['predicate' => 'created', 'subjectCore' => 'per', 'subjectId' => 'old', 'objectCore' => 'obj', 'objectId' => 'o1']]);
        touch($this->dir.'/link.jsonl', time() - 60);
        file_put_contents($this->dir.'/link.jsonl.gz', gzencode(json_encode(['predicate' => 'created', 'subjectCore' => 'per', 'subjectId' => 'new', 'objectCore' => 'obj', 'objectId' => 'o1'])."\n"));
        file_put_contents($this->dir.'/link.jsonl.gz.idx.json', '{}');

        $type = new LinkTypeRow('has_stop', 'tour', 'obj');
        LinkFile::merge($this->dir, [$type], [LinkRow::of($type, 't1', 'o1')]);

        self::assertFileDoesNotExist($this->dir.'/link.jsonl');
        self::assertFileDoesNotExist($this->dir.'/link.jsonl.gz.idx.json');
        self::assertSame(['new', 't1'], array_map(static fn (LinkRow $l): string => $l->subjectId, [...LinkFile::readLinks($this->dir)]));
    }

    public function testRejectsUndeclaredLinks(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        LinkFile::merge($this->dir, [new LinkTypeRow('has_stop', 'tour', 'obj')], [new LinkRow('has_stop', 'story', 's1', 'stop', 'x1')]);
    }

    /** @param list<array<string,mixed>> $rows */
    private function write(string $name, array $rows): void
    {
        file_put_contents($this->dir.'/'.$name, implode('', array_map(static fn (array $r): string => json_encode($r)."\n", $rows)));
    }
}
