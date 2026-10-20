<?php

use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Admin\Cms\SeoController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/role-portal-common.php';

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('seo-settings', [SeoController::class, 'index'])->name('seo.index')->middleware('can:Manage Cms Settings');
Route::put('seo-settings', [SeoController::class, 'update'])->name('seo.update')->middleware('can:Manage Cms Settings');
