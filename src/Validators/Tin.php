<?php

declare(strict_types=1);

namespace PhDevUtils\Validators;

final class Tin
{
    public static function validate(string $input): bool
    {
        $d = self::digits($input);
        return strlen($d) === 9 || strlen($d) === 12;
    }

    public static function format(string $input): ?string
    {
        $d = self::digits($input);
        if (strlen($d) === 9)  return substr($d, 0, 3) . '-' . substr($d, 3, 3) . '-' . substr($d, 6, 3);
        if (strlen($d) === 12) return substr($d, 0, 3) . '-' . substr($d, 3, 3) . '-' . substr($d, 6, 3) . '-' . substr($d, 9, 3);
        return null;
    }

    private static function digits(string $s): string
    {
        return preg_replace('/\D/', '', $s) ?? '';
    }
}
