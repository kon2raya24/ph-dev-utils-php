<?php

declare(strict_types=1);

namespace PhDevUtils;

final class Peso
{
    private const ONES = [
        'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
        'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
        'seventeen', 'eighteen', 'nineteen',
    ];

    private const TENS = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

    /**
     * @param array{decimals?: int, symbol?: 'peso'|'php'|'none'} $opts
     */
    public static function format(float $value, array $opts = []): string
    {
        if (!is_finite($value)) return '';

        $decimals = $opts['decimals'] ?? 2;
        $symbol = $opts['symbol'] ?? 'peso';

        $sign = $value < 0 ? '-' : '';
        $body = number_format(abs($value), $decimals, '.', ',');

        return match ($symbol) {
            'php'  => $sign . 'PHP ' . $body,
            'none' => $sign . $body,
            default => $sign . '₱' . $body,
        };
    }

    public static function parse(string $input): ?float
    {
        $cleaned = preg_replace('/[₱]|PHP|php|\s|,/u', '', $input);
        if ($cleaned === '' || $cleaned === '-' || $cleaned === null) return null;
        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    public static function toWords(float $value): string
    {
        if (!is_finite($value)) return '';

        $negative = $value < 0;
        $abs = abs($value);
        $whole = (int) floor($abs);
        $centavos = (int) round(($abs - $whole) * 100);

        $pesoLabel = $whole === 1 ? 'peso' : 'pesos';
        $centLabel = $centavos === 1 ? 'centavo' : 'centavos';

        $out = self::wholeToWords($whole) . ' ' . $pesoLabel;
        if ($centavos > 0) {
            $out .= ' and ' . self::wholeToWords($centavos) . ' ' . $centLabel;
        }
        return $negative ? 'negative ' . $out : $out;
    }

    private static function under1000(int $n): string
    {
        if ($n < 20) return self::ONES[$n];
        if ($n < 100) {
            $t = intdiv($n, 10);
            $o = $n % 10;
            return $o ? self::TENS[$t] . '-' . self::ONES[$o] : self::TENS[$t];
        }
        $h = intdiv($n, 100);
        $r = $n % 100;
        return $r ? self::ONES[$h] . ' hundred ' . self::under1000($r) : self::ONES[$h] . ' hundred';
    }

    private static function wholeToWords(int $n): string
    {
        if ($n === 0) return 'zero';
        $parts = [];
        $scales = [
            [1_000_000_000, 'billion'],
            [1_000_000, 'million'],
            [1_000, 'thousand'],
        ];
        $remainder = $n;
        foreach ($scales as [$value, $label]) {
            if ($remainder >= $value) {
                $count = intdiv($remainder, $value);
                $parts[] = self::under1000($count) . ' ' . $label;
                $remainder %= $value;
            }
        }
        if ($remainder > 0) $parts[] = self::under1000($remainder);
        return implode(' ', $parts);
    }
}
