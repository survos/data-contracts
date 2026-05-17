<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\DataContracts\Vocabulary\DcTerms;
use Survos\DataContracts\Vocabulary\ItemField;

/**
 * Base DTO for all museum collection items.
 *
 * Maps to/from:
 *   - pixie Row.data (JSON blob in SQLite)
 *   - zm Item + Value rows (via field_map.yaml)
 *   - Meilisearch document (flat array via toMeili())
 *
 * Subclass per ContentType constant to add type-specific fields,
 * define the default zm ResourceTemplate, and drive the input form.
 *
 * All field names match the normalized JSONL keys from DcSetRecordListener,
 * FortepanSetRecordListener, etc. — the same names in 20_normalize/obj.jsonl.
 */
abstract class BaseItemDto
{
    // ── Identity ──────────────────────────────────────────────────────────────

    public ?string $id = null {
        set(mixed $value) => $this->id = $value !== null ? (string) $value : null;
    }
    public ?string $sourceUrl   = null;
    public ?string $contentType = null;
    public ?string $aggregator  = null;

    // ── Core DC fields (always present regardless of type) ───────────────────

    /** dcterms:title */
    public ?string $title = null;

    /** dcterms:description — short curatorial text from the source institution */
    public ?string $description = null;

    /**
     * Detailed physical description of the object as observed:
     * form, materials, markings, condition, dimensions.
     * May come from the source catalog or from AI pass 1 (image analysis).
     */
    public ?string $physicalDescription = null;

    /**
     * AI pass 2: combines physical observation with provenance, period,
     * and cultural context into a researcher-facing narrative.
     * Richer than physicalDescription; intended for display and discovery.
     */
    public ?string $contextDescription = null;

    /**
     * ai:denseSummary — ≤ 400 char retrieval-optimised summary.
     * Entity-rich, factual, no filler. Used by Meilisearch /chat, RAG, chatbots.
     */
    public ?string $denseSummary = null;

    /** dcterms:date — display string, may be fuzzy ("ca. 1920") */
    public ?string $date = null;

    /** Integer year for sorting/filtering */
    public ?int $year = null;

    /** ItemField::CITATION — canonical URL or attribution string for the record */
    public ?string $citation = null;

    /** dcterms:rights */
    public ?string $rights = null;

    /** dcterms:license URI (rightsstatements.org) */
    public ?string $rightsUri = null;

    /** dcterms:accessRights — e.g. "no restrictions", "creative commons" */
    public ?string $reuseAllowed = null;

    /** dcterms:language */
    public ?array $language = null;

    /** dcterms:identifier — local accession number */
    public ?string $identifierLocal = null;

    // ── Agents ────────────────────────────────────────────────────────────────

    /** dcterms:creator — array of names */
    public ?array $creators = null;

    /** Holding institution */
    public ?string $institution = null;

    /** Collection name(s) */
    public ?array $collections = null;

    // ── Subjects ──────────────────────────────────────────────────────────────

    /** dcterms:subject — keyword/topical subjects */
    public ?array $subjects = null;

    /** dcterms:spatial — geographic subjects */
    public ?array $subjectsGeographic = null;

    // ── Geography ─────────────────────────────────────────────────────────────

    public ?string $country  = null;
    public ?string $state    = null;
    public ?string $county   = null;
    public ?string $city     = null;
    public ?float  $latitude = null;
    public ?float  $longitude= null;

    // ── Media ─────────────────────────────────────────────────────────────────

    /** IIIF Image API base URL — use for AI vision and imgProxy resizing */
    public ?string $iiifBase      = null;
    public ?string $iiifManifest  = null;
    public ?string $thumbnailUrl  = null;
    public ?string $largeImageUrl = null;

    // ── Unmapped fields ───────────────────────────────────────────────────────

    /** Fields present in the source record that don't map to any DTO property. */
    public array $unmapped = [];

    /** Best display label: title → description prefix → id */
    public function label(): string
    {
        return $this->title
            ?? ($this->description ? mb_strimwidth($this->description, 0, 80, '…') : null)
            ?? (string) ($this->id ?? '');
    }

    // ── Provenance (for zm Values) ────────────────────────────────────────────

    /** Source of this data: import | ai | ocr | human */
    public string $source = 'import';

    /** Confidence 0.0–1.0 (null = certain, i.e. human-entered) */
    public ?float $confidence = 0.7;

    // ── Class metadata (override in subclasses) ───────────────────────────────

