<?php

namespace Wakar\DateFormatHelper;

use Illuminate\Support\ServiceProvider;

class DateFormatServiceProvider extends ServiceProvider
{
    public function register()
    {
        require_once __DIR__.'/helpers.php';
    }
}
