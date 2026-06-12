<?php
declare(strict_types=1);

namespace Survos\DataContracts\Metadata;

use Symfony\Component\String\Inflector\EnglishInflector;
use Survos\DataContracts\Dto\Core\PersonDto;
use Survos\DataContracts\Dto\Item\ArtifactDto;
use Survos\DataContracts\Dto\Item\CoinDto;
use Survos\DataContracts\Dto\Item\SculptureDto;
use Survos\DataContracts\Dto\Item\EphemeraDto;
use Survos\DataContracts\Dto\Item\AudioDto;
use Survos\DataContracts\Dto\Item\BookDto;
use Survos\DataContracts\Dto\Item\CorrespondenceDto;
use Survos\DataContracts\Dto\Item\DocumentDto;
use Survos\DataContracts\Dto\Item\DrawingDto;
use Survos\DataContracts\Dto\Item\PaintingDto;
use Survos\DataContracts\Dto\Item\FilmDto;
use Survos\DataContracts\Dto\Item\GenericItemDto;
use Survos\DataContracts\Dto\Item\ManuscriptDto;
use Survos\DataContracts\Dto\Item\MapDto;
use Survos\DataContracts\Dto\Item\NewspaperDto;
use Survos\DataContracts\Dto\Item\PhotographDto;
use Survos\DataContracts\Dto\Item\PostcardDto;
use Survos\DataContracts\Vocabulary\ItemField;

/**
 * Canonical content-type slugs used across ssai, zm, md, and mus.
 *
 * Each constant value is the slug used to look up a zm ResourceTemplate,
 * drive OCR decisions, and configure AI extraction prompts.
 *
 * URIs come from:
 *   - DCMI Type Vocabulary   https://purl.org/dc/dcmitype/
 *   - LCGFT                  https://id.loc.gov/authorities/genreForms/
 *   - TGM                    https://id.loc.gov/vocabulary/graphicMaterials/
 *   - MARC Genre Terms       https://id.loc.gov/vocabulary/marcgt/
 */
final class ContentType
{
    private static ?EnglishInflector $inflector = null;

    // ── Photographic / Visual ────────────────────────────────────────────────
    const PHOTOGRAPH    = 'photograph';     // TGM tgm007965 / LCGFT gf2017027258
    const POSTCARD      = 'postcard';       // TGM tgm008060 / marcgt postcard
    const STEREOGRAPH   = 'stereograph';    // TGM tgm009325
    const SLIDE         = 'slide';          // TGM tgm009126 (lantern slides, 35mm)
    const NEGATIVE      = 'negative';       // TGM tgm006437 (film/glass/nitrate)
    const PRINT         = 'print';          // TGM tgm007912 (photomechanical, engraving, etc.)
    const DRAWING       = 'drawing';        // LCGFT gf2017027085
    const PAINTING      = 'painting';       // LCGFT gf2017027245
    const POSTER        = 'poster';         // TGM tgm008100

    // ── Cartographic ────────────────────────────────────────────────────────
    const MAP           = 'map';            // LCGFT gf2011026055 / marcgt map
    const ATLAS         = 'atlas';          // marcgt atlas
    const GLOBE         = 'globe';          // marcgt globe

    // ── Textual ──────────────────────────────────────────────────────────────
    const NEWSPAPER     = 'newspaper';      // LCGFT gf2014026139 / marcgt newspaper
    const PERIODICAL    = 'periodical';     // LCGFT gf2014026156 / marcgt periodical
    const MANUSCRIPT    = 'manuscript';     // LCGFT gf2022026088 / marcgt manuscript
    const CORRESPONDENCE= 'correspondence'; // LCGFT gf2014026051
    const BOOK          = 'book';           // marcgt book
    const DOCUMENT      = 'document';       // dcmitype:Text (generic)
    const EPHEMERA      = 'ephemera';       // LCGFT gf2014026093

    // ── Audio / Moving Image ─────────────────────────────────────────────────
    const FILM          = 'film';           // dcmitype:MovingImage
    const AUDIO         = 'audio';          // dcmitype:Sound

