<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\Journals;
use App\Http\Controllers\Api\Journals\JournalEntryController;
use App\Http\Controllers\Api\Journals\Modules\Sleep\SleepBedTimeController;
use App\Http\Controllers\Api\Journals\Modules\Sleep\SleepWakeUpTimeController;
use App\Http\Controllers\Api\Settings;
use App\Http\Controllers\Api\Settings\Account\DestroyAccountController;
use App\Http\Controllers\Api\Settings\Account\PruneAccountController;
use App\Http\Controllers\Api\Settings\Security\AutoDeleteAccountController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function (): void {
    Route::get('health', [HealthController::class, 'show'])->middleware('throttle:60,1');

    // login
    Route::post('/login', [LoginController::class, 'store']);

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
        // logout
        Route::delete('/logout', [LoginController::class, 'destroy']);

        // logged user
        Route::get('me', [Settings\Profile\ProfileController::class, 'show'])->name('me');
        Route::put('me', [Settings\Profile\ProfileController::class, 'update'])->name('me.update');

        // journals
        Route::post('journals', [Journals\JournalController::class, 'create'])->name('journal.create');
        Route::get('journals', [Journals\JournalController::class, 'index'])->name('journal.index');

        Route::middleware(['journal.api'])->group(function (): void {
            Route::get('journals/{id}', [Journals\JournalController::class, 'show'])->name('journal.show');

            Route::middleware(['journal.entry.api'])->group(function (): void {
                Route::get('journals/{id}/{year}/{month}/{day}', [JournalEntryController::class, 'show'])
                    ->whereNumber('year')
                    ->whereNumber('month')
                    ->whereNumber('day')
                    ->name('journal.entry.show');

                Route::put('journals/{id}/{year}/{month}/{day}/sleep/bedtime', [SleepBedTimeController::class, 'update'])
                    ->whereNumber('year')
                    ->whereNumber('month')
                    ->whereNumber('day')
                    ->name('journal.entry.sleep.bedtime.update');

                Route::put('journals/{id}/{year}/{month}/{day}/sleep/wake_up_time', [SleepWakeUpTimeController::class, 'update'])
                    ->whereNumber('year')
                    ->whereNumber('month')
                    ->whereNumber('day')
                    ->name('journal.entry.sleep.wake_up_time.update');
            });

            // settings
            Route::put('journals/{id}', [Journals\JournalController::class, 'update'])->name('journal.update');
            Route::delete('journals/{id}', [Journals\JournalController::class, 'destroy'])->name('journal.destroy');
        });

        // settings
        // settings -logs
        Route::get('settings/logs', [Settings\Profile\LogController::class, 'index'])->name('settings.logs');
        Route::get('settings/logs/{id}', [Settings\Profile\LogController::class, 'show'])->name('settings.logs.show');

        // settings - emails
        Route::get('settings/emails', [Settings\EmailSentController::class, 'index'])->name('settings.emails');
        Route::get('settings/emails/{id}', [Settings\EmailSentController::class, 'show'])->name('settings.emails.show');

        // settings - api keys
        Route::get('settings/api', [Settings\Security\ApiKeyController::class, 'index'])->name('settings.api');
        Route::get('settings/api/{id}', [Settings\Security\ApiKeyController::class, 'show'])->name('settings.api.show');
        Route::post('settings/api', [Settings\Security\ApiKeyController::class, 'create'])->name('settings.api.create');
        Route::delete('settings/api/{id}', [Settings\Security\ApiKeyController::class, 'destroy'])->name('settings.api.destroy');

        // settings - account
        Route::put('settings/prune', [PruneAccountController::class, 'update'])->name('settings.account.prune');
        Route::put('settings/security/auto-delete-account', [AutoDeleteAccountController::class, 'update'])->name('settings.security.auto-delete.update');
        Route::delete('settings/account', [DestroyAccountController::class, 'destroy'])->name('settings.account.destroy');
    });
});
