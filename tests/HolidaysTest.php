<?php

declare(strict_types=1);

namespace PhDevUtils\Tests;

use PhDevUtils\Holidays;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

final class HolidaysTest extends TestCase
{
    public function testListHolidayYears(): void
    {
        $this->assertSame([2025, 2026], Holidays::listHolidayYears());
    }

    public function testListHolidaysOfYear(): void
    {
        $h = Holidays::listHolidaysOfYear(2026);
        $this->assertGreaterThanOrEqual(20, count($h));
        $this->assertSame('2026-01-01', $h[0]['date']);
        $this->assertSame("New Year's Day", $h[0]['name']);
    }

    public function testListHolidaysUnsupportedYear(): void
    {
        $this->expectException(\OutOfRangeException::class);
        Holidays::listHolidaysOfYear(2030);
    }

    public function testFindHolidayChristmas(): void
    {
        $h = Holidays::findHoliday('2026-12-25');
        $this->assertNotNull($h);
        $this->assertSame('Christmas Day', $h['name']);
        $this->assertSame('regular', $h['type']);
    }

    public function testFindHolidayNonHoliday(): void
    {
        $this->assertNull(Holidays::findHoliday('2026-05-21'));
    }

    public function testFindHolidayYearOutsideDataset(): void
    {
        $this->assertNull(Holidays::findHoliday('2030-12-25'));
    }

    public function testFindHolidayWithDateTimeInstance(): void
    {
        $h = Holidays::findHoliday(new DateTimeImmutable('2026-12-25'));
        $this->assertSame('Christmas Day', $h['name']);
    }

    public function testFindHolidayRejectsMalformedDate(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Holidays::findHoliday('12/25/2026');
    }

    public function testEidlFitr2026Proclamation(): void
    {
        $h = Holidays::findHoliday('2026-03-20');
        $this->assertSame('1189', $h['proclamation']);
    }

    public function testIsHolidayAnyType(): void
    {
        $this->assertTrue(Holidays::isHoliday('2026-12-25'));
        $this->assertTrue(Holidays::isHoliday('2026-02-25')); // special_working
        $this->assertFalse(Holidays::isHoliday('2026-05-21'));
    }

    public function testIsHolidayFilteredByType(): void
    {
        $this->assertFalse(Holidays::isHoliday('2026-02-25', ['types' => ['regular', 'special_non_working']]));
        $this->assertTrue(Holidays::isHoliday('2026-12-25', ['types' => ['regular', 'special_non_working']]));
    }

    public function testNextHolidayDefault(): void
    {
        $h = Holidays::nextHoliday('2026-05-21');
        $this->assertSame('2026-06-12', $h['date']);
        $this->assertSame('Independence Day', $h['name']);
    }

    public function testNextHolidayInclusiveDefault(): void
    {
        $h = Holidays::nextHoliday('2026-12-25');
        $this->assertSame('2026-12-25', $h['date']);
    }

    public function testNextHolidayInclusiveFalse(): void
    {
        $h = Holidays::nextHoliday('2026-12-25', ['inclusive' => false]);
        $this->assertSame('2026-12-30', $h['date']);
    }

    public function testNextHolidayCrossesYear(): void
    {
        $h = Holidays::nextHoliday('2025-12-31', ['inclusive' => false]);
        $this->assertSame('2026-01-01', $h['date']);
    }

    public function testNextHolidayBeyondDataset(): void
    {
        $this->assertNull(Holidays::nextHoliday('2027-01-01'));
    }

    public function testPayMultiplier(): void
    {
        $this->assertSame(2.0, Holidays::PAY_MULTIPLIER['regular']);
        $this->assertSame(1.3, Holidays::PAY_MULTIPLIER['special_non_working']);
        $this->assertSame(1.0, Holidays::PAY_MULTIPLIER['special_working']);
    }
}
