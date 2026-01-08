<?php

namespace Wakar\DateFormatHelper\Helpers;

use Carbon\Carbon;

class DateFormatHelper
{
    public static function format_date($date, $format = 'd-m-Y', $returnOriginalOnFailure = true)
    {
        $date = trim((string) $date);
        if ($date === '' || $date === 'null') {
            return '';
        }
        if (empty($format)) {
            $format = 'd-m-Y';
        }

        // === IMPROVED: Remove ordinal suffixes (1st, 2nd, 3rd, 4th, 21st, 30th, etc.) ===
        // Handles: "1st", "2nd", "3rd", "4th", "21st", "22nd", "23rd", "24th", etc.
        // Works with or without space: "1st Jan" or "1 st Jan"
        // Case insensitive
        $date = preg_replace('/(\d)(st|nd|rd|th)\b/i', '$1', $date);

        // Optional: extra trim in case multiple spaces appear after removal
        $date = preg_replace('/\s+/', ' ', $date);
        $date = trim($date);

        // Unix timestamp
        if (is_numeric($date) && preg_match('/^\d{9,10}$/', $date)) {
            try {
                return Carbon::createFromTimestamp((int) $date)->format($format);
            } catch (\Exception $e) {
            }
        }

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d\TH:i:s',
            'Y-m-d\TH:i:sP',
            'Y-m-d\TH:i:s.u',
            'd/m/Y H:i:s',
            'd-m-Y H:i:s',
            'm/d/Y H:i:s',
            'Y-m-d',
            'Y/m/d',
            'd/m/Y',
            'd-m-Y',
            'm/d/Y',
            'd.m.Y',
            'Y.m.d',
            'j M Y',       // Now works perfectly after cleaning: "2 march 2025"
            'j F Y',       // "2 March 2025"
            'M j, Y',
            'F j, Y',
            'D, j M Y',
            'Ymd',
            'dmY',
            'mdY',
            'm/d/Y h:i:s A',
            'd/m/Y h:i:s A',
            'U',
        ];

        foreach ($formats as $f) {
            try {
                if (
                    method_exists(Carbon::class, 'hasFormat') &&
                    !Carbon::hasFormat($date, $f)
                ) {
                    continue;
                }
                $parsed = Carbon::createFromFormat($f, $date);
                $errors = \DateTime::getLastErrors();
                if ($parsed && empty($errors['warning_count']) && empty($errors['error_count'])) {
                    return $parsed->format($format);
                }
            } catch (\Exception $e) {
            }
        }

        // Final fallback - Carbon::parse is very forgiving
        try {
            return Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return $returnOriginalOnFailure ? $date : '';
        }
    }
}
