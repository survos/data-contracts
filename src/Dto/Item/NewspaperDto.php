<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Metadata\ContentType;
use Survos\FieldBundle\Attribute\Field;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

#[SchemaOrg('Newspaper')]
class NewspaperDto extends AbstractWorkDto
{
    public ?string $publisher    = null;
    public ?string $pubPlace     = null;
    public ?string $volume       = null;
    public ?string $issueNumber  = null;
    public ?string $edition      = null;
    /** Has searchable full-text pages */
    public bool    $hasTranscription = false;

    /**
     * Where this title can be read online, e.g. chronicling-america, virginia-chronicle; empty for
     * a title known only from a directory. A title-level fact set by catalog surveys (harvest's
     * survey:newspapers); issue and page rows leave it null.
     *
     * @var list<string>|null
     */
    #[Field(facet: true, filterable: true, group: 'Availability')]
    public ?array $digitizedIn = null;

    public static function contentType(): string { return ContentType::NEWSPAPER; }
}
