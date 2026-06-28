<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\PropertyMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\DataContracts\Vocabulary\DcTerms;
use Survos\DataContracts\Vocabulary\ItemField;
use Survos\FieldBundle\Attribute\Field;
use Survos\FieldBundle\Attribute\Map;
use Survos\Lingua\Contracts\Attribute\Translatable;

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

    #[Map(source: [ItemField::SOURCE_ID, ItemField::ARK, 'objectID', 'ObjectID', 'codigoDeCatalogacion', 'systemId'])]
    public ?string $id = null {
        set(mixed $value) => $this->id = $value !== null ? (string) $value : null;
    }
    #[Map(source: [DcTerms::SOURCE->value, ItemField::CITATION_URL, ItemField::PAGE_URL, 'IsShownAt', 'cite', 'ResourceURL', 'objectURL', 'linkResource', 'frontendUrl', 'recordUrl', 'objectUrl'])]
    public ?string $sourceUrl   = null;
    public ?string $contentType = null;
    public ?string $aggregator  = null;

    /** Original source asset format: 'pdf' (single document) or 'images' (page-image set). A facet. */
    #[Field(facet: true, filterable: true)]
    public ?string $sourceFormat = null;

    /** Pipeline processing stage (raw / normalized / enriched). A facet so you can filter to enriched items; later this can be made dev-only. */
    #[Field(facet: true, filterable: true)]
    public ?string $stage = null;

    /**
     * Distinct AI tasks applied to this item or any of its images — e.g. observe, analyze, ocr_mistral,
     * enrich_from_hires, info. Derived from the item's claim sources during the folio build (normalized,
     * 'ai:' prefix stripped). A multi-value facet, so you can filter to "items that had analysis" etc.
     * Finer-grained than {@see $stage}: 'enriched' alone can't distinguish observe-only from observe+analyze.
     *
     * @var list<string>|null
     */
    #[Field(facet: true, filterable: true)]
    public ?array $aiTasks = null;

    // ── Core DC fields (always present regardless of type) ───────────────────

    /** dcterms:title */
    #[Map(source: ['title', DcTerms::TITLE->value, 'title_info_primary', 'title_info_primary_t', 'object_name', 'objectName', 'display_title', 'displayTitle', 'nativeName', 'label', 'titulo', 'titel'])]
    #[Translatable]
    public ?string $title = null;

    /** dcterms:description — short curatorial text from the source institution */
    #[Map(source: ['description', DcTerms::DESCRIPTION->value, 'object_description', 'objectDescription', 'objectSummary', 'descripcion', 'beschreibung'])]
    #[Translatable]
    public ?string $description = null;

    /**
     * Detailed physical description of the object as observed:
     * form, materials, markings, condition, dimensions.
     * May come from the source catalog or from AI pass 1 (image analysis).
     */
    #[Translatable]
    #[Map(source: ['object_material_technique', 'objectMaterialTechnique'])]
    public ?string $physicalDescription = null;

    /** Ownership history (museum provenance line, e.g. Walters' "provenance" extra). Free text. */
    #[Map(source: ['provenance', 'dcterms:provenance'])]
    public ?string $provenance = null;

    /**
     * AI pass 2: combines physical observation with provenance, period,
     * and cultural context into a researcher-facing narrative.
     * Richer than physicalDescription; intended for display and discovery.
     */
    #[Translatable]
    #[Map(source: ['significanceStatement'])]
    public ?string $contextDescription = null;

    /**
     * ai:denseSummary — ≤ 400 char retrieval-optimised summary.
     * Entity-rich, factual, no filler. Used by Meilisearch /chat, RAG, chatbots.
     */
    #[Map(source: [ItemField::DENSE_SUMMARY])]
    public ?string $denseSummary = null;

    /** ai:observationProse — detailed AI visual observation (markdown). Rendered with the |markdown filter. */
    #[Map(source: [ItemField::OBSERVATION_PROSE])]
    public ?string $observationProse = null;

    /** ai:caption — short AI-generated caption (also seeds the title when the source has none). */
    #[Map(source: [ItemField::CAPTION])]
    public ?string $caption = null;

    /**
     * search_summary - deterministic BM25-friendly text assembled from normalized fields.
     * Distinct from ai:denseSummary, which is an AI-generated retrieval summary.
     */
    public ?string $searchSummary = null;

    /**
     * Full OCR / transcription text, folded in from the vault's claims.jsonl (ai:ocrText).
     * Long-form; rendered in a dedicated panel, not the field table. For multi-page items
     * this is the item-level concatenation; per-page text lives on the image core.
     */
    public ?string $ocrText = null;

    /** dcterms:date — display string, may be fuzzy ("ca. 1920") */
    #[Map(source: ['date', DcTerms::DATE->value, 'creationDate', 'CreationDate', 'DateText', 'dateMade', 'objectDate', 'accessionDate', 'lastUpdate'])]
    public ?string $date = null;

    /** Integer year for sorting/filtering. Facet (numeric → range slider in the grid). */
    #[PropertyMeta(label: 'Year', description: 'Coverage/production year.', sortable: true, facet: true)]
    public ?int $year = null;

    /** ItemField::CITATION — canonical URL or attribution string for the record */
    public ?string $citation = null;

    /** ItemField::CITATION_URL — deep link back to the source record (e.g. the NARA catalog page). Rendered as the "Original" link on the folio item page. */
    #[Map(source: ['ResourceURL'])]
    public ?string $citationUrl = null;

    /** ItemField::SOURCE_API_URL — machine-readable source API endpoint for this record (admin/technical;
     *  e.g. museum-digital's JSON export). Not a user-facing display field. */
    public ?string $sourceApiUrl = null;

    /** dcterms:rights */
    #[Map(source: ['estadoDerechosObra'])]
    public ?string $rights = null;

    /** dcterms:license URI (rightsstatements.org) */
    #[Map(source: [DcTerms::LICENSE->value, 'license', 'rightsstatement_uri', 'license_uri'])]
    public ?string $rightsUri = null;

    /** dcterms:accessRights — e.g. "no restrictions", "creative commons" */
    #[Map(source: ['reuse_allowed'])]
    public ?string $reuseAllowed = null;

    /** dcterms:language */
    #[Map(source: ['expected_language', 'expectedLanguage'])]
    public ?string $language = null;

    /** dcterms:identifier — local accession number */
    #[Map(source: ['localIdentifier', 'object_inventory_number', 'objectInventoryNumber'])]
    public ?string $identifierLocal = null;

    /**
     * Wikidata Q-ids linked to this item (e.g. ["Q79875815"]) — authority reconciliation and the
     * seed for metadata mining (pull dates/bios/coords/images from Wikidata). owl:sameAs in spirit.
     */
    #[Field(filterable: true)]
    public ?array $wikidata = null;

    // ── Agents ────────────────────────────────────────────────────────────────

    /** dcterms:creator — array of names */
    #[Map(source: [DcTerms::CREATOR->value, 'autores'])]
    public ?array $creators = null;

    /** Holding institution */
    public ?string $institution = null;

    /** Collection name(s) */
    public ?array $collections = null;

    // ── Subjects ──────────────────────────────────────────────────────────────

    /** dcterms:subject — keyword/topical subjects (incl. AI keywords). A sidebar facet. */
    #[Map(source: [DcTerms::SUBJECT->value, ItemField::KEYWORDS])]
    #[Field(facet: true, filterable: true)]
    public ?array $subjects = null;

    /** dcterms:spatial — geographic subjects */
    #[Field(facet: true, filterable: true)]
    public ?array $subjectsGeographic = null;

    // ── Genre / form ──────────────────────────────────────────────────────────

    /** Broad genre/form terms (MODS genre @basic) — e.g. ["Photographs"]. A sidebar facet. */
    #[Field(facet: true, filterable: true)]
    public ?array $genreBasic = null;

    /** Specific genre/form terms (MODS genre @specific) — e.g. ["Albumen prints"]. A facet. */
    #[Field(facet: true, filterable: true)]
    public ?array $genreSpecific = null;

    /** MODS typeOfResource — coarse resource class, e.g. ["Still image"], ["Text"]. A facet. */
    #[Field(facet: true, filterable: true)]
    public ?array $typeOfResource = null;

    // ── Geography ─────────────────────────────────────────────────────────────

    #[Field(facet: true, filterable: true)]
    #[Map(source: ['edm:country'])]
    public ?string $country  = null;
    public ?string $state    = null;
    public ?string $county   = null;
    #[Field(facet: true, filterable: true)]
    public ?string $city     = null;
    public ?float  $latitude = null;
    public ?float  $longitude= null;

    // ── Media ─────────────────────────────────────────────────────────────────

    /** IIIF Image API base URL — use for AI vision and imgProxy resizing */
    public ?string $iiifBase      = null;
    #[Map(source: ['identifier_iiif_manifest'])]
    public ?string $iiifManifest  = null;
    public ?string $thumbnailUrl  = null;
    #[Map(source: ['IsShownBy', 'edm:isShownBy'])]
    public ?string $largeImageUrl = null;

    /** Downloadable PDF of the document (e.g. DC document_access.pdf). When set, folio shows the
     *  pdf.js viewer instead of the image viewer; it is also the source for the split/OCR pipeline. */
    public ?string $pdfUrl = null;

    #[PropertyMeta(label: 'Image count', description: 'Number of images associated with the item.', sortable: true, facet: true)]
    public ?int $imageCount = null;

    /** Number of pages (PDF pages or page-images). Known precisely after OCR; falls back to imageCount. */
    #[PropertyMeta(label: 'Page count', description: 'Number of pages in the document.', sortable: true)]
    public ?int $pageCount = null;

    /** Size of $pdfUrl in bytes (cheap to learn from the PDF's Content-Range during the page probe). */
    #[PropertyMeta(label: 'PDF size', description: 'Byte size of the source PDF.', sortable: true)]
    public ?int $pdfBytes = null;

    #[PropertyMeta(label: 'Has images', description: 'Whether the item has at least one image; facet for filtering out image-less objects.', facet: true)]
    public ?bool $hasImages = null;

    /** Coarse bucket of imageCount (digital-object count) — '1' / '2-4' / '5-20' / '21+'. A facet for filtering small vs huge items (e.g. microfilm rolls) during dev. */
    #[Field(facet: true, filterable: true)]
    public ?string $sizeBucket = null;

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

    /**
     * Confidence 0.0–1.0; null = certain (human-entered or source-imported fact).
     * Defaults to null so imported records don't carry a meaningless boilerplate score —
     * only AI-derived claims set a real confidence (on the Claim, not the item DTO).
     */
    public ?float $confidence = null;

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
            ItemField::SEARCH_SUMMARY     => $this->searchSummary,
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
        assert(
            !array_filter(array_keys($row), static fn(string $k): bool => str_contains($k, '_') && !str_starts_with($k, 'ai:')),
            'Normalized row keys must be camelCase; found snake_case: ' . implode(', ', array_filter(array_keys($row), static fn(string $k): bool => str_contains($k, '_') && !str_starts_with($k, 'ai:')))
        );

        $dto = new static();
        $knownProps = array_keys(get_object_vars($dto));

        foreach ($knownProps as $prop) {
            if ($prop === 'unmapped') {
                continue;
            }
            if (array_key_exists($prop, $row) && $row[$prop] !== '') {
                // '' is treated as absent (blankToNull semantics) so a #[Map] alias fallback can fill
                // the field from the next source; toMeili filters '' out of dto_data anyway.
                $dto->assignTolerant($prop, $row[$prop]);
            }
        }

        foreach ($row as $key => $value) {
            if (!in_array($key, $knownProps, true)) {
                $dto->unmapped[$key] = $value;
            }
        }

        // Everything not matched by direct camelCase property name is resolved by the declarative
        // #[Map(source: […])] alias lists (id←sourceId/ark, sourceUrl←dcterms:source/…, creators,
        // subjects, denseSummary←ai:denseSummary, title/description/date, …) — one source of truth.
        $dto->applyMapAliases($row);

        // The only non-alias rule left: fall back to the subclass's declared content type.
        $dto->contentType ??= static::contentType();

        // Pipeline-stage facet: 'enriched' once any AI claim is present (observe prose / dense summary /
        // caption), else 'normalized'. Lets the folio filter normalized-only vs AI-enriched records.
        $dto->stage ??= ($dto->denseSummary !== null || $dto->caption !== null || $dto->observationProse !== null)
            ? 'enriched'
            : 'normalized';

        // Always expose a page/image count so it's filterable. The precise multi-page count (PDFs,
        // multi-image objects) is set from the page table during the folio build; this is the floor
        // for items without an emitted page list (e.g. fortepan → 1).
        $dto->pageCount ??= $dto->imageCount ?? 1;

        return $dto;
    }

    /** @var array<class-string, array<string, list<string>>> property → source aliases, per class */
    private static array $aliasMapCache = [];

    /**
     * Resolve declarative #[Map(source: […])] aliases onto this DTO: for each property carrying a
     * Map, set the first present, non-null source key it doesn't already have. One source of truth
     * for "this source field asserts this property", shared by the hydrators — it replaces the
     * per-field `??=` alias chains and is the same property↔predicate map a source-meta→claims
     * projection keys on.
     *
     * @param array<string,mixed> $row
     */
    private function applyMapAliases(array $row): void
    {
        foreach (self::aliasMap() as $prop => $sources) {
            if (($this->$prop ?? null) === null) {
                foreach ($sources as $src) {
                    if (($row[$src] ?? null) !== null && $row[$src] !== '' && $this->assignTolerant($prop, $row[$src])) {
                        break;
                    }
                }
            }
            // Once the property is filled — by its direct name OR any alias — none of its declared
            // source aliases are leftovers. Drop every one of them from unmapped so a redundant alias
            // (e.g. dcterms:description when `description` came from the source field, or sourceId/ark→id)
            // doesn't ALSO leak into extras, which should carry only genuinely unmapped keys.
            if (($this->$prop ?? null) !== null) {
                foreach ($sources as $src) {
                    unset($this->unmapped[$src]);
                }
            }
        }
    }

    /**
     * Assign a source value to a property, tolerating type mismatches instead of letting one bad
     * field abort ALL hydration (which dropped the record to a raw row and silently disabled every
     * #[Map] alias). A scalar going to a ?string property is coerced (an int year 1910 → "1910");
     * anything still incompatible (a string into an ?array field like `dimensions`) is kept as raw
     * context in `unmapped` (→ extras) so it isn't lost.
     *
     * @return bool true if the property was set, false if the value spilled to unmapped
     */
    private function assignTolerant(string $prop, mixed $value): bool
    {
        try {
            $this->$prop = $value; // property hooks handle coercion (e.g. int id → string)
            return true;
        } catch (\TypeError) {
        }
        if (is_scalar($value)) {
            try {
                $this->$prop = (string) $value; // e.g. int year → ?string date
                return true;
            } catch (\TypeError) {
            }
        }
        $this->unmapped[$prop] = $value;
        return false;
    }

    /**
     * property name → ordered source aliases, read once per class from #[Map(source: …)].
     *
     * @return array<string, list<string>>
     */
    private static function aliasMap(): array
    {
        if (isset(self::$aliasMapCache[static::class])) {
            return self::$aliasMapCache[static::class];
        }

        $map = [];
        foreach ((new \ReflectionClass(static::class))->getProperties(\ReflectionProperty::IS_PUBLIC) as $p) {
            $sources = [];
            foreach ($p->getAttributes(Map::class) as $attr) {
                $sources = [...$sources, ...$attr->newInstance()->sources()];
            }
            if ($sources !== []) {
                $map[$p->getName()] = $sources;
            }
        }

        return self::$aliasMapCache[static::class] = $map;
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
            ItemField::SEARCH_SUMMARY  => $this->searchSummary,
            ItemField::DENSE_SUMMARY   => $this->denseSummary,
        ], static fn($v) => $v !== null && $v !== [] && $v !== '');
    }
}
