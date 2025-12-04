<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\ConfirmationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\SendMagicLinkController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'index'])->name('marketing.index');
Route::get('/docs', [Docs\DocController::class, 'index'])->name('marketing.docs.index');
Route::get('/docs/concepts/hierarchical-structure', [Docs\Concepts\HierarchicalStructureController::class, 'index'])->name('marketing.docs.concepts.hierarchical-structure');
Route::get('/docs/concepts/permissions', [Docs\Concepts\PermissionsController::class, 'index'])->name('marketing.docs.concepts.permissions');

// api docs
Route::get('/docs/api', [Docs\Api\ApiIntroductionController::class, 'index'])->name('marketing.docs.api.index');
Route::get('/docs/api/authentication', [Docs\Api\AuthenticationController::class, 'index'])->name('marketing.docs.api.authentication');
Route::get('/docs/api/profile', [Docs\Api\ProfileController::class, 'index'])->name('marketing.docs.api.account.profile');
Route::get('/docs/api/api-management', [Docs\Api\ApiManagementController::class, 'index'])->name('marketing.docs.api.account.api-management');
Route::get('/docs/api/logs', [Docs\Api\LogsController::class, 'index'])->name('marketing.docs.api.account.logs');
Route::get('/docs/api/organizations', [Docs\Api\OrganizationsController::class, 'index'])->name('marketing.docs.api.organizations');
