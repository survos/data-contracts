<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

#[ClassMeta(
    label: 'Film',
    description: 'Moving image works: newsreels, home movies, documentaries, and silent films.',
)]
#[SchemaOrg('Movie')]
class FilmDto extends AbstractWorkDto
{
    /** Physical format e.g. "16mm", "35mm", "VHS", "Digital" */
    public ?string $format   = null;
    public ?string $duration = null;
    public bool    $hasTranscription = false;

    public static function contentType(): string { return ContentType::FILM; }
}
