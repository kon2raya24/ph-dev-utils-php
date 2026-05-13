<?php

declare(strict_types=1);

namespace PhDevUtils\Tests;

use PHPUnit\Framework\TestCase;
use PhDevUtils\Address;

final class AddressTest extends TestCase
{
    public function testListRegions(): void
    {
        $this->assertCount(17, Address::listRegions());
    }

    public function testFindRegionByCode(): void
    {
        $this->assertSame('Central Visayas', Address::findRegion('07')['name']);
    }

    public function testFindRegionByName(): void
    {
        $this->assertSame('05', Address::findRegion('Bicol Region')['code']);
    }

    public function testFindRegionByDesignation(): void
    {
        $this->assertSame('13', Address::findRegion('NCR')['code']);
    }

    public function testListProvincesInRegion(): void
    {
        $names = array_column(Address::listProvinces('07'), 'name');
        $this->assertContains('Cebu', $names);
        $this->assertContains('Bohol', $names);
    }

    public function testFindProvince(): void
    {
        $this->assertSame('0722', Address::findProvince('Cebu')['code']);
    }

    public function testFindProvinceUnknown(): void
    {
        $this->assertNull(Address::findProvince('Atlantis'));
    }
}
