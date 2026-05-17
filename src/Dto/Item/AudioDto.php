<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;

#[ClassMeta(
    label: 'Audio',
    description: 'Sound recordings: oral histories, music, radio broadcasts, and field recordings.',
)]
class AudioDto extends BaseItemDto
{
    /** Physical carrier e.g. "Cylinder", "LP", "Cassette", "Digital" */
    public ?string $medium      = null;
    public ?string $duration    = null;
    public bool    $hasTranscription = false;

    public static function contentType(): string { return ContentType::AUDIO; }
}
