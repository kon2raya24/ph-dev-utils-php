<?php

declare(strict_types=1);

namespace PhDevUtils\Validators;

final class PagIbig
{
    public static function validate(string $input): bool
    {
        return strlen(self::digits($input)) === 12;
    }

    public static function format(string $input): ?string
    {
        $d = self::digits($input);
        if (strlen($d) !== 12) return null;
        return substr($d, 0, 4) . '-' . substr($d, 4, 4) . '-' . substr($d, 8, 4);
    }

    private static function digits(string $s): string
    {
        return preg_replace('/\D/', '', $s) ?? '';
    }
}
