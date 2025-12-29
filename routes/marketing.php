<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\Docs;
use App\Http\Controllers\Marketing\MarketingController;
use Illuminate\Support\Facades\Route;

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
Route::get('/docs/api/account', [Docs\Api\AccountController::class, 'index'])->name('marketing.docs.api.account');
Route::get('/docs/api/journals', [Docs\Api\JournalController::class, 'index'])->name('marketing.docs.api.journals');
