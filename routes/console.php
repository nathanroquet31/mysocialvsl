<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Card-free trials: email reminders (7d / 1d before) and downgrade expired trials.
Schedule::command('trials:process')->dailyAt('09:00');

// Daily self-monitoring report to the founder's Telegram (8:00 Paris, DST-aware).
Schedule::command('monitoring:report')->timezone('Europe/Paris')->dailyAt('08:00');
