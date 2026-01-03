<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\Docs;
use App\Http\Controllers\Marketing\MarketingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['marketing'])->group(function (): void {
    Route::get('/', [MarketingController::class, 'index'])->name('marketing.index');
    Route::get('/docs', [Docs\DocController::class, 'index'])->name('marketing.docs.index');
    Route::get('/docs/concepts/hierarchical-structure', [Docs\Concepts\HierarchicalStructureController::class, 'index'])->name('marketing.docs.concepts.hierarchical-structure');
    Route::get('/docs/concepts/permissions', [Docs\Concepts\PermissionController::class, 'index'])->name('marketing.docs.concepts.permissions');

    // api docs
    Route::get('/docs/api', [Docs\Api\ApiIntroductionController::class, 'index'])->name('marketing.docs.api.index');
    Route::get('/docs/api/authentication', [Docs\Api\AuthenticationController::class, 'index'])->name('marketing.docs.api.authentication');
    Route::get('/docs/api/profile', [Docs\Api\ProfileController::class, 'index'])->name('marketing.docs.api.account.profile');
    Route::get('/docs/api/api-management', [Docs\Api\ApiManagementController::class, 'index'])->name('marketing.docs.api.account.api-management');
    Route::get('/docs/api/logs', [Docs\Api\LogController::class, 'index'])->name('marketing.docs.api.account.logs');
    Route::get('/docs/api/emails', [Docs\Api\EmailSentController::class, 'index'])->name('marketing.docs.api.account.emails');
    Route::get('/docs/api/account', [Docs\Api\AccountController::class, 'index'])->name('marketing.docs.api.account');
    Route::get('/docs/api/journals', [Docs\Api\JournalController::class, 'index'])->name('marketing.docs.api.journals');
    Route::get('/docs/api/journal-entries', [Docs\Api\JournalEntryController::class, 'index'])->name('marketing.docs.api.journal-entries');
    Route::get('/docs/api/modules/day-type', [Docs\Api\Modules\DayTypeController::class, 'index'])->name('marketing.docs.api.modules.day-type');
    Route::get('/docs/api/modules/energy', [Docs\Api\Modules\EnergyController::class, 'index'])->name('marketing.docs.api.modules.energy');
    Route::get('/docs/api/modules/health', [Docs\Api\Modules\HealthController::class, 'index'])->name('marketing.docs.api.modules.health');
    Route::get('/docs/api/modules/kids', [Docs\Api\Modules\KidsController::class, 'index'])->name('marketing.docs.api.modules.kids');
    Route::get('/docs/api/modules/mood', [Docs\Api\Modules\MoodController::class, 'index'])->name('marketing.docs.api.modules.mood');
    Route::get('/docs/api/modules/physical-activity', [Docs\Api\Modules\PhysicalActivityController::class, 'index'])->name('marketing.docs.api.modules.physical-activity');
    Route::get('/docs/api/modules/primary-obligation', [Docs\Api\Modules\PrimaryObligationController::class, 'index'])->name('marketing.docs.api.modules.primary-obligation');
    Route::get('/docs/api/modules/sexual-activity', [Docs\Api\Modules\SexualActivityController::class, 'index'])->name('marketing.docs.api.modules.sexual-activity');
    Route::get('/docs/api/modules/sleep', [Docs\Api\Modules\SleepController::class, 'index'])->name('marketing.docs.api.modules.sleep');
    Route::get('/docs/api/modules/travel', [Docs\Api\Modules\TravelController::class, 'index'])->name('marketing.docs.api.modules.travel');
    Route::get('/docs/api/modules/work', [Docs\Api\Modules\WorkController::class, 'index'])->name('marketing.docs.api.modules.work');
});
