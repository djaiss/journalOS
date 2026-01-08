<?php

declare(strict_types=1);

namespace App\Helpers;

use Stevebauman\Purify\Facades\Purify;

/**
 * Provides utility methods for cleaning user input.
 *
 * This class removes potentially dangerous HTML/PHP tags from user-submitted text
 * to prevent XSS (cross-site scripting) attacks.
 *
 * Important: This is for INPUT sanitization. For OUTPUT, Laravel's Blade {{ }}
 * syntax already escapes HTML, so you don't need to sanitize again when displaying.
 */
final class TextSanitizer
{
    /**
     * Sanitize a string by removing all HTML/PHP tags and trimming whitespace.
     *
     * This method takes user input like "<script>alert('xss')</script>Hello"
     * and returns clean text: "alert('xss')Hello". It strips out anything
     * between angle brackets and removes leading/trailing spaces.
     *
     * Use this when you need to ensure the input is plain text only.
     *
     * @param string $value The user-submitted text to sanitize
     * @return string The sanitized text with tags removed and whitespace trimmed
     */
    public static function plainText(string $value): string
    {
        return mb_trim(strip_tags($value));
    }

    /**
     * Sanitize a nullable string, converting empty results to null.
     *
     * This works like plainText() but handles nullable values gracefully.
     * If the input is null, it returns null. If sanitization results in an
     * empty string (e.g., input was "   " or "<p></p>"), it also returns null
     * instead of an empty string.
     *
     * Use this for optional text fields in your API where you want to store
     * NULL in the database instead of empty strings.
     *
     * @param string|null $value The user-submitted text to sanitize, or null
     * @return string|null The sanitized text, or null if input was null/empty
     */
    public static function nullablePlainText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $sanitized = self::plainText($value);

        return $sanitized === '' ? null : $sanitized;
    }

    /**
     * Sanitize HTML content by removing dangerous tags and attributes.
     *
     * This method uses HTMLPurifier to clean HTML input while preserving
     * safe formatting tags like paragraphs, bold, italic, lists, etc.
     * It removes scripts, iframes, and other potentially dangerous elements.
     *
     * Use this when you need to accept rich text (HTML) from users but want
     * to ensure it's safe from XSS attacks.
     *
     * @param string $value The HTML content to sanitize
     * @return string The sanitized HTML with dangerous elements removed
     */
    public static function html(string $value): string
    {
        return Purify::clean($value);
    }
}
