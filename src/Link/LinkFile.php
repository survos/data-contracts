<?php

declare(strict_types=1);

namespace Survos\DataContracts\Link;

use Survos\DataContracts\Dto\Link\LinkRow;
use Survos\DataContracts\Dto\Link\LinkTypeRow;

/**
 * The one way a producer adds its edges to a normalize stage's linkType.jsonl + link.jsonl.
 *
 * Several producers write into the same two files (RelationCollector's creator edges, a provider's
 * tour edges, a Wikibase listener's event edges), so nobody may simply overwrite them. merge()
 * rewrites each file as (rows other producers own) + (this producer's rows), which makes it
 * idempotent: run it twice and the edges appear once.
 *
 * Ownership is exactly the link types passed in, keyed by {@see LinkTypeRow::key()}
 * (subjectCore:code:objectCore) — never by bare code, which two types may share — plus any types
 * passed as `retire`, which are dropped without replacement. A row of any other type, or one this
 * class cannot parse, belongs to somebody else and survives untouched.
 *
 * Both files are streamed (link.jsonl can run to millions of rows) through a temp sibling that is
 * renamed into place, so a crash leaves the previous file intact. Whichever of `<stem>.jsonl` /
 * `<stem>.jsonl.gz` was written last is the one read and rewritten, and the other spelling is
 * removed — a stale plain file beside a fresh .gz would otherwise win path resolution and hide it.
 */
final class LinkFile
{
    public const string LINK_TYPE_STEM = 'linkType';
    public const string LINK_STEM = 'link';

    private const int FLAGS = \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR;

    /** Sidecars survos/jsonl-bundle's JsonlWriter keeps beside a file; they describe the old content. */
    private const array SIDECAR_SUFFIXES = ['.idx.json', '.sidecar.json', '.index.json'];

    /**
     * @param iterable<LinkTypeRow>        $types  the link types this producer owns; every one is (re)declared
     * @param iterable<LinkRow>            $links  this producer's edges, each of one of $types
     * @param bool|null                    $gzip   spelling for a file that does not exist yet (null: plain);
     *                                             an existing file keeps its own spelling
     * @param iterable<LinkTypeRow|string> $retire link types (or their keys) to drop together with
     *                                             their links and NOT re-declare — a shape this producer no
     *                                             longer writes, e.g. `story:has_stop:stop` after the tour flip
     *
     * @return array{types:int,links:int} rows written by this producer
     */
    public static function merge(string $normalizeDir, iterable $types, iterable $links, ?bool $gzip = null, iterable $retire = []): array
    {
        $owned = [];
        foreach ($types as $type) {
            $owned[$type->key()] = $type;
        }
        $dropped = $owned;
        foreach ($retire as $type) {
            $key = $type instanceof LinkTypeRow ? $type->key() : $type;
            if (isset($owned[$key])) {
                throw new \InvalidArgumentException(sprintf('Link type "%s" cannot be both declared and retired.', $key));
            }
            $dropped[$key] = true;
        }

        self::rewrite(
            $normalizeDir,
            self::LINK_TYPE_STEM,
            static fn (array $row): bool => isset($dropped[self::rowTypeKey($row, 'code')]),
            (static function () use ($owned): \Generator {
                foreach ($owned as $type) {
                    yield $type->toArray();
                }
            })(),
            $gzip,
        );

        $count = 0;
        self::rewrite(
            $normalizeDir,
            self::LINK_STEM,
            static fn (array $row): bool => isset($dropped[self::rowTypeKey($row, 'predicate')]),
            (static function () use ($links, $owned, &$count): \Generator {
                foreach ($links as $link) {
                    if (!isset($owned[$link->typeKey()])) {
                        // Undeclared, it would not be dropped on the next run and would pile up.
                        throw new \InvalidArgumentException(sprintf('Link of type "%s" is not among the declared link types (%s).', $link->typeKey(), implode(', ', array_keys($owned))));
                    }
                    ++$count;
                    yield $link->toArray();
                }
            })(),
            $gzip,
        );

        return ['types' => \count($owned), 'links' => $count];
    }

