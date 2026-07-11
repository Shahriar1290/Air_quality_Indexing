<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/history', [DashboardController::class, 'history'])->name('history');
Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
Route::get('/reports', [ReportController::class, 'index'])->name('reports');
Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

// AJAX API Routes for live updates
Route::prefix('ajax')->name('ajax.')->group(function () {
    Route::get('/live', [DashboardController::class, 'apiLive'])->name('live');
    Route::get('/chart', [DashboardController::class, 'apiChart'])->name('chart');
    Route::get('/history', [DashboardController::class, 'apiHistory'])->name('history');
});
