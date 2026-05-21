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

    /**
     * List cities and municipalities, optionally filtered by province / region / isCity / isCapital.
     *
     * @param array{province?: ?string, region?: string, isCity?: bool, isCapital?: bool} $filter
     * @return array<int, array{code: string, name: string, province: ?string, region: string, isCity: bool, isCapital: bool}>
     */
    public static function listCitiesMunicipalities(array $filter = []): array
    {
        $data = DataLoader::load('psgc-cities-municipalities-2024');
        $out = $data['cities_municipalities'];

        if (array_key_exists('province', $filter)) {
            $province = $filter['province'];
            $out = array_filter($out, fn($c) => $c['province'] === $province);
        }
        if (isset($filter['region'])) {
            $region = $filter['region'];
            $out = array_filter($out, fn($c) => $c['region'] === $region);
        }
        if (isset($filter['isCity'])) {
            $isCity = (bool) $filter['isCity'];
            $out = array_filter($out, fn($c) => $c['isCity'] === $isCity);
        }
        if (isset($filter['isCapital'])) {
            $isCapital = (bool) $filter['isCapital'];
            $out = array_filter($out, fn($c) => $c['isCapital'] === $isCapital);
        }
        return array_values($out);
    }

    /**
     * Look up a city/municipality by PSGC 6-digit code or name (case-insensitive,
     * with both "City of X" and "X City" spellings normalized).
     *
     * @return array{code: string, name: string, province: ?string, region: string, isCity: bool, isCapital: bool}|null
     */
    public static function findCityMunicipality(string $query): ?array
    {
        $q = strtolower(trim($query));
        if ($q === '') return null;
        $normQ = self::normalizeCityName($query);

        $data = DataLoader::load('psgc-cities-municipalities-2024');
        foreach ($data['cities_municipalities'] as $c) {
            if ($c['code'] === $q) return $c;
            $name = strtolower($c['name']);
            if ($name === $q) return $c;
            if (self::normalizeCityName($c['name']) === $normQ) return $c;
        }
        return null;
    }

    private static function normalizeCityName(string $s): string
    {
        $s = strtolower(trim($s));
        $s = preg_replace('/^city of\s+/', '', $s);
        $s = preg_replace('/\s+city$/', '', $s);
        return $s;
    }
}