    // ── Object / Artifact ────────────────────────────────────────────────────
    const OBJECT        = 'object';         // dcmitype:PhysicalObject (fallback)
    const COIN          = 'coin';           // nmo:NumismaticObject (numismatic specimen)
    const SCULPTURE     = 'sculpture';      // LCGFT gf2017027252 (statue, bust, relief, …)

    // ── Agent cores ─────────────────────────────────────────────────────────────
    const PERSON        = 'person';         // foaf:Person; used with the compact folio core code `per`

    // ── LOC URIs for RDF / zm Vocabulary interop ─────────────────────────────
    const URIS = [
        self::PHOTOGRAPH    => 'http://id.loc.gov/vocabulary/graphicMaterials/tgm007965',
        self::POSTCARD      => 'http://id.loc.gov/vocabulary/graphicMaterials/tgm008060',
        self::STEREOGRAPH   => 'http://id.loc.gov/vocabulary/graphicMaterials/tgm009325',
        self::SLIDE         => 'http://id.loc.gov/vocabulary/graphicMaterials/tgm009126',
        self::NEGATIVE      => 'http://id.loc.gov/vocabulary/graphicMaterials/tgm006437',
        self::PRINT         => 'http://id.loc.gov/vocabulary/graphicMaterials/tgm007912',
        self::DRAWING       => 'http://id.loc.gov/authorities/genreForms/gf2017027085',
        self::PAINTING      => 'http://id.loc.gov/authorities/genreForms/gf2017027245',
        self::POSTER        => 'http://id.loc.gov/vocabulary/graphicMaterials/tgm008100',
        self::MAP           => 'http://id.loc.gov/authorities/genreForms/gf2011026055',
        self::ATLAS         => 'http://id.loc.gov/vocabulary/marcgt/atlas',
        self::GLOBE         => 'http://id.loc.gov/vocabulary/marcgt/globe',
        self::NEWSPAPER     => 'http://id.loc.gov/authorities/genreForms/gf2014026139',
        self::PERIODICAL    => 'http://id.loc.gov/authorities/genreForms/gf2014026156',
        self::MANUSCRIPT    => 'http://id.loc.gov/authorities/genreForms/gf2022026088',
        self::CORRESPONDENCE=> 'http://id.loc.gov/authorities/genreForms/gf2014026051',
        self::BOOK          => 'http://id.loc.gov/vocabulary/marcgt/book',
        self::DOCUMENT      => 'http://purl.org/dc/dcmitype/Text',
        self::EPHEMERA      => 'http://id.loc.gov/authorities/genreForms/gf2014026093',
        self::FILM          => 'http://purl.org/dc/dcmitype/MovingImage',
        self::AUDIO         => 'http://purl.org/dc/dcmitype/Sound',
        self::OBJECT        => 'http://purl.org/dc/dcmitype/PhysicalObject',
        self::PERSON        => 'http://xmlns.com/foaf/0.1/Person',
    ];

    /**
     * Whether this content type benefits from OCR.
     * Drives the AI pipeline decision: run OCR → text extraction, or skip.
     */
    const OCR_TYPES = [
        self::NEWSPAPER,
        self::MANUSCRIPT,
        self::CORRESPONDENCE,
        self::DOCUMENT,
        self::POSTCARD,      // often has handwritten text on reverse
        self::EPHEMERA,
        self::BOOK,
        self::PERIODICAL,
        self::MAP,           // often has labels/text
        self::POSTER,
    ];

    /**
     * Whether this content type benefits from visual AI description.
     */
    const VISUAL_AI_TYPES = [
        self::PHOTOGRAPH,
        self::POSTCARD,
        self::STEREOGRAPH,
        self::NEGATIVE,
        self::PRINT,
        self::DRAWING,
        self::PAINTING,
        self::MAP,
        self::POSTER,
        self::SLIDE,
        self::OBJECT,
    ];

    // ── Incoming field maps ───────────────────────────────────────────────────

