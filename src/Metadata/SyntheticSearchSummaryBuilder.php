<?php

declare(strict_types=1);

namespace Survos\DataContracts\Metadata;

use Survos\DataContracts\Vocabulary\ItemField;
use Survos\DataContracts\Vocabulary\MuseumVocab;

final class SyntheticSearchSummaryBuilder
{
    private const MAX_LENGTH = 2_000;
    private const MAX_CONCEPTS = 16;

    /**
     * These are not language stop words; they are generic catalogue words that
     * drown out more useful BM25 terms when repeated across every record.
     *
     * @var array<string,true>
     */
    private const CATALOGUE_STOP_WORDS = [
        'catalog' => true,
        'catalogue' => true,
        'collection' => true,
        'contains' => true,
        'description' => true,
        'document' => true,
        'image' => true,
        'images' => true,
        'include' => true,
        'includes' => true,
        'including' => true,
        'item' => true,
        'items' => true,
        'object' => true,
        'objects' => true,
        'record' => true,
        'records' => true,
        'represented' => true,
    ];

    /**
     * @var list<string>
     */
    private const DESCRIPTION_FIELDS = [
        ItemField::DESCRIPTION,
        ItemField::CONTEXT_DESCRIPTION,
        ItemField::PHYSICAL_DESCRIPTION,
        ItemField::NOTES,
    ];

    /**
     * @var array<string,string>
     */
    private const TERM_FIELDS = [
        ItemField::CREATOR => 'Creators',
        MuseumVocab::PERSON => 'People',
        MuseumVocab::ORGANISATION => 'Organisations',
        MuseumVocab::PLACE => 'Places',
        MuseumVocab::SUBJECT => 'Subjects',
        MuseumVocab::MEDIUM => 'Medium',
        MuseumVocab::MATERIAL => 'Materials',
        MuseumVocab::TECHNIQUE => 'Techniques',
        MuseumVocab::CULTURE => 'Culture',
        MuseumVocab::PERIOD => 'Period',
        MuseumVocab::COLLECTION => 'Collection',
        MuseumVocab::DEPARTMENT => 'Department',
        ItemField::KEYWORDS => 'Keywords',
        ItemField::GENRE_SPECIFIC => 'Genre',
        ItemField::GENRE_BASIC => 'Genre',
    ];

    /**
     * @var array<string,true>|null
     */
    private ?array $stopWords = null;

    public function __construct(private readonly ?object $stopWordsProvider = null)
    {
    }

    /**
     * @param array<string,mixed> $row
     */
    public function build(array $row, int $maxLength = self::MAX_LENGTH): string
    {
        $contentType = ContentType::fromRecord($row);
        $title = $this->firstScalar($row[ItemField::TITLE] ?? null);
        $date = $this->firstScalar($row[ItemField::DATE] ?? null);

        $sentences = [];
        $opening = $this->opening($contentType, $title, $date);
        if ($opening !== null) {
            $sentences[] = $opening;
        }

        foreach (self::DESCRIPTION_FIELDS as $field) {
            foreach ($this->scalars($row[$field] ?? null) as $text) {
                $sentences[] = $this->sentence($text);
            }
        }

        foreach (self::TERM_FIELDS as $field => $label) {
            $values = $this->unique($this->scalars($row[$field] ?? null));
            if ($values === []) {
                continue;
            }

            $sentences[] = sprintf('%s: %s.', $label, implode('; ', $values));
        }

        $concepts = $this->concepts($row);
        if ($concepts !== []) {
            $sentences[] = sprintf('Related search terms: %s.', implode('; ', $concepts));
        }

        $summary = $this->truncate($this->dedupeSentences($sentences), $maxLength);

        return $summary;
    }

    private function opening(string $contentType, ?string $title, ?string $date): ?string
    {
        $label = ucfirst(str_replace('_', ' ', $contentType));

        if ($title !== null && $date !== null) {
            return sprintf('%s titled "%s", dated %s.', $label, $title, $date);
        }

        if ($title !== null) {
            return sprintf('%s titled "%s".', $label, $title);
        }

        if ($date !== null) {
            return sprintf('%s dated %s.', $label, $date);
        }

        return $label === '' ? null : sprintf('%s catalogue record.', $label);
    }

    /**
     * @param list<string> $sentences
     */
    private function dedupeSentences(array $sentences): string
    {
        $seen = [];
        $out = [];

        foreach ($sentences as $sentence) {
            $sentence = $this->sentence($sentence);
            if ($sentence === '') {
                continue;
            }

            $key = mb_strtolower($sentence);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $out[] = $sentence;
        }

        return trim(implode(' ', $out));
    }

