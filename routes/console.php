<?php

use App\Console\Commands\SendEmailInvoice;
use App\Console\Commands\SendEmailReceipt;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command(SendEmailInvoice::class)->everyMinute()->runInBackground();
Schedule::command(SendEmailReceipt::class)->everyMinute()->runInBackground();
