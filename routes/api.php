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
