<?php

declare(strict_types=1);

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Organizations;
use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;

Route::put('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware(['auth', 'verified', 'throttle:60,1', 'set.locale'])->group(function (): void {
    Route::get('organizations', [Organizations\OrganizationController::class, 'index'])->name('organization.index');
    Route::get('organizations/create', [Organizations\OrganizationController::class, 'create'])->name('organization.create');
    Route::post('organizations', [Organizations\OrganizationController::class, 'store'])->name('organization.store');

    // organization
    Route::middleware(['organization'])->group(function (): void {
        Route::get('organizations/{slug}', [Organizations\OrganizationController::class, 'show'])->name('organization.show');
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

    // api keys
    Route::get('settings/api-keys/create', [Settings\Security\ApiKeyController::class, 'create'])->name('settings.api-keys.create');
    Route::post('settings/api-keys', [Settings\Security\ApiKeyController::class, 'store'])->name('settings.api-keys.store');
    Route::delete('settings/api-keys/{apiKey}', [Settings\Security\ApiKeyController::class, 'destroy'])->name('settings.api-keys.destroy');

    // account
    Route::get('settings/account', [Settings\AccountController::class, 'index'])->name('settings.account.index');
    Route::delete('settings/account', [Settings\AccountController::class, 'destroy'])->name('settings.account.destroy');
});

require __DIR__ . '/auth.php';
