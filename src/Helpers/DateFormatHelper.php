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

        // Unix timestamp
        if (is_numeric($date) && preg_match('/^\d{9,10}$/', $date)) {
            try {
                return Carbon::createFromTimestamp((int) $date)->format($format);
            } catch (\Exception $e) {}
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
            'j M Y',
            'j F Y',
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
                if (method_exists(Carbon::class, 'hasFormat') &&
                    !Carbon::hasFormat($date, $f)) {
                    continue;
                }

                $parsed = Carbon::createFromFormat($f, $date);
                $errors = \DateTime::getLastErrors();

                if ($parsed && empty($errors['warning_count']) && empty($errors['error_count'])) {
                    return $parsed->format($format);
                }
            } catch (\Exception $e) {}
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return $returnOriginalOnFailure ? $date : '';
        }
    }
}
