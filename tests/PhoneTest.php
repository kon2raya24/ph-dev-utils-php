<?php

declare(strict_types=1);

namespace PhDevUtils\Tests;

use PHPUnit\Framework\TestCase;
use PhDevUtils\Phone;

final class PhoneTest extends TestCase
{
    public function testMobile09Prefix(): void
    {
        $r = Phone::parseMobile('09171234567');
        $this->assertNotNull($r);
        $this->assertSame('+639171234567', $r['e164']);
        $this->assertSame('Globe', $r['network']);
    }

    public function testMobilePlus63(): void
    {
        $r = Phone::parseMobile('+63 917 123 4567');
        $this->assertSame('+639171234567', $r['e164']);
    }

    public function testMobileSmart(): void
    {
        $this->assertSame('Smart', Phone::parseMobile('09181234567')['network']);
    }

    public function testMobileDito(): void
    {
        $this->assertSame('DITO', Phone::parseMobile('08951234567')['network']);
    }

    public function testInvalidMobile(): void
    {
        $this->assertNull(Phone::parseMobile('12345'));
    }

    public function testLandlineMetroManila(): void
    {
        $r = Phone::parseLandline('(02) 8123-4567');
        $this->assertNotNull($r);
        $this->assertSame('2', $r['areaCode']);
        $this->assertSame('Metro Manila', $r['area']);
    }

    public function testLandlineCebu(): void
    {
        $r = Phone::parseLandline('(032) 123-4567');
        $this->assertSame('32', $r['areaCode']);
        $this->assertSame('Cebu', $r['area']);
    }
}
