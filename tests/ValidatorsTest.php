<?php

declare(strict_types=1);

namespace PhDevUtils\Tests;

use PHPUnit\Framework\TestCase;
use PhDevUtils\Validators\Tin;
use PhDevUtils\Validators\Sss;
use PhDevUtils\Validators\PhilHealth;
use PhDevUtils\Validators\PagIbig;
use PhDevUtils\Validators\NationalId;
use PhDevUtils\Validators\Umid;
use PhDevUtils\Validators\Passport;
use PhDevUtils\Validators\Prc;

final class ValidatorsTest extends TestCase
{
    public function testTin(): void
    {
        $this->assertTrue(Tin::validate('123-456-789'));
        $this->assertTrue(Tin::validate('123-456-789-000'));
        $this->assertFalse(Tin::validate('12345'));
        $this->assertSame('123-456-789', Tin::format('123456789'));
        $this->assertSame('123-456-789-000', Tin::format('123456789000'));
        $this->assertNull(Tin::format('12345'));
    }

    public function testSss(): void
    {
        $this->assertTrue(Sss::validate('34-1234567-8'));
        $this->assertFalse(Sss::validate('123'));
        $this->assertSame('34-1234567-8', Sss::format('3412345678'));
    }

    public function testPhilHealth(): void
    {
        $this->assertTrue(PhilHealth::validate('12-345678901-2'));
        $this->assertSame('12-345678901-2', PhilHealth::format('123456789012'));
    }

    public function testPagIbig(): void
    {
        $this->assertTrue(PagIbig::validate('1234-5678-9012'));
        $this->assertSame('1234-5678-9012', PagIbig::format('123456789012'));
    }

    public function testNationalId(): void
    {
        $this->assertTrue(NationalId::validate('1234-5678-9012-3456'));
        $this->assertSame('1234-5678-9012-3456', NationalId::format('1234567890123456'));
        // The 12-digit PSN length must NOT validate as a PCN.
        $this->assertFalse(NationalId::validate('123456789012'));
        $this->assertNull(NationalId::format('123456789012'));
    }

    public function testUmid(): void
    {
        $this->assertTrue(Umid::validate('1234-5678901-2'));
        $this->assertSame('1234-5678901-2', Umid::format('123456789012'));
        $this->assertFalse(Umid::validate('12345'));
        $this->assertNull(Umid::format('12345'));
    }

    public function testPassport(): void
    {
        $this->assertTrue(Passport::validate('P1234567A'));
        $this->assertSame('P1234567A', Passport::format('p1234567a'));
        $this->assertTrue(Passport::validate('XX1234567'));
        $this->assertSame('XX1234567', Passport::format('xx1234567'));
        $this->assertFalse(Passport::validate('1234567'));
        $this->assertFalse(Passport::validate('P123456789'));
        $this->assertNull(Passport::format('nope'));
    }

    public function testPrc(): void
    {
        $this->assertTrue(Prc::validate('1234567'));
        $this->assertSame('1234567', Prc::format('123-4567'));
        $this->assertFalse(Prc::validate('123456'));
        $this->assertNull(Prc::format('12345678'));
    }
}
