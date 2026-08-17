<?php
declare(strict_types=1);

namespace Survos\DataContracts\Dto\Item;

use Survos\DataContracts\Attribute\PropertyMeta;
use Survos\SchemaOrgBundle\Attribute\SchemaOrg;

/**
 * Base DTO for works of art (sculpture, painting, drawing, print, …).
 *
 * An artwork is a physical object, so it inherits material / dimensions / weight; this layer adds
 * the art-historical attributes (style, movement, signature) that distinguish art from generic
 * artifacts, specimens or documents. Concrete subclasses declare their ContentType.
 */
#[SchemaOrg('VisualArtwork')]
abstract class ArtworkDto extends PhysicalObjectDto
{
    /** Artistic style (e.g. Baroque, Missioneiro, Gothic). */
    #[PropertyMeta(label: 'Style', description: 'Artistic style.', facet: true)]
    public ?string $style = null;

    /** Art movement / school (e.g. Barroco, Modernismo). */
    #[PropertyMeta(label: 'Movement', description: 'Art movement or school.', facet: true)]
    public ?string $movement = null;

    /** Signature / maker's marks, where recorded. */
    public ?string $signed = null;
}
