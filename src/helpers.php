<?php

use Wakar\DateFormatHelper\Helpers\DateFormatHelper;

if (!function_exists('format_date')) {
    function format_date($date, $format = 'd-m-Y', $returnOriginalOnFailure = true)
    {
        return DateFormatHelper::format_date($date, $format, $returnOriginalOnFailure);
    }
}