    private function sentence(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? $text);
        if ($text === '') {
            return '';
        }

        return str_ends_with($text, '.') || str_ends_with($text, '?') || str_ends_with($text, '!')
            ? $text
            : $text . '.';
    }

    private function truncate(string $text, int $maxLength): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $cut = mb_substr($text, 0, max(0, $maxLength - 1));
        $lastStop = max(
            mb_strrpos($cut, '.') ?: 0,
            mb_strrpos($cut, ';') ?: 0,
            mb_strrpos($cut, ',') ?: 0,
            mb_strrpos($cut, ' ') ?: 0,
        );

        if ($lastStop > 200) {
            $cut = mb_substr($cut, 0, $lastStop);
        }

        return rtrim($cut, " \t\n\r\0\x0B.,;") . '…';
    }

    /**
     * @param array<string,mixed> $row
     * @return list<string>
     */
    private function concepts(array $row): array
    {
        $textParts = [];
        foreach ([ItemField::TITLE, ...self::DESCRIPTION_FIELDS] as $field) {
            array_push($textParts, ...$this->scalars($row[$field] ?? null));
        }

        $scores = [];
        foreach ($textParts as $textPart) {
            $tokens = $this->tokens($textPart);
            $count = \count($tokens);
            for ($i = 0; $i < $count; ++$i) {
                $this->scoreConcept($scores, $tokens[$i], 1);

                if (isset($tokens[$i + 1])) {
                    $this->scoreConcept($scores, $tokens[$i] . ' ' . $tokens[$i + 1], 4);
                }

                if (isset($tokens[$i + 2])) {
                    $this->scoreConcept($scores, $tokens[$i] . ' ' . $tokens[$i + 1] . ' ' . $tokens[$i + 2], 5);
                }
            }
        }

        if ($scores === []) {
            return [];
        }

        arsort($scores);

        return \array_slice(array_keys($scores), 0, self::MAX_CONCEPTS);
    }

    /**
     * @return list<string>
     */
    private function tokens(string $text): array
    {
        $stopWords = $this->stopWordMap();
        $tokens = [];

        foreach (preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($text)) ?: [] as $token) {
            if (\strlen($token) < 3 || ctype_digit($token) || isset($stopWords[$token]) || isset(self::CATALOGUE_STOP_WORDS[$token])) {
                continue;
            }

            $tokens[] = $token;
        }

        return $tokens;
    }

    /**
     * @param array<string,int> $scores
     */
    private function scoreConcept(array &$scores, string $concept, int $weight): void
    {
        $scores[$concept] = ($scores[$concept] ?? 0) + $weight;
    }

    /**
     * @return array<string,true>
     */
    private function stopWordMap(): array
    {
        if ($this->stopWords !== null) {
            return $this->stopWords;
        }

        $provider = $this->stopWordsProvider;
        if ($provider === null && class_exists('voku\\helper\\StopWords')) {
            $class = 'voku\\helper\\StopWords';
            $provider = new $class();
        }

        if (!method_exists($provider, 'getStopWordsFromLanguage')) {
            trigger_error('voku/stop-words is required to generate search_summary concept terms for dense retrieval. Install it with: composer require voku/stop-words', \E_USER_WARNING);

            return $this->stopWords = [];
        }

        $words = [];
        foreach ($provider->getStopWordsFromLanguage('en') as $word) {
            if (is_string($word) && $word !== '') {
                $words[mb_strtolower($word)] = true;
            }
        }

        return $this->stopWords = $words;
    }

    /**
     * @return list<string>
     */
    private function scalars(mixed $value): array
    {
        if ($value === null || $value === '' || $value === []) {
            return [];
        }

        if (\is_scalar($value)) {
            return [trim((string) $value)];
        }

        if (!\is_array($value)) {
            return [];
        }

        $out = [];
        foreach ($value as $item) {
            if (\is_scalar($item)) {
                $text = trim((string) $item);
                if ($text !== '') {
                    $out[] = $text;
                }
            }
        }

        return $out;
    }

    private function firstScalar(mixed $value): ?string
    {
        foreach ($this->scalars($value) as $scalar) {
            return $scalar;
        }

        return null;
    }

    /**
     * @param list<string> $values
     * @return list<string>
     */
    private function unique(array $values): array
    {
        $seen = [];
        $out = [];

        foreach ($values as $value) {
            $value = trim($value);
            if ($value === '') {
                continue;
            }

            $key = mb_strtolower($value);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $out[] = $value;
        }

        return $out;
    }
}
