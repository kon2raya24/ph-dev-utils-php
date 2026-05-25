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

    private const TL_UNITS = ['sero', 'isa', 'dalawa', 'tatlo', 'apat', 'lima', 'anim', 'pito', 'walo', 'siyam'];
    private const TL_TEENS = [
        'sampu', 'labing-isa', 'labindalawa', 'labintatlo', 'labing-apat',
        'labinlima', 'labing-anim', 'labimpito', 'labingwalo', 'labinsiyam',
    ];
    private const TL_TENS = ['', '', 'dalawampu', 'tatlumpu', 'apatnapu', 'limampu', 'animnapu', 'pitumpu', 'walumpu', 'siyamnapu'];

    public static function toWordsFilipino(float $value): string
    {
        if (!is_finite($value)) return '';
        if ($value < 0) throw new \OutOfRangeException('toWordsFilipino: negative amounts not supported');
        if ($value >= 1_000_000_000_000_000) {
            throw new \OutOfRangeException('toWordsFilipino: amount too large (max ~999 trilyon)');
        }

        $pesos = (int) floor($value);
        $centavos = (int) round(($value - $pesos) * 100);
        if ($centavos === 100) {
            $pesos += 1;
            $centavos = 0;
        }

        if ($pesos === 0 && $centavos === 0) return 'Sero piso';

        $wholeWords = self::tlIntegerToWords($pesos);
        if ($centavos > 0) {
            $cents = str_pad((string) $centavos, 2, '0', STR_PAD_LEFT);
            $result = $wholeWords . ' at ' . $cents . '/100 piso';
        } else {
            $result = self::tlAppendUnit($wholeWords, 'piso');
        }
        return mb_strtoupper(mb_substr($result, 0, 1)) . mb_substr($result, 1);
    }

    private static function tlEndsInVowelOrN(string $s): bool
    {
        return (bool) preg_match('/([aeiouAEIOU]|n|ng)$/', $s);
    }

    private static function tlAppendUnit(string $words, string $unit): string
    {
        $parts = explode(' ', $words);
        $i = count($parts) - 1;
        $last = $parts[$i];
        if (preg_match('/[aeiouAEIOU]$/', $last)) {
            $parts[$i] = $last . 'ng';
            $parts[] = $unit;
        } elseif (str_ends_with($last, 'ng')) {
            $parts[] = $unit;
        } elseif (str_ends_with($last, 'n')) {
            $parts[$i] = $last . 'g';
            $parts[] = $unit;
        } else {
            $parts[] = 'na';
            $parts[] = $unit;
        }
        return implode(' ', $parts);
    }

    private static function tlUnder100(int $n): string
    {
        if ($n === 0) return '';
        if ($n < 10) return self::TL_UNITS[$n];
        if ($n < 20) return self::TL_TEENS[$n - 10];
        $t = intdiv($n, 10);
        $o = $n % 10;
        return $o === 0 ? self::TL_TENS[$t] : self::TL_TENS[$t] . "'t " . self::TL_UNITS[$o];
    }

    private static function tlUnder1000(int $n): string
    {
        if ($n === 0) return '';
        if ($n < 100) return self::tlUnder100($n);
        $h = intdiv($n, 100);
        $rest = $n % 100;
        if ($h === 1) {
            $hundredsWord = 'isang daan';
        } elseif (self::tlEndsInVowelOrN(self::TL_UNITS[$h])) {
            $hundredsWord = self::TL_UNITS[$h] . 'ng daan';
        } else {
            $hundredsWord = self::TL_UNITS[$h] . ' na raan';
        }
        return $rest === 0 ? $hundredsWord : $hundredsWord . ' ' . self::tlUnder100($rest);
    }

    private static function tlIntegerToWords(int $n): string
    {
        if ($n === 0) return 'sero';
        $scales = [
            [1_000_000_000_000, 'trilyon'],
            [1_000_000_000, 'bilyon'],
            [1_000_000, 'milyon'],
            [1_000, 'libo'],
        ];
        $remainder = $n;
        $parts = [];
        foreach ($scales as [$value, $unit]) {
            if ($remainder >= $value) {
                $count = intdiv($remainder, $value);
                $parts[] = self::tlAppendUnit(self::tlUnder1000($count), $unit);
                $remainder %= $value;
            }
        }
        if ($remainder > 0) $parts[] = self::tlUnder1000($remainder);
        return implode(' ', $parts);
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
