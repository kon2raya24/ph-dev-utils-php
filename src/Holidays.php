<?php

declare(strict_types=1);

namespace PhDevUtils;

use DateTimeInterface;

/**
 * PH holidays helpers — bundled regular + special holiday data for 2025-2026.
 *
 * Sources: Proclamation 727 (2025), Proclamation 1006 (2026), plus separate
 * proclamations for Islamic holidays (Eid'l Fitr, Eid'l Adha). New years are
 * added per package release as the Office of the President issues annual proclamations.
 */
final class Holidays
{
    /** DOLE pay-rule multipliers for hours worked on each holiday type. */
    public const PAY_MULTIPLIER = [
        'regular' => 2.0,
        'special_non_working' => 1.3,
        'special_working' => 1.0,
    ];

    /** @return array<int> */
    public static function listHolidayYears(): array
    {
        $years = [];
        foreach ([2025, 2026] as $y) {
            try {
                DataLoader::load("holidays-{$y}");
                $years[] = $y;
            } catch (\Throwable) {
                // skip if file missing
            }
        }
        sort($years);
        return $years;
    }

    /** @return array<int, array{date: string, name: string, type: string, proclamation?: string}> */
    public static function listHolidaysOfYear(int $year): array
    {
        $available = self::listHolidayYears();
        if (!in_array($year, $available, true)) {
            $years = implode(', ', $available);
            throw new \OutOfRangeException("Holidays::listHolidaysOfYear: no data for year {$year}. Available: {$years}. PH holidays are proclaimed annually; future years are added per package release.");
        }
        $data = DataLoader::load("holidays-{$year}");
        return $data['holidays'];
    }

    /**
     * Look up the holiday on a specific date.
     *
     * @return array{date: string, name: string, type: string, proclamation?: string}|null
     */
    public static function findHoliday(string|DateTimeInterface $date): ?array
    {
        $iso = self::normalizeDate($date);
        $year = (int) substr($iso, 0, 4);
        $available = self::listHolidayYears();
        if (!in_array($year, $available, true)) return null;
        $data = DataLoader::load("holidays-{$year}");
        foreach ($data['holidays'] as $h) {
            if ($h['date'] === $iso) return $h;
        }
        return null;
    }

    /**
     * True if the date is a PH holiday.
     *
     * @param array{types?: array<string>} $opts
     */
    public static function isHoliday(string|DateTimeInterface $date, array $opts = []): bool
    {
        $h = self::findHoliday($date);
        if ($h === null) return false;
        if (!isset($opts['types'])) return true;
        return in_array($h['type'], $opts['types'], true);
    }

    /**
     * Find the next holiday on or after the given date.
     *
     * @param array{types?: array<string>, inclusive?: bool} $opts
     * @return array{date: string, name: string, type: string, proclamation?: string}|null
     */
    public static function nextHoliday(string|DateTimeInterface $from, array $opts = []): ?array
    {
        $iso = self::normalizeDate($from);
        $inclusive = $opts['inclusive'] ?? true;
        $types = $opts['types'] ?? null;
        foreach (self::listHolidayYears() as $year) {
            $data = DataLoader::load("holidays-{$year}");
            foreach ($data['holidays'] as $h) {
                if ($types !== null && !in_array($h['type'], $types, true)) continue;
                if ($inclusive ? $h['date'] >= $iso : $h['date'] > $iso) return $h;
            }
        }
        return null;
    }

    private static function normalizeDate(string|DateTimeInterface $input): string
    {
        if ($input instanceof DateTimeInterface) {
            return $input->format('Y-m-d');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input)) {
            throw new \InvalidArgumentException("Holidays: date must be in YYYY-MM-DD format, got \"{$input}\"");
        }
        return $input;
    }
}
