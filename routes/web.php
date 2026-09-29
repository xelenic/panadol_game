<?php

use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', GameController::class)->name('game');
Route::get('/game/settings', GameSettingController::class)->name('game.settings');

Route::redirect('/admin', '/admin/settings/edit');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [SessionController::class, 'create'])->name('login');
        Route::post('login', [SessionController::class, 'store'])->middleware('throttle:login')->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::singleton('settings', SettingController::class)->destroyable()->except('show');
        Route::post('logout', [SessionController::class, 'destroy'])->name('logout');
    });
});