    /**
     * Every link type declared in $normalizeDir, keyed by {@see LinkTypeRow::key()}.
     *
     * @return array<string, LinkTypeRow>
     */
    public static function readTypes(string $normalizeDir): array
    {
        $types = [];
        $file = self::existing($normalizeDir, self::LINK_TYPE_STEM)[0] ?? null;
        foreach (null === $file ? [] : self::rows($file) as $row) {
            $type = LinkTypeRow::fromArray($row);
            $types[$type->key()] = $type;
        }

        return $types;
    }

    /** @return iterable<LinkRow> */
    public static function readLinks(string $normalizeDir): iterable
    {
        $file = self::existing($normalizeDir, self::LINK_STEM)[0] ?? null;
        foreach (null === $file ? [] : self::rows($file) as $row) {
            yield LinkRow::fromArray($row);
        }
    }

    /**
     * @param callable(array<string,mixed>):bool $isOurs
     * @param iterable<array<string,mixed>>      $ours
     */
    private static function rewrite(string $dir, string $stem, callable $isOurs, iterable $ours, ?bool $gzip): void
    {
        $spellings = self::existing($dir, $stem);
        $target = $spellings[0] ?? $dir.'/'.$stem.'.jsonl'.(($gzip ?? false) ? '.gz' : '');
        $isGz = str_ends_with($target, '.gz');
        $temp = $target.'.merging';

        if (!is_dir($dir) && !mkdir($dir, 0o777, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Cannot create %s.', $dir));
        }
        $out = $isGz ? gzopen($temp, 'wb6') : fopen($temp, 'wb');
        if (false === $out) {
            throw new \RuntimeException(sprintf('Cannot open %s for writing.', $temp));
        }
        $write = static function (string $line) use ($out, $isGz): void {
            $isGz ? gzwrite($out, $line."\n") : fwrite($out, $line."\n");
        };

        try {
            if (is_file($target)) {
                foreach (self::lines($target) as $line) {
                    $row = json_decode($line, true);
                    if (!\is_array($row) || !$isOurs($row)) {
                        $write($line);
                    }
                }
            }
            foreach ($ours as $row) {
                $write(json_encode($row, self::FLAGS));
            }
        } catch (\Throwable $e) {
            $isGz ? gzclose($out) : fclose($out);
            @unlink($temp);
            throw $e;
        }
        $isGz ? gzclose($out) : fclose($out);

        if (!rename($temp, $target)) {
            throw new \RuntimeException(sprintf('Cannot move %s to %s.', $temp, $target));
        }
        foreach ($spellings as $file) {
            if ($file !== $target) {
                @unlink($file);
            }
            foreach (self::SIDECAR_SUFFIXES as $suffix) {
                @unlink($file.$suffix);
            }
        }
    }

    /** @return list<string> the existing spellings of <stem>.jsonl[.gz], most recently written first */
    private static function existing(string $dir, string $stem): array
    {
        $files = array_values(array_filter([$dir.'/'.$stem.'.jsonl', $dir.'/'.$stem.'.jsonl.gz'], 'is_file'));
        usort($files, static fn (string $a, string $b): int => filemtime($b) <=> filemtime($a));

        return $files;
    }

    /** @param array<string,mixed> $row */
    private static function rowTypeKey(array $row, string $codeKey): ?string
    {
        foreach (['subjectCore', $codeKey, 'objectCore'] as $key) {
            if (!\is_scalar($row[$key] ?? null)) {
                return null;
            }
        }

        return LinkTypeRow::keyOf((string) $row['subjectCore'], (string) $row[$codeKey], (string) $row['objectCore']);
    }

    /** @return iterable<array<string,mixed>> */
    private static function rows(string $file): iterable
    {
        foreach (self::lines($file) as $line) {
            $row = json_decode($line, true);
            if (\is_array($row)) {
                yield $row;
            }
        }
    }

    /** @return iterable<string> non-empty trimmed lines; gzopen reads plain files too */
    private static function lines(string $file): iterable
    {
        $handle = gzopen($file, 'rb');
        if (false === $handle) {
            throw new \RuntimeException(sprintf('Cannot read %s.', $file));
        }
        try {
            while (($line = gzgets($handle)) !== false) {
                if (($line = trim($line)) !== '') {
                    yield $line;
                }
            }
        } finally {
            gzclose($handle);
        }
    }
}
