<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\Journals;
use App\Http\Controllers\Api\Journals\JournalEntryController;
use App\Http\Controllers\Api\Journals\Modules\DayType\DayTypeController;
use App\Http\Controllers\Api\Journals\Modules\Energy\EnergyController as EnergyModuleController;
use App\Http\Controllers\Api\Journals\Modules\Health\HealthController as HealthModuleController;
use App\Http\Controllers\Api\Journals\Modules\Hygiene\HygieneController as HygieneModuleController;
use App\Http\Controllers\Api\Journals\Modules\Kids\KidsController as KidsModuleController;
use App\Http\Controllers\Api\Journals\Modules\Mood\MoodController as MoodModuleController;
use App\Http\Controllers\Api\Journals\Modules\PhysicalActivity\PhysicalActivityController;
use App\Http\Controllers\Api\Journals\Modules\PrimaryObligation\PrimaryObligationController as PrimaryObligationModuleController;
use App\Http\Controllers\Api\Journals\Modules\Shopping\ShoppingContextController;
use App\Http\Controllers\Api\Journals\Modules\Shopping\ShoppingController;
use App\Http\Controllers\Api\Journals\Modules\Shopping\ShoppingForController;
use App\Http\Controllers\Api\Journals\Modules\Shopping\ShoppingIntentController;
use App\Http\Controllers\Api\Journals\Modules\Shopping\ShoppingTypeController;
use App\Http\Controllers\Api\Journals\Modules\SexualActivity\SexualActivityController;
use App\Http\Controllers\Api\Journals\Modules\SexualActivity\SexualActivityTypeController;
use App\Http\Controllers\Api\Journals\Modules\SocialDensity\SocialDensityController as SocialDensityModuleController;
use App\Http\Controllers\Api\Journals\Modules\Sleep\SleepBedTimeController;
use App\Http\Controllers\Api\Journals\Modules\Sleep\SleepWakeUpTimeController;
use App\Http\Controllers\Api\Journals\Modules\Travel\TravelController;
use App\Http\Controllers\Api\Journals\Modules\Travel\TravelModeController;
use App\Http\Controllers\Api\Journals\Modules\Work\WorkController;
use App\Http\Controllers\Api\Journals\Modules\Work\WorkLoadController;
use App\Http\Controllers\Api\Journals\Modules\Work\WorkModeController;
use App\Http\Controllers\Api\Journals\Modules\Work\WorkProcrastinatedController;
use App\Http\Controllers\Api\Journals\Notes\NotesController;
use App\Http\Controllers\Api\Journals\Notes\NotesResetController;
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
                Route::prefix('journals/{id}/{year}/{month}/{day}')
                    ->whereNumber('year')
                    ->whereNumber('month')
                    ->whereNumber('day')
                    ->group(function (): void {
                        Route::get('', [JournalEntryController::class, 'show'])
                            ->name('journal.entry.show');

                        Route::put('sleep/bedtime', [SleepBedTimeController::class, 'update'])
                            ->name('journal.entry.sleep.bedtime.update');

                        Route::put('sleep/wake_up_time', [SleepWakeUpTimeController::class, 'update'])
                            ->name('journal.entry.sleep.wake_up_time.update');

                        Route::put('work', [WorkController::class, 'update'])
                            ->name('journal.entry.work.update');

                        Route::put('work/mode', [WorkModeController::class, 'update'])
                            ->name('journal.entry.work.mode.update');

                        Route::put('work/load', [WorkLoadController::class, 'update'])
                            ->name('journal.entry.work.load.update');

                        Route::put('work/procrastinated', [WorkProcrastinatedController::class, 'update'])
                            ->name('journal.entry.work.procrastinated.update');

                        Route::put('travel', [TravelController::class, 'update'])
                            ->name('journal.entry.travel.update');

                        Route::put('travel/mode', [TravelModeController::class, 'update'])
                            ->name('journal.entry.travel.mode.update');

                        Route::put('shopping', [ShoppingController::class, 'update'])
                            ->name('journal.entry.shopping.update');

                        Route::put('shopping/type', [ShoppingTypeController::class, 'update'])
                            ->name('journal.entry.shopping.type.update');

                        Route::put('shopping/intent', [ShoppingIntentController::class, 'update'])
                            ->name('journal.entry.shopping.intent.update');

                        Route::put('shopping/context', [ShoppingContextController::class, 'update'])
                            ->name('journal.entry.shopping.context.update');

                        Route::put('shopping/for', [ShoppingForController::class, 'update'])
                            ->name('journal.entry.shopping.for.update');

                        Route::put('kids', [KidsModuleController::class, 'update'])
                            ->name('journal.entry.kids.update');

                        Route::put('day-type', [DayTypeController::class, 'update'])
                            ->name('journal.entry.day-type.update');

                        Route::put('primary-obligation', [PrimaryObligationModuleController::class, 'update'])
                            ->name('journal.entry.primary-obligation.update');

                        Route::put('physical-activity', [PhysicalActivityController::class, 'update'])
                            ->name('journal.entry.physical-activity.update');

                        Route::put('sexual-activity', [SexualActivityController::class, 'update'])
                            ->name('journal.entry.sexual-activity.update');

                        Route::put('sexual-activity/type', [SexualActivityTypeController::class, 'update'])
                            ->name('journal.entry.sexual-activity.type.update');

                        Route::put('health', [HealthModuleController::class, 'update'])
                            ->name('journal.entry.health.update');

                        Route::put('hygiene', [HygieneModuleController::class, 'update'])
                            ->name('journal.entry.hygiene.update');

                        Route::put('mood', [MoodModuleController::class, 'update'])
                            ->name('journal.entry.mood.update');

                        Route::put('energy', [EnergyModuleController::class, 'update'])
                            ->name('journal.entry.energy.update');

                        Route::put('social-density', [SocialDensityModuleController::class, 'update'])
                            ->name('journal.entry.social-density.update');

                        Route::put('notes', [NotesController::class, 'update'])
                            ->name('journal.entry.notes.update');

                        Route::put('notes/reset', [NotesResetController::class, 'update'])
                            ->name('journal.entry.notes.reset');
                    });
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
