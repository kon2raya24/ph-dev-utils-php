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

    public function testToWordsFilipinoZero(): void
    {
        $this->assertSame('Sero piso', Peso::toWordsFilipino(0));
    }

    public function testToWordsFilipinoSingles(): void
    {
        $this->assertSame('Isang piso', Peso::toWordsFilipino(1));
        $this->assertSame('Dalawang piso', Peso::toWordsFilipino(2));
        $this->assertSame('Limang piso', Peso::toWordsFilipino(5));
        $this->assertSame('Anim na piso', Peso::toWordsFilipino(6));
    }

    public function testToWordsFilipinoTeensAndTens(): void
    {
        $this->assertSame('Sampung piso', Peso::toWordsFilipino(10));
        $this->assertSame('Labing-isang piso', Peso::toWordsFilipino(11));
        $this->assertSame('Labindalawang piso', Peso::toWordsFilipino(12));
        $this->assertSame('Dalawampung piso', Peso::toWordsFilipino(20));
        $this->assertSame("Dalawampu't isang piso", Peso::toWordsFilipino(21));
        $this->assertSame("Siyamnapu't siyam na piso", Peso::toWordsFilipino(99));
    }

    public function testToWordsFilipinoHundreds(): void
    {
        $this->assertSame('Isang daang piso', Peso::toWordsFilipino(100));
        $this->assertSame('Dalawang daang piso', Peso::toWordsFilipino(200));
        $this->assertSame('Apat na raang piso', Peso::toWordsFilipino(400));
        $this->assertSame('Anim na raang piso', Peso::toWordsFilipino(600));
        $this->assertSame('Siyam na raang piso', Peso::toWordsFilipino(900));
    }

    public function testToWordsFilipinoThousandsAndBeyond(): void
    {
        $this->assertSame('Isang libong piso', Peso::toWordsFilipino(1000));
        $this->assertSame('Sampung libong piso', Peso::toWordsFilipino(10000));
        $this->assertSame('Isang milyong piso', Peso::toWordsFilipino(1_000_000));
        $this->assertSame('Isang bilyong piso', Peso::toWordsFilipino(1_000_000_000));
    }

    public function testToWordsFilipinoWorkedExample(): void
    {
        $this->assertSame(
            "Labindalawang libo tatlong daan apatnapu't lima at 67/100 piso",
            Peso::toWordsFilipino(12345.67),
        );
    }

    public function testToWordsFilipinoCentavosOnly(): void
    {
        $this->assertSame('Sero at 50/100 piso', Peso::toWordsFilipino(0.5));
        $this->assertSame('Sero at 05/100 piso', Peso::toWordsFilipino(0.05));
    }

    public function testToWordsFilipinoRoundsUpToWholePeso(): void
    {
        $this->assertSame('Isang piso', Peso::toWordsFilipino(0.999));
    }

    public function testToWordsFilipinoRejectsNegative(): void
    {
        $this->expectException(\OutOfRangeException::class);
        Peso::toWordsFilipino(-1);
    }

    public function testToWordsFilipinoRejectsTooLarge(): void
    {
        $this->expectException(\OutOfRangeException::class);
        Peso::toWordsFilipino(1e15);
    }
}
