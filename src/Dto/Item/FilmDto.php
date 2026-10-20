<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\Lingua\Contracts\Attribute\Translatable;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

#[ClassMeta(
    label: 'Film',
    description: 'Moving image works: newsreels, home movies, documentaries, and silent films.',
)]
#[SchemaOrg('Movie')]
class FilmDto extends AbstractWorkDto
{
    /** Physical format e.g. "16mm", "35mm", "VHS", "Digital" */
    public ?string $format = null;
    public ?string $duration = null;
    public bool $hasTranscription = false;

    /**
     * Full speech transcript as paragraph-sized chunks, for films whose transcript is source text rather
     * than OCR (YouTube oral histories). A list, because #[Translatable] on a list<string> translates each
     * element on its own, and one whole transcript (up to ~90,000 characters) times out the engine.
     * Film only: marking BaseItemDto::$ocrText translatable would queue every OCR'd newspaper page.
     *
     * @var list<string>|null
     */
    #[Translatable]
    public ?array $transcript = null;

    public static function contentType(): string
    {
        return ContentType::FILM;
    }
}
