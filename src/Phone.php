<?php

declare(strict_types=1);

namespace PhDevUtils;

final class Phone
{
    private const AREA_CODES = [
        '2'  => 'Metro Manila',
        '32' => 'Cebu',
        '33' => 'Iloilo',
        '34' => 'Bacolod / Negros Occidental',
        '35' => 'Dumaguete / Negros Oriental',
        '36' => 'Aklan / Capiz',
        '38' => 'Bohol',
        '42' => 'Quezon',
        '43' => 'Batangas',
        '44' => 'Bulacan',
        '45' => 'Pampanga / Tarlac',
        '46' => 'Cavite',
        '47' => 'Zambales / Bataan',
        '48' => 'Palawan',
        '49' => 'Laguna',
        '52' => 'Albay',
        '53' => 'Leyte',
        '54' => 'Camarines Sur',
        '55' => 'Northern Samar',
        '56' => 'Samar',
        '62' => 'Zamboanga City',
        '63' => 'Lanao del Norte',
        '64' => 'Cotabato',
        '65' => 'Marawi / Lanao del Sur',
        '68' => 'Zamboanga del Sur',
        '74' => 'Baguio / Benguet',
        '75' => 'Pangasinan',
        '77' => 'Ilocos',
        '78' => 'Cagayan',
        '82' => 'Davao',
        '83' => 'General Santos',
        '84' => 'Tagum / Davao del Norte',
        '85' => 'Butuan / Agusan',
        '86' => 'Surigao',
        '87' => 'Davao Occidental',
        '88' => 'Cagayan de Oro / Misamis Oriental',
    ];

    /**
     * @return array{e164: string, national: string, network: ?string}|null
     */
    public static function parseMobile(string $input): ?array
    {
        $d = self::digits($input);

        if (str_starts_with($d, '63') && strlen($d) === 12) $d = '0' . substr($d, 2);
        if (strlen($d) === 10 && preg_match('/^[89]/', $d)) $d = '0' . $d;

        if (strlen($d) !== 11 || !preg_match('/^0[89]/', $d)) return null;

        $prefix = substr($d, 0, 4);
        $network = self::lookupNetwork($prefix);

        return [
            'e164' => '+63' . substr($d, 1),
            'national' => $d,
            'network' => $network,
        ];
    }

    /**
     * @return array{e164: string, national: string, areaCode: string, area: ?string}|null
     */
    public static function parseLandline(string $input): ?array
    {
        $d = self::digits($input);
        if (str_starts_with($d, '63')) $d = substr($d, 2);
        if (str_starts_with($d, '0'))  $d = substr($d, 1);

        $len = strlen($d);
        if ($len < 8 || $len > 10) return null;

        foreach ([2, 1] as $aLen) {
            $area = substr($d, 0, $aLen);
            if (!isset(self::AREA_CODES[$area])) continue;

            $subscriber = substr($d, $aLen);
            $sLen = strlen($subscriber);
            if ($sLen < 6 || $sLen > 8) continue;

            return [
                'e164' => '+63' . $d,
                'national' => '(0' . $area . ') ' . $subscriber,
                'areaCode' => $area,
                'area' => self::AREA_CODES[$area],
            ];
        }
        return null;
    }

    private static function lookupNetwork(string $prefix): ?string
    {
        $data = DataLoader::load('network-prefixes');
        foreach ($data as $network => $prefixes) {
            if (str_starts_with((string) $network, '_')) continue;
            if (in_array($prefix, $prefixes, true)) return (string) $network;
        }
        return null;
    }

    private static function digits(string $s): string
    {
        return preg_replace('/\D/', '', $s) ?? '';
    }
}