    /**
     * Maps DC genre_specific values (TGM terms) to ContentType constants.
     * genre_specific is the most precise — check it first.
     * Synonyms / alternate spellings are included.
     */
    const GENRE_SPECIFIC_MAP = [
        // Photographs
        'photograph'              => self::PHOTOGRAPH,
        'photographic prints'     => self::PHOTOGRAPH,
        'portrait photographs'    => self::PHOTOGRAPH,
        'group portraits'         => self::PHOTOGRAPH,
        'architectural photographs' => self::PHOTOGRAPH,
        'travel photography'      => self::PHOTOGRAPH,
        'cabinet photographs'     => self::PHOTOGRAPH,
        'cartes de visite'        => self::PHOTOGRAPH,
        // Postcards
        'postcard'                => self::POSTCARD,
        'photographic postcard'   => self::POSTCARD,
        'postcards'               => self::POSTCARD,
        'advertising cards'       => self::POSTCARD,
        // Stereographs
        'stereograph'             => self::STEREOGRAPH,
        'stereographs'            => self::STEREOGRAPH,
        // Slides
        'slide'                   => self::SLIDE,
        'photographic slide'      => self::SLIDE,
        'lantern slides'          => self::SLIDE,
        'slides'                  => self::SLIDE,
        // Negatives (all types)
        'negative'                => self::NEGATIVE,
        'film negative'           => self::NEGATIVE,
        'glass negative'          => self::NEGATIVE,
        'glass plate negative'    => self::NEGATIVE,
        'film negatives'          => self::NEGATIVE,
        'glass negatives'         => self::NEGATIVE,
        'nitrate negatives'       => self::NEGATIVE,
        'dry plate negatives'     => self::NEGATIVE,
        'negatives'               => self::NEGATIVE,
        // Prints (non-photo)
        'print'                   => self::PRINT,
        'lithograph'              => self::PRINT,
        'etching'                 => self::PRINT,
        'engraving'               => self::PRINT,
        'lithographs'             => self::PRINT,
        'etchings'                => self::PRINT,
        'engravings'              => self::PRINT,
        'chromolithographs'       => self::PRINT,
        'drypoints'               => self::PRINT,
        'woodcuts'                => self::PRINT,
        'photomechanical prints'  => self::PRINT,
        'periodical illustrations' => self::PRINT,
        'portrait prints'         => self::PRINT,
        'rotogravures'            => self::PRINT,
        'albumen prints'          => self::PHOTOGRAPH,
        'gelatin silver prints'   => self::PHOTOGRAPH,
        // Maps
        'map'                     => self::MAP,
        'topographic maps'        => self::MAP,
        'nautical charts'         => self::MAP,
        'wall maps'               => self::MAP,
        'world maps'              => self::MAP,
        'military maps'           => self::MAP,
        'manuscript maps'         => self::MAP,
        'pictorial maps'          => self::MAP,
        'thematic maps'           => self::MAP,
        // Documents/text
        'document'                => self::DOCUMENT,
        'newspaper clipping'      => self::NEWSPAPER,
        'letter'                  => self::CORRESPONDENCE,
        'telegram'                => self::CORRESPONDENCE,
        'broadsides'              => self::DOCUMENT,
        'receipts (acknowledgments)' => self::CORRESPONDENCE,
        'lists'                   => self::DOCUMENT,
        'clippings'               => self::DOCUMENT,
        'anti-slavery newspapers' => self::NEWSPAPER,
        // Plans/drawings
        'drawing'                 => self::DRAWING,
        'planning drawings'       => self::DRAWING,
        'architectural drawings'  => self::DRAWING,
        // Cartoons / caricatures
        'cartoons'                => self::DRAWING,
        'caricatures'             => self::DRAWING,
        'cartoon'                 => self::DRAWING,
        'editorial cartoons'      => self::DRAWING,
        'comic strips'            => self::DRAWING,
        'illustrations'           => self::DRAWING,
        // Paintings
        'painting'                => self::PAINTING,
        'oil paintings'           => self::PAINTING,
        'watercolors'             => self::PAINTING,
        'watercolours'            => self::PAINTING,
        'gouaches'                => self::PAINTING,
        // Sculpture (EN + PT)
        'sculpture'               => self::SCULPTURE,
        'sculptures'              => self::SCULPTURE,
        'escultura'               => self::SCULPTURE,
        'esculturas'              => self::SCULPTURE,
        'statue'                  => self::SCULPTURE,
        'statues'                 => self::SCULPTURE,
        'estatua'                 => self::SCULPTURE,
        'estátua'                 => self::SCULPTURE,
        'statuette'               => self::SCULPTURE,
        'estatueta'               => self::SCULPTURE,
        'bust'                    => self::SCULPTURE,
        'busts'                   => self::SCULPTURE,
        'busto'                   => self::SCULPTURE,
        'relief'                  => self::SCULPTURE,
        'bas-relief'              => self::SCULPTURE,
        'relevo'                  => self::SCULPTURE,
        'imaginaria'             => self::SCULPTURE,
        'imaginária'             => self::SCULPTURE,
    ];

