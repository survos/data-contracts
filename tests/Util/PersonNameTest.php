<?php

declare(strict_types=1);

namespace Survos\DataContracts\Tests\Util;

use PHPUnit\Framework\TestCase;
use Survos\DataContracts\Metadata\ContentType;
use Survos\DataContracts\Util\PersonName;

final class PersonNameTest extends TestCase
{
    public function testPersonalNamesAndOrganizationsUseCanonicalTypes(): void
    {
        foreach (['John Smith', 'Smith, John', 'Fergusson, James, 1808-1886', 'María de la Cruz', 'J. R. Smith'] as $name) {
            self::assertSame(ContentType::PERSON, PersonName::parse($name)['kind'], $name);
        }
        foreach (['Boston Public Library', 'Library, British', 'Special Collections Research Center, William & Mary Libraries', 'Office of Undergraduate Admission, William & Mary', 'John Smith Foundation'] as $name) {
            self::assertSame(ContentType::ORGANIZATION, PersonName::parse($name)['kind'], $name);
        }
        self::assertSame(['kind' => ContentType::PERSON, 'name' => 'Fergusson, James', 'birth' => 1808, 'death' => 1886], PersonName::parse('Fergusson, James, 1808-1886'));
    }
}
