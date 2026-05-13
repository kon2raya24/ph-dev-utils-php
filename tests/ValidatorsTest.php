<?php

declare(strict_types=1);

namespace PhDevUtils\Tests;

use PHPUnit\Framework\TestCase;
use PhDevUtils\Validators\Tin;
use PhDevUtils\Validators\Sss;
use PhDevUtils\Validators\PhilHealth;
use PhDevUtils\Validators\PagIbig;

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
}
