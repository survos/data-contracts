<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\ClassMeta;
use Survos\DataContracts\Metadata\ContentType;

#[ClassMeta(
    label: 'Interview',
    description: 'Multi-speaker recorded conversations: oral histories, testimonies, and interviews — distinct from a single-speaker speech or reading, so templates and AI context can expect dialogue.',
)]
class InterviewDto extends AudioDto
{
    /** Known participants when available, e.g. ["Interviewer: John Henry Faulk", "Interviewee: Harriet Smith"]. Speaker-turn text itself lives on each Row's Page.dialogue, not here. */
    public ?array $speakers = null;

    public static function contentType(): string { return ContentType::INTERVIEW; }
}