    /**
     * Maps DC genre_basic values (LCGFT terms) to ContentType constants.
     * Fallback when genre_specific doesn't match.
     */
    const GENRE_BASIC_MAP = [
        'photograph'     => self::PHOTOGRAPH,
        'photographs'    => self::PHOTOGRAPH,
        'photo'          => self::PHOTOGRAPH,  // synonym
        'foto'           => self::PHOTOGRAPH,  // synonym (Spanish/German)
        'picture'        => self::PHOTOGRAPH,  // synonym
        'postcard'       => self::POSTCARD,
        'cards'          => self::POSTCARD,    // DC uses 'Cards' for postcard collections
        'map'            => self::MAP,
        'maps'           => self::MAP,
        'newspaper'      => self::NEWSPAPER,
        'newspapers'     => self::NEWSPAPER,
        'manuscript'     => self::MANUSCRIPT,
        'manuscripts'    => self::MANUSCRIPT,
        'correspondence' => self::CORRESPONDENCE,
        'print'          => self::PRINT,
        'prints'         => self::PRINT,
        'drawing'        => self::DRAWING,
        'drawings'       => self::DRAWING,
        'painting'       => self::PAINTING,
        'paintings'      => self::PAINTING,
        'poster'         => self::POSTER,
        'posters'        => self::POSTER,
        'ephemera'       => self::EPHEMERA,
        'book'           => self::BOOK,
        'books'          => self::BOOK,
        'periodical'     => self::PERIODICAL,
        'periodicals'    => self::PERIODICAL,
        'sound recordings' => self::AUDIO,
        'motion pictures'  => self::FILM,
        'object'           => self::OBJECT,
        'objects'          => self::OBJECT,
        'art objects'      => self::OBJECT,
        'albums (books)'   => self::BOOK,
        'musical notation' => self::DOCUMENT,
        'music'            => self::AUDIO,
    ];

    private const GENRE_SUFFIX_MAP = [
        'photograph' => self::PHOTOGRAPH,
        'postcard' => self::POSTCARD,
        'stereograph' => self::STEREOGRAPH,
        'slide' => self::SLIDE,
        'negative' => self::NEGATIVE,
        'print' => self::PRINT,
        'map' => self::MAP,
        'newspaper clipping' => self::NEWSPAPER,
        'letter' => self::CORRESPONDENCE,
        'telegram' => self::CORRESPONDENCE,
        'drawing' => self::DRAWING,
        'painting' => self::PAINTING,
        'poster' => self::POSTER,
    ];

