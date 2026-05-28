<?php

declare(strict_types=1);

namespace PhDevUtils\Validators;

// PhilSys National ID — the 16-digit PhilSys Card Number (PCN) printed on the PhilID.
// Validates the PCN, NOT the 12-digit PhilSys Number (PSN), which is never disclosed.
// Format-level only (no checksum).
final class NationalId
{
    public static function validate(string $input): bool
    {
        return strlen(self::digits($input)) === 16;
    }

    public static function format(string $input): ?string
    {
        $d = self::digits($input);
        if (strlen($d) !== 16) return null;
        return substr($d, 0, 4) . '-' . substr($d, 4, 4) . '-' . substr($d, 8, 4) . '-' . substr($d, 12, 4);
    }

    private static function digits(string $s): string
    {
        return preg_replace('/\D/', '', $s) ?? '';
    }
}
