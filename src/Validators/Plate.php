<?php

declare(strict_types=1);

namespace PhDevUtils\Validators;

// LTO motor-vehicle plate numbers, format-level only. Standard private 4-wheel and
// motorcycle series (not specialty / government / diplomatic / temporary):
//   - 4-wheel:    3 letters + 3-4 digits   ("ABC 1234", older "ABC 123")
//   - motorcycle: 2 letters + 4-5 digits   ("AB 12345")
//                 or 1 letter + 3 digits + 2 letters  (2023+ series, "A 123 BC")
final class Plate
{
    private const CAR = '/^[A-Z]{3}\d{3,4}$/';
    private const MC_OLD = '/^[A-Z]{2}\d{4,5}$/';
    private const MC_NEW = '/^[A-Z]\d{3}[A-Z]{2}$/';

    /**
     * @return array{plate:string, type:string}|null  type is "car" or "motorcycle"
     */
    public static function parse(string $input): ?array
    {
        $s = self::normalize($input);
        if (preg_match(self::CAR, $s) === 1) {
            return ['plate' => $s, 'type' => 'car'];
        }
        if (preg_match(self::MC_OLD, $s) === 1 || preg_match(self::MC_NEW, $s) === 1) {
            return ['plate' => $s, 'type' => 'motorcycle'];
        }
        return null;
    }

    public static function validate(string $input): bool
    {
        return self::parse($input) !== null;
    }

    private static function normalize(string $s): string
    {
        return strtoupper(preg_replace('/[\s-]/', '', $s) ?? '');
    }
}