    /**
     * The ContentType constant for this DTO class.
     * e.g. ContentType::PHOTOGRAPH, ContentType::NEWSPAPER
     */
    abstract public static function contentType(): string;

    /**
     * LOC/DCMI URI for the content type.
     * Used as zm ResourceClass and for RDF export.
     */
    public static function classUri(): ?string
    {
        return ContentType::uri(static::contentType());
    }

    /**
     * Human-readable label for the zm ResourceTemplate.
     */
    public static function classLabel(): string
    {
        return ucfirst(static::contentType());
    }

    // ── Hydration ─────────────────────────────────────────────────────────────

    /**
     * Instantiate the correct typed subclass from an Asset::$sourceMeta blob.
     *
     * Uses ContentType to pick the right subclass (PhotographDto, MapDto, etc.)
     * then maps dcterms: keys via the DcTerms enum — fully automatic, no hard-coding.
     *
     * Registry maps content_type slug → DTO class. Add new types here as subclasses are created.
     */
    private static array $typeRegistry = [];

    public static function registerType(string $contentType, string $dtoClass): void
    {
        static::$typeRegistry[$contentType] = $dtoClass;
    }

    public static function fromSourceMeta(array $meta): static
    {
        // Pick the right subclass from content_type
        $contentType = $meta[ItemField::CONTENT_TYPE] ?? null;
        $class = static::$typeRegistry[$contentType] ?? static::class;
        /** @var static $dto */
        $dto = new $class();

        // Map dcterms: keys via DcTerms enum → DTO property names
        // DcTerms::TITLE->localName() = 'title' → $dto->title
        foreach (DcTerms::cases() as $term) {
            $key = $term->value;            // 'dcterms:title'
            $prop = $term->localName();     // 'title'
            if (!array_key_exists($key, $meta)) continue;
            // Map to DTO property using camelCase conversion for multi-word terms
            // e.g. 'isPartOf' → $dto->collections (special cases below)
            $camel = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $prop))));
            if (property_exists($dto, $camel)) {
                $dto->$camel = $meta[$key];
            }
        }

        // Special mappings where dcterms localName ≠ DTO property
        $dto->id           ??= $meta[ItemField::SOURCE_ID]         ?? null;
        $dto->sourceUrl    ??= $meta[DcTerms::SOURCE->value]       ?? null;
        $dto->contentType  ??= $contentType                        ?? ($dto instanceof static ? static::contentType() : null);
        $dto->aggregator   ??= $meta[ItemField::AGGREGATOR]        ?? null;
        $dto->creators     ??= $meta[DcTerms::CREATOR->value]      ?? null;
        $dto->subjects     ??= $meta[DcTerms::SUBJECT->value]      ?? null;
        $dto->collections  ??= $meta[DcTerms::IS_PART_OF->value]   ?? null;
        $dto->rights       ??= $meta[DcTerms::RIGHTS->value]       ?? null;
        $dto->rightsUri    ??= $meta[DcTerms::LICENSE->value]      ?? $meta['license_uri'] ?? null;
        $dto->iiifBase     ??= $meta[ItemField::IIIF_BASE]         ?? null;
        $dto->iiifManifest ??= $meta[ItemField::IIIF_MANIFEST]     ?? null;
        $dto->thumbnailUrl ??= $meta[ItemField::THUMBNAIL_URL]     ?? null;
        $dto->denseSummary ??= $meta[ItemField::DENSE_SUMMARY]     ?? null;

        return $dto;
    }

    /**
     * Serialize back to a sourceMeta blob (inverse of fromSourceMeta).
     * Uses DcTerms enum for the canonical dcterms: keys.
     */
    public function toSourceMeta(): array
    {
        // Build dcterms: keyed map from DTO properties
        $meta = array_filter([
            DcTerms::TITLE->value         => $this->title,
            DcTerms::DESCRIPTION->value   => $this->description,
            DcTerms::DATE->value          => $this->date,
            DcTerms::CREATOR->value       => $this->creators,
            DcTerms::SUBJECT->value       => $this->subjects,
            DcTerms::IS_PART_OF->value    => $this->collections,
            DcTerms::PUBLISHER->value     => $this->institution,
            DcTerms::LANGUAGE->value      => $this->language,
            DcTerms::EXTENT->value        => $this->extent ?? null,
            DcTerms::RIGHTS->value        => $this->rights,
            DcTerms::LICENSE->value       => $this->rightsUri,
            DcTerms::ACCESS_RIGHTS->value => $this->reuseAllowed ?? null,
            DcTerms::SOURCE->value        => $this->sourceUrl,
            DcTerms::IDENTIFIER->value    => $this->identifierLocal,
            ItemField::CONTENT_TYPE       => static::contentType(),
            ItemField::AGGREGATOR         => $this->aggregator,
            ItemField::SOURCE_ID          => $this->id,
            ItemField::IIIF_BASE          => $this->iiifBase,
            ItemField::IIIF_MANIFEST      => $this->iiifManifest,
            ItemField::THUMBNAIL_URL      => $this->thumbnailUrl,
            ItemField::DENSE_SUMMARY      => $this->denseSummary,
        ], static fn($v) => $v !== null && $v !== '' && $v !== []);

        return $meta;
    }

    /**
     * Populate from a normalized JSONL record (20_normalize/obj.jsonl).
     * Field names match the normalizer output exactly.
     */
    public static function fromNormalized(array $row): static
    {
        $dto = new static();
        $knownProps = array_keys(get_object_vars($dto));

        foreach ($knownProps as $prop) {
            if ($prop === 'unmapped') {
                continue;
            }
            if (array_key_exists($prop, $row)) {
                $dto->$prop = $row[$prop]; // property hooks handle coercion (e.g. int id → string)
            }
        }

        foreach ($row as $key => $value) {
            if (!in_array($key, $knownProps, true)) {
                $dto->unmapped[$key] = $value;
            }
        }

        // Handle snake_case / aliased keys not covered by direct property name match
        $dto->id            ??= $row[ItemField::SOURCE_ID]   ?? $row[ItemField::ARK] ?? null;
        $dto->sourceUrl     ??= $row[DcTerms::SOURCE->value] ?? $row[ItemField::PAGE_URL]   ?? null;
        $dto->contentType   ??= $row[ItemField::CONTENT_TYPE]?? static::contentType();
        $dto->aggregator    ??= $row[ItemField::AGGREGATOR]  ?? null;
        $dto->creators      ??= $row[DcTerms::CREATOR->value]?? $row['name_facet']          ?? null;
        $dto->subjects      ??= $row[DcTerms::SUBJECT->value]?? $row['subject_facet']       ?? null;
        $dto->subjectsGeographic ??= $row['subject_geographic'] ?? null;
        $dto->identifierLocal    ??= $row['identifier_local']   ?? null;
        $dto->iiifBase      ??= $row[ItemField::IIIF_BASE]     ?? null;
        $dto->iiifManifest  ??= $row[ItemField::IIIF_MANIFEST] ?? null;
        $dto->thumbnailUrl  ??= $row[ItemField::THUMBNAIL_URL]   ?? null;
        $dto->largeImageUrl ??= $row[ItemField::LARGE_IMAGE_URL] ?? null;
        $dto->denseSummary  ??= $row[ItemField::DENSE_SUMMARY] ?? null;
        return $dto;
    }

    /**
     * Flatten to a Meilisearch document — same field names as the normalized JSONL.
     * Null values are excluded (Meilisearch handles missing fields gracefully).
     */
    public function toMeili(): array
    {
        return array_filter(
            get_object_vars($this),
            static fn($v) => $v !== null && $v !== [] && $v !== ''
        );
    }

    /**
     * Build a zm-compatible Value map keyed by dcterms: URI.
     * Used by the zm import pipeline.
     *
     * @return array<string, mixed>
     */
    public function toValueMap(): array
    {
        return array_filter([
            DcTerms::TITLE->value      => $this->title,
            DcTerms::DESCRIPTION->value=> $this->description,
            DcTerms::DATE->value       => $this->date,
            DcTerms::CREATOR->value    => $this->creators,
            DcTerms::SUBJECT->value    => array_unique(array_filter(array_merge(
                $this->subjects ?? [],
                $this->subjectsGeographic ?? [],
            ))) ?: null,
            DcTerms::LANGUAGE->value   => $this->language,
            DcTerms::RIGHTS->value     => $this->rights,
            DcTerms::LICENSE->value    => $this->rightsUri,
            DcTerms::IDENTIFIER->value => $this->identifierLocal,
            DcTerms::SOURCE->value     => $this->sourceUrl,
            ItemField::LATITUDE        => $this->latitude,
            ItemField::LONGITUDE       => $this->longitude,
            ItemField::DENSE_SUMMARY   => $this->denseSummary,
        ], static fn($v) => $v !== null && $v !== [] && $v !== '');
    }
}
