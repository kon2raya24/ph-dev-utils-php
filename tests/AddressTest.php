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

    // PSGC cities/municipalities (v0.2)

    public function testListCitiesMunicipalitiesTotal(): void
    {
        $all = Address::listCitiesMunicipalities();
        $this->assertGreaterThanOrEqual(1600, count($all));
        $this->assertLessThanOrEqual(1700, count($all));
    }

    public function testListCitiesMunicipalitiesFilterProvince(): void
    {
        $cebu = Address::listCitiesMunicipalities(['province' => '0722']);
        $this->assertGreaterThan(40, count($cebu));
        foreach ($cebu as $c) {
            $this->assertSame('0722', $c['province']);
        }
    }

    public function testListCitiesMunicipalitiesFilterRegion(): void
    {
        $ncr = Address::listCitiesMunicipalities(['region' => '13']);
        $this->assertCount(17, $ncr);
    }

    public function testListCitiesMunicipalitiesIsCityFilter(): void
    {
        $cities = Address::listCitiesMunicipalities(['region' => '07', 'isCity' => true]);
        foreach ($cities as $c) {
            $this->assertTrue($c['isCity']);
        }
    }

    public function testFindCityMunicipalityByCode(): void
    {
        $batac = Address::findCityMunicipality('012805');
        $this->assertStringContainsString('Batac', $batac['name']);
        $this->assertTrue($batac['isCity']);
    }

    public function testFindCityMunicipalityByName(): void
    {
        $this->assertSame('012801', Address::findCityMunicipality('Adams')['code']);
    }

    public function testFindCityMunicipalityPrefixStripping(): void
    {
        $manila = Address::findCityMunicipality('Manila');
        $this->assertNotNull($manila);
        $this->assertSame('13', $manila['region']);
        $this->assertNull($manila['province']);
    }

    public function testFindCityMunicipalitySuffixStripping(): void
    {
        // PSA stores "City of Cebu"; both "Cebu City" and "City of Cebu" should resolve.
        $this->assertNotNull(Address::findCityMunicipality('Cebu City'));
        $this->assertNotNull(Address::findCityMunicipality('City of Cebu'));
    }

    public function testFindCityMunicipalityUnknown(): void
    {
        $this->assertNull(Address::findCityMunicipality('Atlantis'));
    }

    public function testHUCEntries(): void
    {
        $hucs = array_values(array_filter(
            Address::listCitiesMunicipalities(),
            fn($c) => $c['province'] === null,
        ));
        $this->assertCount(19, $hucs);
        $munis = array_values(array_filter($hucs, fn($c) => !$c['isCity']));
        $this->assertCount(1, $munis);
        $this->assertSame('Pateros', $munis[0]['name']);
    }
}
