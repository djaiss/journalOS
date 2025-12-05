<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;
use App\Jobs\DeleteInactiveAccounts;

Schedule::job(new DeleteInactiveAccounts(), 'low')->dailyAt('00:30');
