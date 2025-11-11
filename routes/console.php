<?php

use App\Jobs\DailyPayoutJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('daily:payout', function () {
    dispatch(new DailyPayoutJob);
})->describe('Run Daily Payout Job');