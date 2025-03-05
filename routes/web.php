<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Master\MasterController;
use App\Http\Controllers\Master\UserDataController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('master-data')->group(function () {
        Route::get('/', MasterController::class)->name('master-data');
        Route::resources([
            'users' => UserDataController::class,
        ]);
    });
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
