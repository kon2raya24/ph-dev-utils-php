<?php

declare(strict_types=1);

namespace PhDevUtils\Tests;

use PHPUnit\Framework\TestCase;
use PhDevUtils\Peso;

final class PesoTest extends TestCase
{
    public function testFormatDefault(): void
    {
        $this->assertSame('₱1,234.50', Peso::format(1234.5));
    }

    public function testFormatZero(): void
    {
        $this->assertSame('₱0.00', Peso::format(0));
    }

    public function testFormatNegative(): void
    {
        $this->assertSame('-₱99.90', Peso::format(-99.9));
    }

    public function testFormatPhpSymbol(): void
    {
        $this->assertSame('PHP 1,000.00', Peso::format(1000, ['symbol' => 'php']));
    }

    public function testParse(): void
    {
        $this->assertSame(1234.5, Peso::parse('₱1,234.50'));
        $this->assertSame(99.0, Peso::parse('PHP 99.00'));
        $this->assertNull(Peso::parse('abc'));
    }

    public function testToWordsWholePeso(): void
    {
        $this->assertSame('one peso', Peso::toWords(1));
        $this->assertSame('twenty-five pesos', Peso::toWords(25));
    }

    public function testToWordsWithCentavos(): void
    {
        $this->assertSame('one peso and fifty centavos', Peso::toWords(1.5));
    }
}
