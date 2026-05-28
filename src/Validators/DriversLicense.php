<?php

declare(strict_types=1);

namespace PhDevUtils\Validators;

// LTO driver's license number: 1 letter + 10 digits, displayed as X##-##-######.
// Format-level only (no checksum). e.g. "N02-12-345678".
final class DriversLicense
{
    public static function validate(string $input): bool
    {
        return preg_match('/^[A-Z]\d{10}$/', self::normalize($input)) === 1;
    }

    public static function format(string $input): ?string
    {
        $s = self::normalize($input);
        if (preg_match('/^[A-Z]\d{10}$/', $s) !== 1) {
            return null;
        }
        return substr($s, 0, 3) . '-' . substr($s, 3, 2) . '-' . substr($s, 5, 6);
    }

    private static function normalize(string $s): string
    {
        return strtoupper(preg_replace('/[\s-]/', '', $s) ?? '');
    }
}
