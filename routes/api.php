<?php

use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\MeController;
use App\Http\Middleware\CheckJournal;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [MeController::class, 'show'])->name('me');

    // manage journals
    Route::get('journals', [JournalController::class, 'index']);
    Route::post('journals', [JournalController::class, 'create']);

    Route::middleware(CheckJournal::class)->prefix('journals/{journal}')->group(function () {
        Route::put('', [JournalController::class, 'update']);
        Route::delete('', [JournalController::class, 'destroy']);
    });
});
