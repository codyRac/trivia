<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Friday 1pm Pacific: what has been redeemed but not done yet
Schedule::command('slack:unfulfilled-review')->fridays()->at('13:00')->timezone('America/Los_Angeles');
