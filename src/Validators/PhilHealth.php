<?php

declare(strict_types=1);

namespace PhDevUtils\Validators;

final class PhilHealth
{
    public static function validate(string $input): bool
    {
        return strlen(self::digits($input)) === 12;
    }

    public static function format(string $input): ?string
    {
        $d = self::digits($input);
        if (strlen($d) !== 12) return null;
        return substr($d, 0, 2) . '-' . substr($d, 2, 9) . '-' . substr($d, 11, 1);
    }

    private static function digits(string $s): string
    {
        return preg_replace('/\D/', '', $s) ?? '';
    }
}
