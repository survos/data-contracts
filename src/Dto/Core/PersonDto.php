<?php

declare(strict_types=1);

namespace Survos\DataContracts\Dto\Core;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;

#[ClassMeta(
    label: 'Person',
    description: 'People associated with collection records: creators, contributors, subjects, interviewees, performers, and other named agents.',
)]
final class PersonDto extends BasePersonDto
{
    public static function contentType(): string
    {
        return ContentType::PERSON;
    }
}
