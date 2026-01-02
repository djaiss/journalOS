<?php

declare(strict_types=1);

use App\Http\Controllers\App;
use App\Http\Controllers\App\Journals;
use App\Http\Controllers\App\Settings;
use App\Http\Controllers\Instance;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/marketing.php';

Route::put('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware(['throttle:30,1'])->group(function (): void {
    Route::get('demo', [App\DemoAccountController::class, 'index'])->name('demo.index');
});

Route::middleware(['auth', 'verified', 'throttle:60,1', 'set.locale'])->group(function (): void {
    // upgrade
    Route::get('upgrade', [App\UpgradeAccountController::class, 'index'])->name('upgrade.index');

    // claim
    Route::get('claim', [App\ClaimAccountController::class, 'index'])->name('claim.index');
    Route::post('claim', [App\ClaimAccountController::class, 'store'])->name('claim.store');

    Route::middleware(['subscription'])->group(function (): void {
        Route::get('journals', [Journals\JournalController::class, 'index'])->name('journal.index');
        Route::get('journals/create', [Journals\JournalController::class, 'create'])->name('journal.create');
        Route::post('journals', [Journals\JournalController::class, 'store'])->name('journal.store');

        // journal
        Route::middleware(['journal'])->group(function (): void {
            Route::get('journals/{slug}', [Journals\JournalController::class, 'show'])->name('journal.show');

            Route::middleware(['journal.entry'])->group(function (): void {
                Route::get('journals/{slug}/entries/{year}/{month}/{day}', [Journals\JournalEntryController::class, 'show'])
                    ->name('journal.entry.show');

                // sleep tracking
                Route::get('journals/{slug}/entries/{year}/{month}/{day}/sleep/{bedtime}/{wake_up_time}', [Journals\Modules\Sleep\SleepController::class, 'show'])
                    ->where(
                        [
                            'bedtime' => '([01][0-9]|2[0-3]):[0-5][0-9]',
                            'wake_up_time' => '([01][0-9]|2[0-3]):[0-5][0-9]',
                        ],
                    )
                    ->name('journal.entry.sleep.show');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/sleep/bedtime', [Journals\Modules\Sleep\SleepBedTimeController::class, 'update'])->name('journal.entry.sleep.bedtime.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/sleep/wake_up_time', [Journals\Modules\Sleep\SleepWakeUpTimeController::class, 'update'])->name('journal.entry.sleep.wake_up_time.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/sleep/reset', [Journals\Modules\Sleep\SleepResetController::class, 'update'])->name('journal.entry.sleep.reset');

                // work
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/work', [Journals\Modules\Work\WorkController::class, 'update'])->name('journal.entry.work.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/work/mode', [Journals\Modules\Work\WorkModeController::class, 'update'])->name('journal.entry.work.mode.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/work/load', [Journals\Modules\Work\WorkLoadController::class, 'update'])->name('journal.entry.work.load.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/work/procrastinated', [Journals\Modules\Work\WorkProcrastinatedController::class, 'update'])->name('journal.entry.work.procrastinated.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/work/reset', [Journals\Modules\Work\WorkResetController::class, 'update'])->name('journal.entry.work.reset');

                // travel
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/travel', [Journals\Modules\Travel\TravelController::class, 'update'])->name('journal.entry.travel.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/travel/mode', [Journals\Modules\Travel\TravelModeController::class, 'update'])->name('journal.entry.travel.mode.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/travel/reset', [Journals\Modules\Travel\TravelResetController::class, 'update'])->name('journal.entry.travel.reset');

                // day type
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/day-type', [Journals\Modules\DayType\DayTypeController::class, 'update'])->name('journal.entry.day-type.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/day-type/reset', [Journals\Modules\DayType\DayTypeResetController::class, 'update'])->name('journal.entry.day-type.reset');

                // physical activity
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/physical-activity', [Journals\Modules\PhysicalActivity\PhysicalActivityController::class, 'update'])->name('journal.entry.physical-activity.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/physical-activity/type', [Journals\Modules\PhysicalActivity\PhysicalActivityTypeController::class, 'update'])->name('journal.entry.physical-activity.type.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/physical-activity/intensity', [Journals\Modules\PhysicalActivity\PhysicalActivityIntensityController::class, 'update'])->name('journal.entry.physical-activity.intensity.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/physical-activity/reset', [Journals\Modules\PhysicalActivity\PhysicalActivityResetController::class, 'update'])->name('journal.entry.physical-activity.reset');
            });

            // settings
            Route::get('journals/{slug}/settings', [Journals\Settings\JournalSettingsController::class, 'show'])->name('journal.settings.show');
            Route::put('journals/{slug}', [Journals\JournalController::class, 'update'])->name('journal.update');
            Route::delete('journals/{slug}', [Journals\JournalController::class, 'destroy'])->name('journal.destroy');

            // settings - modules
            Route::put('journals/{slug}/settings/modules', [Journals\Settings\JournalModulesController::class, 'update'])->name('journal.settings.modules.update');
        });
    });

    // settings redirect
    Route::redirect('settings', 'settings/profile');

    // settings
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.index');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');

    // logs
    Route::get('settings/profile/logs', [Settings\LogController::class, 'index'])->name('settings.logs.index');

    // emails
    Route::get('settings/profile/emails', [Settings\EmailSentController::class, 'index'])->name('settings.emails.index');

    // security
    Route::get('settings/security', [Settings\Security\SecurityController::class, 'index'])->name('settings.security.index');
    Route::put('settings/password', [Settings\Security\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\Security\AppearanceController::class, 'edit'])->name('settings.appearance.edit');

    // 2fa
    Route::put('settings/security/2fa', [Settings\Security\PreferredTwoFAController::class, 'update'])->name('settings.security.2fa.update');
    Route::get('settings/security/2fa/create', [Settings\Security\TwoFAController::class, 'create'])->name('settings.security.2fa.create');
    Route::post('settings/security/2fa', [Settings\Security\TwoFAController::class, 'store'])->name('settings.security.2fa.store');
    Route::get('settings/security/recovery-codes', [Settings\Security\RecoveryCodeController::class, 'show'])->name('settings.security.recoverycodes.show');

    // auto delete account
    Route::put('settings/security/auto-delete-account', [Settings\Security\AutoDeleteAccountController::class, 'update'])->name('settings.security.auto-delete.update');

    // api keys
    Route::get('settings/api-keys/create', [Settings\Security\ApiKeyController::class, 'create'])->name('settings.api-keys.create');
    Route::post('settings/api-keys', [Settings\Security\ApiKeyController::class, 'store'])->name('settings.api-keys.store');
    Route::delete('settings/api-keys/{apiKey}', [Settings\Security\ApiKeyController::class, 'destroy'])->name('settings.api-keys.destroy');

    // account
    Route::get('settings/account', [Settings\AccountController::class, 'index'])->name('settings.account.index');
    Route::put('settings/prune', [Settings\PruneAccountController::class, 'update'])->name('settings.account.prune');
    Route::delete('settings/account', [Settings\AccountController::class, 'destroy'])->name('settings.account.destroy');

    Route::middleware(['instance.admin'])->group(function (): void {
        Route::get('instance', [Instance\InstanceController::class, 'index'])->name('instance.index');
        Route::get('instance/users/{user}', [Instance\InstanceController::class, 'show'])->name('instance.show');
        Route::delete('instance/users/{user}', [Instance\InstanceDestroyAccountController::class, 'destroy'])->name('instance.destroy');
        Route::put('instance/users/{user}/free', [Instance\InstanceFreeAccountController::class, 'update'])->name('instance.users.free');
    });
});

require __DIR__ . '/auth.php';
