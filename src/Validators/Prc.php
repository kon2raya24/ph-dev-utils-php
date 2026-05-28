<?php

declare(strict_types=1);

namespace PhDevUtils\Validators;

// PRC (Professional Regulation Commission) license / registration number: 7 digits.
// Format-level only (no checksum).
final class Prc
{
    public static function validate(string $input): bool
    {
        return strlen(self::digits($input)) === 7;
    }

    public static function format(string $input): ?string
    {
        $d = self::digits($input);
        return strlen($d) === 7 ? $d : null;
    }

    private static function digits(string $s): string
    {
        return preg_replace('/\D/', '', $s) ?? '';
    }
}
