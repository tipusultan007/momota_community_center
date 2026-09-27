<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('remind:payments')->dailyAt('10:00');
Schedule::command('app:send-event-greetings')->dailyAt('11:00');
Schedule::command('check:subscriptions')->dailyAt('09:00');
