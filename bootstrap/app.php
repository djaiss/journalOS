<?php

declare(strict_types=1);

use App\Http\Middleware\CheckInstanceAdministrator;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\CheckJournal;
use App\Http\Middleware\CheckJournalAPI;
use App\Http\Middleware\CheckJournalEntry;
use App\Http\Middleware\CheckJournalEntryAPI;
use App\Http\Middleware\CheckSubscription;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'set.locale' => SetLocale::class,
            'journal' => CheckJournal::class,
            'journal.api' => CheckJournalAPI::class,
            'journal.entry' => CheckJournalEntry::class,
            'journal.entry.api' => CheckJournalEntryAPI::class,
            'instance.admin' => CheckInstanceAdministrator::class,
            'subscription' => CheckSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
