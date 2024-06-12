<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Profile\ApiAccessController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

// Routes in this project should follow the Rails routing structure
// (https://guides.rubyonrails.org/routing.html#controller-namespaces-and-routing)

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // profile management
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // api access
    Route::get('settings/keys', [ApiAccessController::class, 'index'])->name('settings.api.index');
    Route::get('settings/keys/new', [ApiAccessController::class, 'new'])->name('settings.api.new');
    Route::post('settings/keys', [ApiAccessController::class, 'create'])->name('settings.api.create');
    Route::delete('settings/keys/{id}', [ApiAccessController::class, 'destroy'])->name('settings.api.destroy');
});

require __DIR__ . '/auth.php';
