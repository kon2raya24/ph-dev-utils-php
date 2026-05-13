<?php

declare(strict_types=1);

namespace PhDevUtils\Validators;

final class Sss
{
    public static function validate(string $input): bool
    {
        return strlen(self::digits($input)) === 10;
    }

    public static function format(string $input): ?string
    {
        $d = self::digits($input);
        if (strlen($d) !== 10) return null;
        return substr($d, 0, 2) . '-' . substr($d, 2, 7) . '-' . substr($d, 9, 1);
    }

    private static function digits(string $s): string
    {
        return preg_replace('/\D/', '', $s) ?? '';
    }
}
