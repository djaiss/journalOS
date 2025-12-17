<?php

declare(strict_types=1);

use App\Console\Commands\CleanupGuestAccounts;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\DeleteInactiveAccounts;

Schedule::job(
    new DeleteInactiveAccounts(),
    'low',
)->dailyAt('00:30');

Schedule::job(
    new CleanupGuestAccounts(),
    'low',
)->hourly();
