<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\Settings;
use App\Http\Controllers\Api\Organizations;
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

        // organizations
        Route::post('organizations', [Organizations\OrganizationController::class, 'create'])->name('organization.create');
        Route::get('organizations', [Organizations\OrganizationController::class, 'index'])->name('organization.index');

        Route::middleware(['organization.api'])->group(function (): void {
            Route::get('organizations/{id}', [Organizations\OrganizationController::class, 'show'])->name('organization.show');

            // settings
            // settings - job families
            Route::get('organizations/{id}/settings/job-families', [Organizations\Settings\JobFamilyController::class, 'index'])->name('organizations.settings.job-families');
            Route::post('organizations/{id}/settings/job-families', [Organizations\Settings\JobFamilyController::class, 'create'])->name('organizations.settings.job-families.create');
            Route::get('organizations/{id}/settings/job-families/{job_family_id}', [Organizations\Settings\JobFamilyController::class, 'show'])->name('organizations.settings.job-families.show');
            Route::put('organizations/{id}/settings/job-families/{job_family_id}', [Organizations\Settings\JobFamilyController::class, 'update'])->name('organizations.settings.job-families.update');
            Route::delete('organizations/{id}/settings/job-families/{job_family_id}', [Organizations\Settings\JobFamilyController::class, 'destroy'])->name('organizations.settings.job-families.destroy');

            // settings - job disciplines
            Route::get('organizations/{id}/settings/job-families/{job_family_id}/job-disciplines', [Organizations\Settings\JobDisciplineController::class, 'index'])->name('organizations.settings.job-disciplines');
            Route::post('organizations/{id}/settings/job-families/{job_family_id}/job-disciplines', [Organizations\Settings\JobDisciplineController::class, 'create'])->name('organizations.settings.job-disciplines.create');
            Route::get('organizations/{id}/settings/job-families/{job_family_id}/job-disciplines/{job_discipline_id}', [Organizations\Settings\JobDisciplineController::class, 'show'])->name('organizations.settings.job-disciplines.show');
            Route::put('organizations/{id}/settings/job-families/{job_family_id}/job-disciplines/{job_discipline_id}', [Organizations\Settings\JobDisciplineController::class, 'update'])->name('organizations.settings.job-disciplines.update');
            Route::delete('organizations/{id}/settings/job-families/{job_family_id}/job-disciplines/{job_discipline_id}', [Organizations\Settings\JobDisciplineController::class, 'destroy'])->name('organizations.settings.job-disciplines.destroy');

            // settings - job levels
            Route::get('organizations/{id}/settings/job-families/{job_family_id}/job-disciplines/{job_discipline_id}/job-levels', [Organizations\Settings\JobLevelController::class, 'index'])->name('organizations.settings.job-levels');
            Route::post('organizations/{id}/settings/job-families/{job_family_id}/job-disciplines/{job_discipline_id}/job-levels', [Organizations\Settings\JobLevelController::class, 'create'])->name('organizations.settings.job-levels.create');
            Route::get('organizations/{id}/settings/job-families/{job_family_id}/job-disciplines/{job_discipline_id}/job-levels/{job_level_id}', [Organizations\Settings\JobLevelController::class, 'show'])->name('organizations.settings.job-levels.show');
            Route::put('organizations/{id}/settings/job-families/{job_family_id}/job-disciplines/{job_discipline_id}/job-levels/{job_level_id}', [Organizations\Settings\JobLevelController::class, 'update'])->name('organizations.settings.job-levels.update');
            Route::delete('organizations/{id}/settings/job-families/{job_family_id}/job-disciplines/{job_discipline_id}/job-levels/{job_level_id}', [Organizations\Settings\JobLevelController::class, 'destroy'])->name('organizations.settings.job-levels.destroy');
        });

        // settings
        // settings -logs
        Route::get('settings/logs', [Settings\Profile\LogController::class, 'index'])->name('settings.logs');
        Route::get('settings/logs/{id}', [Settings\Profile\LogController::class, 'show'])->name('settings.logs.show');

        // settings - api keys
        Route::get('settings/api', [Settings\Security\ApiKeyController::class, 'index'])->name('settings.api');
        Route::get('settings/api/{id}', [Settings\Security\ApiKeyController::class, 'show'])->name('settings.api.show');
        Route::post('settings/api', [Settings\Security\ApiKeyController::class, 'create'])->name('settings.api.create');
        Route::delete('settings/api/{id}', [Settings\Security\ApiKeyController::class, 'destroy'])->name('settings.api.destroy');
    });
});
