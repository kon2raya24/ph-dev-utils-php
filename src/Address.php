<?php

declare(strict_types=1);

namespace PhDevUtils;

final class Address
{
    public static function listRegions(): array
    {
        return DataLoader::load('regions');
    }

    public static function findRegion(string $query): ?array
    {
        $q = strtolower(trim($query));
        if ($q === '') return null;

        foreach (self::listRegions() as $r) {
            if (
                strtolower($r['code']) === $q ||
                strtolower($r['name']) === $q ||
                strtolower($r['designation']) === $q
            ) {
                return $r;
            }
        }
        return null;
    }

    public static function listProvinces(?string $regionCode = null): array
    {
        $all = DataLoader::load('provinces');
        if ($regionCode === null) return $all;
        return array_values(array_filter($all, fn($p) => $p['region'] === $regionCode));
    }

    public static function findProvince(string $query): ?array
    {
        $q = strtolower(trim($query));
        if ($q === '') return null;

        foreach (DataLoader::load('provinces') as $p) {
            if (strtolower($p['code']) === $q || strtolower($p['name']) === $q) {
                return $p;
            }
        }
        return null;
    }
}
