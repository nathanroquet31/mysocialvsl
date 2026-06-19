<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Card-free trials: email reminders (7d / 1d before) and downgrade expired trials.
Schedule::command('trials:process')->dailyAt('09:00');
