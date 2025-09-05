<?php

use App\Console\Commands\ProcessMlmCommission;
use App\Console\Commands\ProcessMlmRebirth;
use App\Functions\UsefulFunctions;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// Schedule::command(ProcessMlmCommission::class)->weeklyOn(4, '08:30');
// Schedule::command(ProcessMlmCommission::class)->dailyAt('13:50');

// Schedule::command(ProcessMlmRebirth::class)->dailyAt('00:00');

// Schedule::command('mlm:process-batch')
//     ->everyFiveMinutes()
//     ->thursdays()
//     ->between('0:00', '6:00')
//     ->withoutOverlapping();

// Schedule::command('mlm:reset-tracking')
//     ->thursdays()
//     ->at('6:05');

Schedule::command('mlm:process-batch')
    ->everyThreeMinutes()
    ->thursdays()
    ->between('00:00', '06:50')
    ->withoutOverlapping();

Schedule::command('mlm:reset-tracking')
    ->thursdays()
    ->at('07:05');


Schedule::command('mlm:rebirth-batch')
    ->everyFiveMinutes()
    ->days([0,1,2,3,5,6]) // All days except Thursday (4)
    ->between('00:00', '06:55')
    ->timezone('Africa/Lagos')
    ->withoutOverlapping();

Schedule::command('mlm:reset-rebirth-tracking')
    ->days([0,1,2,3,5,6])
    ->at('07:05');

Schedule::command('app:check-for-unregistered-users')
        ->everyFifteenMinutes()
        ->between('07:00', '23:59');