    /**
     * Maps DC type_of_resource values (DCMI Type) to ContentType constants.
     * Final fallback when neither genre_specific nor genre_basic matches.
     */
    const TYPE_OF_RESOURCE_MAP = [
        'still image'    => self::PHOTOGRAPH,
        'still images'   => self::PHOTOGRAPH,
        'text'           => self::DOCUMENT,
        'cartographic'   => self::MAP,
        'moving image'   => self::FILM,
        'moving images'  => self::FILM,
        'audio'          => self::AUDIO,
        'sound recording'=> self::AUDIO,
        'sound recordings'=> self::AUDIO,
        'notated music'  => self::DOCUMENT,
        'manuscript'     => self::MANUSCRIPT,
        'mixed material' => self::DOCUMENT,
        'artifact'       => self::OBJECT,
        'software'       => self::DOCUMENT,
        'dataset'        => self::DOCUMENT,
    ];

    /**
     * Derive a canonical ContentType slug from DC normalized record attributes.
     *
     * Resolution order (most specific wins):
     *   1. genre_specific (TGM — e.g. "Postcards", "Film negatives")
     *   2. genre_basic    (LCGFT — e.g. "Photographs", "Maps")
     *   3. type_of_resource (DCMI Type — e.g. "Still image", "Text")
     *
     * Returns self::OBJECT as the ultimate fallback.
     */
    public static function fromDcAttrs(array $attrs): string
    {
        // 1. genre_specific — most precise
        foreach ((array)($attrs[ItemField::GENRE_SPECIFIC] ?? []) as $g) {
            if ($type = self::lookupGenreType((string) $g, self::GENRE_SPECIFIC_MAP, self::GENRE_BASIC_MAP)) {
                return $type;
            }
        }

        // 2. genre_basic
        foreach ((array)($attrs[ItemField::GENRE_BASIC] ?? []) as $g) {
            if ($type = self::lookupGenreType((string) $g, self::GENRE_BASIC_MAP, self::GENRE_SPECIFIC_MAP)) {
                return $type;
            }
        }

        // 3. type_of_resource
        $typeRaw = $attrs['type_of_resource'] ?? '';
        foreach ((array) $typeRaw as $value) {
            $type = self::lookupMappedType((string) $value, self::TYPE_OF_RESOURCE_MAP);
            if ($type !== null) {
                return $type;
            }
        }

        return self::OBJECT;
    }

    /**
     * Derive a canonical ContentType slug from any normalized record.
     *
     * Handles both DC-style datasets (genre_specific, genre_basic, type_of_resource)
     * and museum/general datasets where type lives in fields like `type`,
     * `classification`, `objectType`, `category`, `tipo`, `classe`, etc.
     *
     * Listeners call this after their extractions to stamp content_type onto the row.
     * Resolution order:
     *   1. content_type already set → pass through
     *   2. DC-style genre fields → fromDcAttrs()
     *   3. Common type/classification fields → GENRE_SPECIFIC_MAP → GENRE_BASIC_MAP
     *   4. Fallback → self::OBJECT
     */
    public static function fromRecord(array $record): string
    {
        // Already resolved upstream
        if (!empty($record[ItemField::CONTENT_TYPE])) {
            return (string) $record[ItemField::CONTENT_TYPE];
        }

        // DC-style genre fields — pass them under the exact keys fromDcAttrs reads
        // (genre_specific is the most precise signal; camel/snake both accepted).
        $dcAttrs = array_filter([
            ItemField::GENRE_SPECIFIC => $record[ItemField::GENRE_SPECIFIC] ?? $record['genreSpecific'] ?? null,
            ItemField::GENRE_BASIC    => $record[ItemField::GENRE_BASIC] ?? $record['genreBasic'] ?? null,
            'type_of_resource'        => $record['type_of_resource'] ?? $record['typeOfResource'] ?? null,
        ]);
        if ($dcAttrs) {
            $resolved = self::fromDcAttrs($dcAttrs);
            if ($resolved !== self::OBJECT) {
                return $resolved;
            }
        }

        // Common type/classification fields used by museum APIs
        $candidates = ['type', 'classification', 'objectType', 'object_type',
                       'category', 'tipo', 'classe', 'klasse'];
        foreach ($candidates as $field) {
            $value = $record[$field] ?? null;
            if ($value === null || $value === '' || $value === []) {
                continue;
            }
            foreach ((array) $value as $v) {
                if ($type = self::lookupGenreType((string) $v, self::GENRE_SPECIFIC_MAP, self::GENRE_BASIC_MAP)) {
                    return $type;
                }
            }
        }

        return self::OBJECT;
    }

