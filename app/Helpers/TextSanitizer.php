<?php

declare(strict_types=1);

namespace App\Helpers;

final class TextSanitizer
{
    public static function plainText(string $value): string
    {
        return trim(strip_tags($value));
    }

    public static function nullablePlainText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $sanitized = self::plainText($value);

        return $sanitized === '' ? null : $sanitized;
    }
}
