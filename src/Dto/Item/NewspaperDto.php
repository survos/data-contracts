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

    /**
     * Where to read it, keyed like digitizedIn: ['chronicling-america' => 'https://www.loc.gov/item/…/'].
     *
     * @var array<string, string>|null
     */
    public ?array $digitizedUrls = null;

    /** Publication frequency, grouped for faceting: Daily, Weekly, Semiweekly, Monthly, … */
    #[Field(facet: true, filterable: true, group: 'Publication')]
    public ?string $frequency = null;

    /** The catalog's own wording, e.g. "Daily (except Sun.)"; kept when it says more than the group. */
    public ?string $frequencyNote = null;

    /** Still publishing, per the directory (an end year of 9999). Null when the source doesn't say. */
    #[Field(facet: true, filterable: true, group: 'Publication')]
    public ?bool $ongoing = null;

    /** Issues held online, where a source counts them (Chronicling America does). */
    public ?int $issueCount = null;

    /** @var list<string>|null */
    public ?array $oclc = null;

    /** @var list<string>|null */
    public ?array $lccn = null;

    public static function contentType(): string { return ContentType::NEWSPAPER; }
}
