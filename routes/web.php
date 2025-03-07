<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Master\MasterController;
use App\Http\Controllers\Master\UserDataController;
use App\Http\Controllers\Master\RoleDataController;
use App\Http\Controllers\Master\PermissionDataController;
use App\Http\Controllers\Master\RolePermissionController;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth', 'verified', \Inertia\EncryptHistoryMiddleware::class])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('master-data')->middleware('permission:master-data')->group(function () {
        Route::get('/', MasterController::class)->name('master-data');
        Route::resources([
            'users' => UserDataController::class,
            'roles' => RoleDataController::class,
            'permissions' => PermissionDataController::class,
        ]);
        Route::controller(RolePermissionController::class)->group(function () {
            Route::get('roles/{role}/setting', 'index')->name('role.setting');
            Route::post('roles/{role}/setting/{permission}', 'store')->name('role.setting.update');
        });
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
