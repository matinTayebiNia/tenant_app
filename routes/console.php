<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('telescope:prune --hours=1440')->dailyAt('03:30');

// Schedule::command('backup:clean')->daily()->at('01:00');
// Schedule::command('backup:run --disable-notifications')->daily()->at('01:30');
