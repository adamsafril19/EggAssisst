<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate daily summary at 20:00 WIB
Schedule::command('summary:generate')
    ->dailyAt('20:00')
    ->timezone('Asia/Jakarta')
    ->description('Generate daily stock summary');
