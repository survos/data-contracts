<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Core;

use Survos\DataContracts\Attribute\PropertyMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\DataContracts\Vocabulary\ItemField;

/**
 * Base DTO for rows in the `per` core.
 *
 * `per` is the compact folio core/vocabulary code; `person` is the semantic
 * content type used as dtoType/contentType.
 */
abstract class BasePersonDto
{
    #[PropertyMeta(searchable: true, sortable: true)]
    public ?string $id = null {
        set(mixed $value) => $this->id = $value !== null ? (string) $value : null;
    }

    #[PropertyMeta(facet: true)]
    public ?string $contentType = null;

    #[PropertyMeta(facet: true)]
    public ?string $aggregator = null;

    public ?string $sourceUrl = null;

    #[PropertyMeta(facet: true)]
    public ?string $language = null;

    #[PropertyMeta(searchable: true, sortable: true)]
    public ?string $name = null;

    #[PropertyMeta(searchable: true, sortable: true)]
    public ?string $givenName = null;

    #[PropertyMeta(searchable: true, sortable: true, facet: true)]
    public ?string $familyName = null;

    #[PropertyMeta(searchable: true, sortable: true)]
    public ?string $sortName = null;

    #[PropertyMeta(searchable: true)]
    public ?string $description = null;

    #[PropertyMeta(sortable: true, facet: true)]
    public ?string $birthDate = null;

    #[PropertyMeta(sortable: true, facet: true)]
    public ?string $deathDate = null;

    #[PropertyMeta(searchable: true, facet: true)]
    public ?string $birthPlace = null;

    #[PropertyMeta(searchable: true, facet: true)]
    public ?string $deathPlace = null;

    #[PropertyMeta(facet: true)]
    public ?string $gender = null;

    /**
     * Legal, social, or life status of the person, e.g. "Enslaved Person", "Free Person",
     * "Deceased" — deliberately generic (not "enslavementStatus" or similar), so any future
     * person-oriented provider whose subjects carry some status vocabulary gets a real home for
     * it instead of falling into $unmapped. First real source: Enslaved.org's Wikibase dump
     * (wikibase provider, survos-sites/musdig#35).
     */
    #[PropertyMeta(facet: true)]
    public ?string $status = null;

    #[PropertyMeta(searchable: true, facet: true)]
    public ?string $nationality = null;

    /**
     * Ethnic or ethnolinguistic origin group as recorded by the source, e.g. "Igbo", "Afro-Creole",
     * "Angola" -- deliberately NOT folded into $nationality: nationality means modern citizenship,
     * a narrower and often anachronistic concept for historical records (an enslaved or captured
     * person's recorded ethnic/language-group origin predates and is distinct from any nation-state
     * citizenship). First real source: Enslaved.org's Wikibase dump (wikibase provider,
     * survos-sites/musdig#35, property P46 hasEthnolinguisticDescriptor).
     */
    #[PropertyMeta(searchable: true, facet: true)]
    public ?string $ethnicity = null;

    /** @var string[]|null */
    #[PropertyMeta(searchable: true, facet: true)]
    public ?array $occupations = null;

    /**
     * Role(s) held in specific recorded contexts, e.g. "Registered Person", "Buyer", "Vessel
     * Captain" -- distinct from $status (overall recorded condition): role is context-specific,
     * a person can hold different roles in different records. First real source: Enslaved.org's
     * Wikibase dump (property P17).
     *
     * @var string[]|null
     */
    #[PropertyMeta(searchable: true, facet: true)]
    public ?array $role = null;

    /** @var string[]|null */
    #[PropertyMeta(searchable: true, facet: true)]
    public ?array $subjects = null;

    public ?string $rights = null;

    #[PropertyMeta(searchable: true)]
    public ?string $searchSummary = null;

    #[PropertyMeta(searchable: true)]
    public ?string $denseSummary = null;

    /** Fields present in the normalized row that do not map to this DTO. */
    public array $unmapped = [];

    public string $source = 'import';

    public ?float $confidence = 0.7;

    abstract public static function contentType(): string;

    public static function classUri(): ?string
    {
        return ContentType::uri(static::contentType());
    }

    public static function classLabel(): string
    {
        return 'Person';
    }

    /** @param array<string,mixed> $row */
    public static function fromNormalized(array $row): static
    {
        $dto = new static();
        $knownProps = array_keys(get_object_vars($dto));

        foreach ($knownProps as $prop) {
            if ($prop === 'unmapped') {
                continue;
            }
            if (array_key_exists($prop, $row)) {
                $dto->$prop = $row[$prop];
            }
        }

        $dto->id ??= $row[ItemField::SOURCE_ID] ?? $row['sourceId'] ?? null;
        $dto->sourceUrl ??= $row[ItemField::CITATION_URL] ?? $row[ItemField::PAGE_URL] ?? $row[ItemField::URL] ?? null;
        $dto->contentType ??= $row[ItemField::CONTENT_TYPE] ?? static::contentType();
        $dto->aggregator ??= $row[ItemField::AGGREGATOR] ?? null;
        $dto->language ??= $row[ItemField::LANGUAGE] ?? null;
        $dto->name ??= $row[ItemField::TITLE] ?? $row['label'] ?? null;
        $dto->searchSummary ??= $row[ItemField::SEARCH_SUMMARY] ?? null;
        $dto->denseSummary ??= $row[ItemField::DENSE_SUMMARY] ?? null;

        // These raw keys feed the fallback assignments above under an alternate name (sourceId
        // -> id, citationUrl -> sourceUrl, title -> name, ...) -- excluding them here (matching
        // BaseItemDto::applyMapAliases()'s equivalent unmapped-cleanup) keeps unmapped/extras
        // genuinely unmapped instead of redundantly duplicating a value already on a named
        // property. Without this, every person row carried e.g. both $sourceUrl (real) AND
        // unmapped.citationUrl (the same value, unreachable by name).
        $aliasKeysConsumed = [
            ItemField::SOURCE_ID, 'sourceId',
            ItemField::CITATION_URL, ItemField::PAGE_URL, ItemField::URL,
            ItemField::CONTENT_TYPE, ItemField::AGGREGATOR, ItemField::LANGUAGE,
            ItemField::TITLE, 'label',
            ItemField::SEARCH_SUMMARY, ItemField::DENSE_SUMMARY,
        ];

        foreach ($row as $key => $value) {
            if (!in_array($key, $knownProps, true) && !in_array($key, $aliasKeysConsumed, true)) {
                $dto->unmapped[$key] = $value;
            }
        }

        return $dto;
    }

    /** @return array<string,mixed> */
    public function toMeili(): array
    {
        return array_filter(
            get_object_vars($this),
            static fn (mixed $value): bool => $value !== null && $value !== [] && $value !== ''
        );
    }
}
