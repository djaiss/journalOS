<?php

declare(strict_types=1);

use App\Http\Controllers\App;
use App\Http\Controllers\App\Journals;
use App\Http\Controllers\App\Settings;
use App\Http\Controllers\Instance;
use App\Http\Controllers\Llm;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/marketing.php';

Route::put('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware(['throttle:30,1', 'journal.llm'])->group(function (): void {
    Route::get('llm/{accessKey}/{year}/{month}/{day}', [Llm\JournalEntryController::class, 'show'])
        ->name('llm.journal.entry.show');
    Route::get('llm/{accessKey}/{year}/{month}', [Llm\JournalEntryMonthController::class, 'index'])
        ->name('llm.journal.month.show');
    Route::get('llm/{accessKey}/{year}', [Llm\JournalEntryYearController::class, 'index'])
        ->name('llm.journal.year.show');
});

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

                // notes
                Route::get('journals/{slug}/entries/{year}/{month}/{day}/notes/edit', [Journals\Notes\NotesController::class, 'edit'])->name('journal.entry.notes.edit');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/notes', [Journals\Notes\NotesController::class, 'update'])->name('journal.entry.notes.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/notes/reset', [Journals\Notes\NotesResetController::class, 'update'])->name('journal.entry.notes.reset');

                // sleep tracking
                Route::get('journals/{slug}/entries/{year}/{month}/{day}/sleep/{bedtime}/{wake_up_time}', [Journals\Modules\Sleep\SleepController::class, 'show'])
                    ->where(
                        [
                            'bedtime' => '([01][0-9]|2[0-3]):[0-5][0-9]',
                            'wake_up_time' => '([01][0-9]|2[0-3]):[0-5][0-9]',
                        ],
                    )
                    ->name('journal.entry.sleep.show');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/sleep', [Journals\Modules\Sleep\SleepController::class, 'update'])->name('journal.entry.sleep.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/sleep/reset', [Journals\Modules\Sleep\SleepResetController::class, 'update'])->name('journal.entry.sleep.reset');

                // work
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/work', [Journals\Modules\Work\WorkController::class, 'update'])->name('journal.entry.work.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/work/reset', [Journals\Modules\Work\WorkResetController::class, 'update'])->name('journal.entry.work.reset');

                // travel
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/travel', [Journals\Modules\Travel\TravelController::class, 'update'])->name('journal.entry.travel.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/travel/reset', [Journals\Modules\Travel\TravelResetController::class, 'update'])->name('journal.entry.travel.reset');

                // weather
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/weather', [Journals\Modules\Weather\WeatherController::class, 'update'])->name('journal.entry.weather.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/weather/reset', [Journals\Modules\Weather\WeatherResetController::class, 'update'])->name('journal.entry.weather.reset');

                // weather influence
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/weather-influence', [Journals\Modules\WeatherInfluence\WeatherInfluenceController::class, 'update'])->name('journal.entry.weather-influence.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/weather-influence/reset', [Journals\Modules\WeatherInfluence\WeatherInfluenceResetController::class, 'update'])->name('journal.entry.weather-influence.reset');

                // shopping
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/shopping', [Journals\Modules\Shopping\ShoppingController::class, 'update'])->name('journal.entry.shopping.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/shopping/reset', [Journals\Modules\Shopping\ShoppingResetController::class, 'update'])->name('journal.entry.shopping.reset');

                // meals
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/meals', [Journals\Modules\Meals\MealsController::class, 'update'])->name('journal.entry.meals.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/meals/reset', [Journals\Modules\Meals\MealsResetController::class, 'update'])->name('journal.entry.meals.reset');

                // kids
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/kids', [Journals\Modules\Kids\KidsController::class, 'update'])->name('journal.entry.kids.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/kids/reset', [Journals\Modules\Kids\KidsResetController::class, 'update'])->name('journal.entry.kids.reset');

                // day type
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/day-type', [Journals\Modules\DayType\DayTypeController::class, 'update'])->name('journal.entry.day-type.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/day-type/reset', [Journals\Modules\DayType\DayTypeResetController::class, 'update'])->name('journal.entry.day-type.reset');

                // primary obligation
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/primary-obligation', [Journals\Modules\PrimaryObligation\PrimaryObligationController::class, 'update'])->name('journal.entry.primary-obligation.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/primary-obligation/reset', [Journals\Modules\PrimaryObligation\PrimaryObligationResetController::class, 'update'])->name('journal.entry.primary-obligation.reset');

                // physical activity
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/physical-activity', [Journals\Modules\PhysicalActivity\PhysicalActivityController::class, 'update'])->name('journal.entry.physical-activity.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/physical-activity/reset', [Journals\Modules\PhysicalActivity\PhysicalActivityResetController::class, 'update'])->name('journal.entry.physical-activity.reset');

                // sexual activity
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/sexual-activity', [Journals\Modules\SexualActivity\SexualActivityController::class, 'update'])->name('journal.entry.sexual-activity.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/sexual-activity/reset', [Journals\Modules\SexualActivity\SexualActivityResetController::class, 'update'])->name('journal.entry.sexual-activity.reset');

                // health
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/health', [Journals\Modules\Health\HealthController::class, 'update'])->name('journal.entry.health.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/health/reset', [Journals\Modules\Health\HealthResetController::class, 'update'])->name('journal.entry.health.reset');

                // hygiene
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/hygiene', [Journals\Modules\Hygiene\HygieneController::class, 'update'])->name('journal.entry.hygiene.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/hygiene/reset', [Journals\Modules\Hygiene\HygieneResetController::class, 'update'])->name('journal.entry.hygiene.reset');

                // mood
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/mood', [Journals\Modules\Mood\MoodController::class, 'update'])->name('journal.entry.mood.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/mood/reset', [Journals\Modules\Mood\MoodResetController::class, 'update'])->name('journal.entry.mood.reset');

                // reading
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/reading', [Journals\Modules\Reading\ReadingController::class, 'update'])->name('journal.entry.reading.update');
                Route::post('journals/{slug}/entries/{year}/{month}/{day}/reading/books', [Journals\Modules\Reading\ReadingBookController::class, 'store'])->name('journal.entry.reading.books.store');
                Route::delete('journals/{slug}/entries/{year}/{month}/{day}/reading/books/{book}', [Journals\Modules\Reading\ReadingBookController::class, 'destroy'])->name('journal.entry.reading.books.destroy');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/reading/reset', [Journals\Modules\Reading\ReadingResetController::class, 'update'])->name('journal.entry.reading.reset');

                // energy
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/energy', [Journals\Modules\Energy\EnergyController::class, 'update'])->name('journal.entry.energy.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/energy/reset', [Journals\Modules\Energy\EnergyResetController::class, 'update'])->name('journal.entry.energy.reset');

                // cognitive load
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/cognitive-load', [Journals\Modules\CognitiveLoad\CognitiveLoadController::class, 'update'])->name('journal.entry.cognitive-load.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/cognitive-load/reset', [Journals\Modules\CognitiveLoad\CognitiveLoadResetController::class, 'update'])->name('journal.entry.cognitive-load.reset');

                // social density
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/social-density', [Journals\Modules\SocialDensity\SocialDensityController::class, 'update'])->name('journal.entry.social-density.update');
                Route::put('journals/{slug}/entries/{year}/{month}/{day}/social-density/reset', [Journals\Modules\SocialDensity\SocialDensityResetController::class, 'update'])->name('journal.entry.social-density.reset');
            });

            // settings
            Route::get('journals/{slug}/settings/modules', [Journals\Settings\JournalModulesSettingsController::class, 'show'])->name('journal.settings.modules.index');
            Route::get('journals/{slug}/settings/management', [Journals\Settings\JournalManagementSettingsController::class, 'show'])->name('journal.settings.management.index');
            Route::put('journals/{slug}', [Journals\JournalController::class, 'update'])->name('journal.update');
            Route::delete('journals/{slug}', [Journals\JournalController::class, 'destroy'])->name('journal.destroy');

            // settings - modules and layouts
            Route::get('journals/{slug}/settings/layouts/create', [Journals\Settings\JournalLayoutsController::class, 'create'])->name('journal.settings.layouts.create');
            Route::post('journals/{slug}/settings/layouts', [Journals\Settings\JournalLayoutsController::class, 'store'])->name('journal.settings.layouts.store');
            Route::delete('journals/{slug}/settings/layouts/{layout}', [Journals\Settings\JournalLayoutsController::class, 'destroy'])->name('journal.settings.layouts.destroy');
            Route::put('journals/{slug}/settings/layouts/{layout}', [Journals\Settings\JournalLayoutsController::class, 'update'])->name('journal.settings.layouts.update');
            Route::put('journals/{slug}/settings/layouts/{layout}/default', [Journals\Settings\JournalLayoutDefaultController::class, 'update'])->name('journal.settings.layouts.default');
            Route::get('journals/{slug}/settings/layouts/{layout}/modules', [Journals\Settings\JournalLayoutModulesController::class, 'show'])->name('journal.settings.layouts.modules.index');
            Route::post('journals/{slug}/settings/layouts/{layout}/modules', [Journals\Settings\JournalLayoutModulesController::class, 'store'])->name('journal.settings.layouts.modules.store');
            Route::delete('journals/{slug}/settings/layouts/{layout}/modules/{moduleKey}', [Journals\Settings\JournalLayoutModulesController::class, 'destroy'])->name('journal.settings.layouts.modules.destroy');
            Route::put('journals/{slug}/settings/layouts/{layout}/modules/reorder', [Journals\Settings\JournalLayoutModulesController::class, 'reorder'])->name('journal.settings.layouts.modules.reorder');

            // settings - edit past
            Route::put('journals/{slug}/settings/edit-past', [Journals\Settings\JournalPastEditingController::class, 'update'])->name('journal.settings.edit-past.update');

            // llm
            Route::get('journals/{slug}/settings/llm', [Journals\Settings\JournalLLMSettingsController::class, 'show'])->name('journal.settings.llm.index');
            Route::put('journals/{slug}/settings/llm', [Journals\Settings\JournalLLMSettingsController::class, 'update'])->name('journal.settings.llm.update');
            Route::get('journals/{slug}/settings/llm/logs', [Journals\Settings\JournalLlmAccessLogController::class, 'index'])
                ->name('journal.settings.llm.logs.index');
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