    private static function lookupGenreType(string $value, array $primaryMap, array $fallbackMap): ?string
    {
        return self::lookupMappedType($value, $primaryMap)
            ?? self::lookupMappedType($value, $fallbackMap)
            ?? self::lookupSuffixType($value);
    }

    private static function lookupMappedType(string $value, array $map): ?string
    {
        foreach (self::lookupKeys($value) as $key) {
            if (isset($map[$key])) {
                return $map[$key];
            }
        }

        return null;
    }

    /**
     * Common museum feeds often use descriptive phrases such as "bark paintings".
     * After singularization, a suffix check catches those without adding every phrase.
     */
    private static function lookupSuffixType(string $value): ?string
    {
        foreach (self::lookupKeys($value) as $key) {
            foreach (self::GENRE_SUFFIX_MAP as $suffix => $type) {
                if ($key === $suffix || str_ends_with($key, ' '.$suffix)) {
                    return $type;
                }
            }
        }

        return null;
    }

    private static function lookupKeys(string $value): array
    {
        $key = strtolower(trim($value));
        if ($key === '') {
            return [];
        }

        $keys = [$key];
        $inflector = self::$inflector ??= new EnglishInflector();

        foreach ($inflector->singularize($key) as $singular) {
            $singular = strtolower(trim($singular));
            if ($singular !== '') {
                $keys[] = $singular;
            }
        }

        $lastSpace = strrpos($key, ' ');
        if ($lastSpace !== false) {
            $prefix = substr($key, 0, $lastSpace + 1);
            $lastWord = substr($key, $lastSpace + 1);
            foreach ($inflector->singularize($lastWord) as $singularLastWord) {
                $singularLastWord = strtolower(trim($singularLastWord));
                if ($singularLastWord !== '') {
                    $keys[] = $prefix.$singularLastWord;
                }
            }
        }

        return array_values(array_unique($keys));
    }

    /**
     * Does this content type warrant running OCR?
     */
    public static function needsOcr(string $contentType): bool
    {
        return in_array($contentType, self::OCR_TYPES, true);
    }

    /**
     * Does this content type warrant visual AI description?
     */
    public static function needsVisualAi(string $contentType): bool
    {
        return in_array($contentType, self::VISUAL_AI_TYPES, true);
    }

    /**
     * Return the authoritative LOC/DCMI URI for a content type slug.
     */
    public static function uri(string $contentType): ?string
    {
        return self::URIS[$contentType] ?? null;
    }

    /**
     * Map a ContentType slug to the canonical BaseItemDto subclass FQCN.
     * Falls back to GenericItemDto when no specific subclass exists.
     */
    public static function dtoClass(string $contentType): string
    {
        return match ($contentType) {
            self::PHOTOGRAPH, self::STEREOGRAPH, self::SLIDE, self::NEGATIVE
                => PhotographDto::class,
            self::POSTCARD
                => PostcardDto::class,
            self::DRAWING, self::PRINT, self::POSTER
                => DrawingDto::class,
            self::PAINTING
                => PaintingDto::class,
            self::MAP, self::ATLAS
                => MapDto::class,
            self::NEWSPAPER, self::PERIODICAL
                => NewspaperDto::class,
            self::CORRESPONDENCE
                => CorrespondenceDto::class,
            self::MANUSCRIPT
                => ManuscriptDto::class,
            self::BOOK
                => BookDto::class,
            self::DOCUMENT
                => DocumentDto::class,
            self::EPHEMERA
                => EphemeraDto::class,
            self::AUDIO
                => AudioDto::class,
            self::FILM
                => FilmDto::class,
            self::OBJECT
                => ArtifactDto::class,
            self::COIN
                => CoinDto::class,
            self::SCULPTURE
                => SculptureDto::class,
            self::PERSON
                => PersonDto::class,
            default => GenericItemDto::class,
        };
    }
}
