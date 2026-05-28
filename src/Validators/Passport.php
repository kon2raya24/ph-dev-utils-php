<?php

declare(strict_types=1);

namespace PhDevUtils\Validators;

// Philippine passport number, format-level only (no checksum). Two current 9-char patterns:
//   - ePassport (Aug 15, 2016 onward): 1 letter + 7 digits + 1 letter, e.g. P1234567A
//   - Machine-readable (2005–2016):    2 letters + 7 digits,          e.g. XX1234567
final class Passport
{
    private const EPASSPORT = '/^[A-Z]\d{7}[A-Z]$/';
    private const MRP = '/^[A-Z]{2}\d{7}$/';

    public static function validate(string $input): bool
    {
        $n = self::normalize($input);
        return preg_match(self::EPASSPORT, $n) === 1 || preg_match(self::MRP, $n) === 1;
    }

    public static function format(string $input): ?string
    {
        $n = self::normalize($input);
        return self::validate($n) ? $n : null;
    }

    private static function normalize(string $s): string
    {
        return strtoupper(preg_replace('/[\s-]/', '', $s) ?? '');
    }
}
